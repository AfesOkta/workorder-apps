<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\Notification;
use App\Listeners\LogUserActivity;
use App\Listeners\UsersLoginSession;
use App\Models\Bkupenerimaan;
use App\Models\BludSkpd;
use App\Models\Configs;
use App\Models\ResponseMutasi;
use App\Models\Skpd;
use App\Models\SkpdBendahara;
use App\Models\SourceRekening;
use App\Models\Stsheader;
use App\Models\Stsrincian;
use App\Models\User;
use App\Repositories\Impl\BkuImplement;
use BankJatim\MutasiRekening;
use BankJatim\VirtualAccount;
use Carbon\Carbon;
use DateTime;
use EAccountingApiSts\Sts;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class SynchronizeController extends Controller
{
    protected $setting;
    use AuthenticatesUsers;

    public function __construct(Configs $setting)
    {
        $this->setting = $setting;
    }

    public function getMutasiRekening($rekeningBank, $skpd)
    {
        ini_set("memory_limit","-1");
        try {
            $date1 = date("Ymd");
            $setting = Configs::findOrFail(1);
            $response = MutasiRekening::new()
                ->setRekening($rekeningBank)
                ->setTanggalAwal(new DateTime($date1))
                ->setTanggalAkhir(new Datetime($date1))
                ->post('http://'.$setting->url_sync_bjtm.'/wsjatim/inquiry.php?mutasi', [
                    \GuzzleHttp\RequestOptions::AUTH => [$setting->user_auth, $setting->pass_auth],
                    \GuzzleHttp\RequestOptions::VERIFY => false,
                    \GuzzleHttp\RequestOptions::PROXY => 'http://'.$setting->url_proxy_sync_bjtm.':'.$setting->port_proxy_sync_bjtm]);

            if ($response->responseCode == '00') {
                ResponseMutasi::truncate();
                foreach ($response->data as $key => $value) {
                    if ($response->data[$key]->kredit != 0) {
                        DB::beginTransaction();
                        $data = [
                            "responseCode" => $response->responseCode,
                            "responseDescription" => $response->responseDescription,
                            "tanggalPosting" => $response->data[$key]->tanggalPosting,
                            "tanggalEfektif" => $response->data[$key]->tanggalEfektif,
                            "kodeTransaksi" => $response->data[$key]->kodeTransaksi,
                            "keterangan" => $response->data[$key]->keterangan,
                            "debet" => $response->data[$key]->debet,
                            "kredit" => $response->data[$key]->kredit,
                            "rekeningPenerima" => $response->data[$key]->rekeningPenerima,
                            "rekeningPengirim" => $rekeningBank,
                            "saldo" => $response->data[$key]->saldo,
                            "noReferensi" => $response->data[$key]->noReferensi,
                            "skpd_kode" => $skpd,
                            "history" => json_encode($response->data),
                        ];

                        $existsMutasi = ResponseMutasi::query()
                                        ->where("kodeTransaksi", $response->data[$key]->kodeTransaksi)
                                        ->where("tanggalPosting", $response->data[$key]->tanggalPosting)
                                        ->where("skpd_kode", $skpd)
                                        ->where("rekeningPengirim", $rekeningBank)
                                        ->where('keterangan', $response->data[$key]->keterangan)
                                        ->first();

                        if ($existsMutasi == null) {
                            ResponseMutasi::query()->insert($data);
                        }
                        DB::commit();
                    }

                    if ($response->data[$key]->debet != 0) {
                        DB::beginTransaction();
                        $data = [
                            "responseCode" => $response->responseCode,
                            "responseDescription" => $response->responseDescription,
                            "tanggalPosting" => $response->data[$key]->tanggalPosting,
                            "tanggalEfektif" => $response->data[$key]->tanggalEfektif,
                            "kodeTransaksi" => $response->data[$key]->kodeTransaksi,
                            "keterangan" => $response->data[$key]->keterangan,
                            "debet" => $response->data[$key]->debet,
                            "kredit" => $response->data[$key]->kredit,
                            "rekeningPenerima" => $response->data[$key]->rekeningPenerima,
                            "rekeningPengirim" => $rekeningBank,
                            "saldo" => $response->data[$key]->saldo,
                            "noReferensi" => $response->data[$key]->noReferensi,
                            "skpd_kode" => $skpd,
                            "history" => json_encode($response->data),
                        ];

                        $existsMutasi = ResponseMutasi::query()
                                        ->where("kodeTransaksi", $response->data[$key]->kodeTransaksi)
                                        ->where("tanggalPosting", $response->data[$key]->tanggalPosting)
                                        ->where("skpd_kode", $skpd)
                                        ->where("rekeningPengirim", $rekeningBank)
                                        ->where('keterangan', $response->data[$key]->keterangan)
                                        ->first();

                        if ($existsMutasi == null) {
                           ResponseMutasi::query()->insert($data);
                        }
                        DB::commit();
                    }

                Notification::sendMessageMutasi("getMutasi today ".date('Y-m-d H:i:s')." with data ". json_encode($data));

                }
            }
        } catch (Exception $exception) {
            Notification::sendException($exception);
            DB::rollBack();
        }
        ini_set("memory_limit","128M");
    }

    public function getVirtualAccount(Stsheader $sts)
    {
        ini_set("memory_limit","2048M");
        set_time_limit(0);
        try {
            $setting = Configs::findOrFail(1);
            $va =$setting->identity_bjtm_va.$setting->identity_kasda_va.$sts->id_skpd.date('y').$sts->sts_kode;
            $response = VirtualAccount::new()
                ->setVirtualAccount($va)
                ->setNama(substr($sts->skpd->nama_skpd,0,50))
                ->setTanggalExp(new DateTime('now'))
                ->setFlagProses(VirtualAccount::CREATE)
                ->setTotalTagihan(ceil(sprintf($sts->total)))
                ->setBerita($sts->uraian == null ? "Ujicoba get VA" : $sts->uraian)
                ->post($setting->url_sync_va_bjtm, [
                    \GuzzleHttp\RequestOptions::AUTH => [$setting->user_auth, $setting->pass_auth],
                    \GuzzleHttp\RequestOptions::PROXY => 'http://'.$setting->url_proxy_sync_bjtm.':'.$setting->port_proxy_sync_bjtm
                ]);
            Notification::sendMessageSts($response);
            if ($response->virtualAccount != null) {
                $va =  $response->virtualAccount;
                Notification::sendMessageSts("GET VA ".$response->virtualAccount." untuk STS Header dengan SKPD ".$sts->kd_skpd." dan STS ID = ".$sts->sts_kode." pada tanggal ".Carbon::now()." get Va oleh user :". Auth::user()->id);

            }else {
                $va = "";
                Notification::sendMessageSts("Tidak bisa mendapatkan untuk STS Header dengan SKPD ".$sts->kd_skpd." dan STS ID = ".$sts->sts_kode." pada tanggal ".Carbon::now()." get Va oleh user :". Auth::user()->id);
            }
        }catch (\Exception $exception) {
            $va = "";
            Notification::sendMessage("GET VA Error ".$exception->getMessage()." untuk STS Header dengan SKPD ".$sts->kd_skpd." dan STS ID = ".$sts->sts_kode." pada tanggal ".Carbon::now()." get Va oleh user :". Auth::user()->id);
        }
        ini_set("memory_limit","128M");
        return $va;

    }

    public function doPostingVA(Request $request)
    {
        try {
            $bodyContent = $request::all();
            $va = $bodyContent['VirtualAccount'];
            $amount = $bodyContent['Amount'];
            $tgl_va = $bodyContent['Tanggal'];
            $reference = $bodyContent['Reference'];

            $existsVa = Stsheader::where('virtual_account','=',$va)->first();
            if ($existsVa) {
                DB::beginTransaction();
                $update =Stsheader::where('virtual_account','=',$va)->update(['lunas' => 1]);
                if ($update) {
                    Notification::sendMessageSts("GET POST VA ".$va." untuk STS Header dengan SKPD ".$existsVa->skpd_kode." dan STS ID = ".$existsVa->sts_kode." pada tanggal ".Carbon::now()." Get POST VA STS Header by System");
                    $data = [
                        "VirtualAccount" => $va,
                        "Amount" => $amount,
                        "Tanggal" => $tgl_va,
                        "Status" => [
                            "IsError" =>"False",
                            "ResponseCode" => "00",
                            "ErrorDesc" => "Success"
                        ]
                    ];
                    Notification::sendMessageSts("Pembayaran telah diterima");
                    $error = false;
                } else{
                    $error = true;
                }
                DB::commit();
                if ($error) {
                    return response()->json('Kode VA tidak ditemukan', 400);
                }else{
                    return response()->json($data, 200);
                }
            }else{
                return response()->json('Kode VA tidak ditemukan', 400);
            }
        }catch(\Exception $e) {
            Notification::sendMessageSts("GET POST VA Error ".$e->getMessage()." untuk nomer virtual account ".$va." pada tanggal ".Carbon::now()." Get POST VA STS Header by System");
            DB::rollBack();
            return response()->json($e->getMessage(), 400);
        }
    }

    public function getVirtualAccountApprove($stskode, $skpd)
    {
        ini_set("memory_limit","2048M");
        set_time_limit(0);
        try {
            DB::beginTransaction();
            $sts_kode       = $stskode;
            $kd_skpd         = $skpd;
            $skpd = Skpd::where('kd_skpd',$kd_skpd)->first();
            $parent = Skpd::where('parent',$skpd->parent)->firts();
            $sts = Stsheader::query()->where('sts_kode','=',"$sts_kode")
                ->where('skpd_kode',"$kd_skpd")->where('parent_id',''.$parent->id.'')->first();

            if ($sts != null) {
                $setting = Configs::findOrFail(1);
                $va =$setting->identity_bjtm_va.$setting->identity_kasda_va.$skpd->id.date('y').$sts->sts_kode;
                $response = VirtualAccount::new()
                    ->setVirtualAccount($va)
                    ->setNama(substr($skpd->nama_skpd,0,50))
                    ->setTanggalExp(new DateTime('now'))
                    ->setFlagProses(VirtualAccount::CREATE)
                    ->setTotalTagihan(ceil(sprintf($sts->total)))
                    ->setBerita($sts->uraian == null ? "Ujicoba get VA" : $sts->uraian)
                    ->post($setting->url_sync_va_bjtm, [
                        \GuzzleHttp\RequestOptions::AUTH => [$setting->user_auth, $setting->pass_auth],
                        \GuzzleHttp\RequestOptions::PROXY => 'http://'.$setting->url_proxy_sync_bjtm.':'.$setting->port_proxy_sync_bjtm
                    ]);
                Notification::sendMessage($response);
                if ($response->virtualAccount != null) {
                    $update = Stsheader::query()
                        ->where('id','=',$sts->id)->where('skpd_kode',"$skpd->kd_skpd")
                        ->update(['va_bjtm' => $response->virtualAccount]);
                    if ($update) {
                        Notification::sendMessage("GET VA ".$response->virtualAccount." untuk STS Header dengan SKPD ".$kd_skpd." dan STS ID = ".$sts_kode." pada tanggal ".Carbon::now()." get Va oleh user :". Auth::user()->id);
                        $data = [
                            'message' => 'Berhasil get Nomer Virtual Account',
                            'status'    => TRUE,
                        ];
                    }else{
                        Notification::sendMessage("Tidak bisa mendapatkan untuk STS Header dengan SKPD ".$kd_skpd." dan STS ID = ".$sts_kode." pada tanggal ".Carbon::now()." get Va oleh user :". Auth::user()->id);
                        $data = [
                            'message' => 'Tidak berhasil update Nomer Virual Account',
                            'status'    => FALSE,
                        ];
                    }

                }else {
                    Notification::sendMessage("Tidak bisa mendapatkan untuk STS Header dengan SKPD ".$kd_skpd." dan STS ID = ".$sts_kode." pada tanggal ".Carbon::now()." get Va oleh user :". Auth::user()->id);
                    $data = [
                        'message' => 'Tidak berhasil mendapatkan Nomer Virual Account',
                        'status'    => FALSE,
                    ];
                }
            }else{
                Notification::sendMessage("Tidak bisa mendapatkan untuk STS Header dengan SKPD ".$kd_skpd." dan STS ID = ".$sts_kode." pada tanggal ".Carbon::now()." get Va oleh user :". Auth::user()->id);
                $data = [
                    'message' => 'Tidak berhasil mendapatkan Nomer Virual Account',
                    'status'    => FALSE,
                ];
            }
            DB::commit();
        }catch (\Exception $exception) {
            DB::rollBack();
            $data = [
                'message' => 'Tidak berhasil mendapatkan Nomer Virual Account',
                'status'    => FALSE,
            ];
            Notification::sendMessage("GET VA Error ".$exception->getMessage()." untuk STS Header dengan SKPD ".$kd_skpd." dan STS ID = ".$sts_kode." pada tanggal ".Carbon::now()." get Va oleh user :". Auth::user()->id);
        }
        ini_set("memory_limit","128M");
        return json_encode($data);

    }

    public function doGetSynchronizeWithSapa($tgl_sts)
    {
        try {
        $stsHeader = Stsheader::withAllRelations()
        ->where("actived",1)->where("lunas",1)->where("sts_tgl",$tgl_sts)
        ->where("sapa",0)
        ->where('approved','>',0)
        ->whereHas("skpd", function($query){
            $query->where("jenis_skpd","<", 3);
        })
        //->groupBy("sts_kode","no_sts_pengantar","sts_tgl","id_skpd","uraian","cr_bayar","total","skpd_bend_id","id","skpd_kode")
        ->orderBy("id_skpd","asc")->get();
        if ($stsHeader == null) {
            return response()->json(array('status' => 'Data tidak ditemukan !'));
        } else {
            $ss = array();
            foreach ($stsHeader as $row) {
               $filteredStsrincian= Stsrincian::withAllRelations()
                ->where("id_hdr",$row->id)
                ->where("id_skpd",$row->id_skpd)
                //->groupBy("id","subkegiatan_kode","subrekening_kode","subkegiatan_id","sub_total")
                ->get();
                $detail = array();
                foreach ($filteredStsrincian as $row_child) {
                    $data = [
                        "kode_kegiatan" => $row_child->kegiatan->subkegiatan_kode,
                        "subkegiatan_id"=> $row_child->kegiatan->kegiatan_skode,
                        "kode_rekening" => $row_child->source_rekening->kode_rekening,
                        "sub_total"     => $row_child->sub_total
                    ];
                    array_push($detail, $data);
                }
                $sts = [
                    "sts_kode"          => $row->sts_kode,
                    "nomer_sts"         => $row->no_sts_pengantar,
                    "sts_tgl"           => date('Y-m-d',strtotime($row->sts_tgl)),
                    "nama_skpd"         => $row->skpd->nama_skpd,
                    "kode_skpd"         => $row->skpd->kd_skpd,
                    "kode_sipd"         => $row->skpd->kd_sipd,
                    "uraian"            => $row->uraian,
                    "tipe_pembayaran"   => $row->cr_bayar,
                    "total"             => $row->total,
                    "nip_penyetor"      => $row->skpd_bendahara->user->nip,
                    "name"              => $row->skpd_bendahara->user->name
                ];
                array_push($ss, ['master_sts' => $sts, 'rincian_sts' => $detail]);

            }

            Notification::sendMessageSts("Synchronize STS Header to SAPA pada tanggal ".Carbon::now()." dengan data ".json_encode($ss)." Synchronize STS to SAPA by System");
            return response()->json($ss);
        }
    }catch(Exception $ex){
        Notification::sendException($ex);
        return response()->json($ex->getMessage(),401);
    }}

    public function doUpdatedSynchronizeFromSapa($sts_kd, $skpd_kode, $sapa_kode)
    {
        try {
            $skpd_kode = str_replace(".", " ", $skpd_kode);
            if ($sapa_kode == "") {
                return response()->json(array('status' => 'can\'t be null.'));
            }

            if (!ctype_digit($sts_kd)) {
                return response()->json(array('status' => 'Contains non-numbers.'));
            } else {
                $data = strlen($sts_kd);

                if ($data != 6) {
                    return response()->json(array('status' => 'not in formal sts number.'));
                } else {
                    $skpd = Skpd::where('kd_skpd',"$skpd_kode")->first();
                    try {
                        DB::beginTransaction();
                        $sts = Stsheader::withAllRelations()
                            ->where("id_skpd",$skpd->id)->where("sts_kode","$sts_kd")
                            ->first();
                        $sts->sapa = 1;
                        $sts->tgl_sync_sapa = date("Y-m-d");
                        $sts->no_sts = $sapa_kode;
                        $sts->save();
                        DB::commit();
                        Notification::sendMessageSts("STS pengantar dengan nomer ".$sts_kd." berhasil di update untuk OPD ".$skpd->nama_skpd );
                        return response()->json("STS pengantar dengan nomer ".$sts_kd." berhasil di update untuk OPD ".$skpd->nama_skpd );
                    } catch (\Throwable $th) {
                        Notification::sendMessageSts("STS pengantar dengan nomer ".$sts_kd." tidak berhasil di update untuk OPD ".$skpd->nama_skpd." dengan error ".$th->getMessage() );
                        DB::rollBack();
                        return response()->json("STS pengantar dengan nomer ".$sts_kd." tidak berhasil di update untuk OPD ".$skpd->nama_skpd );
                    }
                }
            }
        }catch(\Exception $ex) {
            $ex->getMessage();
            return response()->json($ex->getMessage());
        }

    }
    /**
     * doSendPendapatanToEpayment
     *
     * @param  mixed $request
     * @return void
     */
    public function doSendPendapatanToEpayment(Request $request)
    {
        $AUTH_USER = 'eRevenue';
        $AUTH_PASS = 'AlHaMdUlIlLaH';

        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $is_not_authenticated = (
            !$has_supplied_credentials ||
            $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
            $_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
        );

        if ($is_not_authenticated) {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            exit();
        }

        $bulan = checkMonthLabel($request->bulan);

        $skpd = SKPD::where("kd_skpd",''.$request->opd_kode.'')->first();
        $data = doCalculationBku($request->tahun, $bulan, $skpd->id);
        return response()->json([
                        "status" => true,
                        "message" => "Data Pendapatan E-Revenue BLUD ".$skpd->nama_skpd." - ".$request->opd_kode,
                        "rincian" => [
                            "rekening" => "4.1.04.16.01.0001",
                            "total_bulan_lalu" => $data['saldo_bln_lalu'],
                            "total_bulan_ini" => $data['saldo_bln_ini'],
                        ]
                    ],200,[],JSON_UNESCAPED_SLASHES);
    }

    /**
     * doSendPendapatanToEpayment
     *
     * @param  mixed $request
     * @return void
     */
    public function doReceiveSP3BFromEpayment(Request $request)
    {
        $AUTH_USER = 'eRevenue';
        $AUTH_PASS = 'AlHaMdUlIlLaH';

        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $is_not_authenticated = (
            !$has_supplied_credentials ||
            $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
            $_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
        );

        if ($is_not_authenticated) {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            exit();
        }
        $bulan = checkMonthLabel($request->bulan);

        $skpd = SKPD::where("kd_skpd",''.$request->opd_kode.'')->first();
        $data = doCalculationBku($request->tahun, $bulan, $skpd->id);

        Bkupenerimaan::where('tahun', $request->tahun)->where(DB::raw('month(bukti_tgl)'),'=',$bulan)->where('actived',1)
        ->update(['epayment_sp2d'=>$request->sp3b, 'tgl_synch_sp3b'=>now()]);
        return response()->json([
                        "status" => true,
                        "message" => "Data Pendapatan E-Revenue BLUD ".$skpd->nama_skpd." - ".$request->opd_kode,
                        "rincian" => [
                            "rekening" => "4.1.04.16.02.0001",
                            "total_bulan_lalu" => $data['saldo_bln_lalu'],
                            "total_bulan_ini" => $data['saldo_bln_ini'],
                        ]
                    ],200,[],JSON_UNESCAPED_SLASHES);
    }

    public function doRealisasiApi(Request $request)
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        $AUTH_USER = 'eRevenue';
        $AUTH_PASS = 'AlHaMdUlIlLaH';

        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $is_not_authenticated = (
            !$has_supplied_credentials ||
            $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
            $_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
        );

        if ($is_not_authenticated) {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            exit();
        }
        $date = $request->date ?: date('Y-m-d');
        $sts = getSynchronizeStsByDate($date);

        $result = [];
        foreach ($sts as $st) {
            $result[] = [
                "ID" => $st->id,
                "NO_BUKTI" => $st->no_sts,
                "TG_BUKTI" => $st->sts_tgl,
                "KD_RINCI" => $st->kode_rekening,
                "KD_KEGIATAN" => $st->subkegiatan_kode,
                "ID_SUBKEGIATAN" => (int)$st->kegiatan_skode,
                "SUBKEGIATAN_NAMA" => $st->kegiatan_skode,
                "KD_ORGANISASI" => $st->parent_skpd,
                "KD_SUBORGANISASI" => $st->opd_kode,
                "KODE" => "K",
                "NO_DOKUMEN" => "",
                "URAIAN" => $st->uraian,
                "KREDIT" => $st->sub_total,
                "DEBET" => "0",
                "TAHUN" => (int)substr($st->sts_tgl,0,4),
                "ACTIVED" => $st->actived,
                "STATUS"   => 3 // tambahan untuk status 3 from sts
            ];
        }

        $bku = getSynchronizeBkuManualByDate($date);

        foreach ($bku as $st) {
            $result[] = [
                "ID" => $st->bku_no,
                "NO_BUKTI" => $st->bukti_no,
                "TG_BUKTI" => $st->bukti_tgl,
                "KD_RINCI" => $st->rekening_kode,
                "KD_KEGIATAN" => $st->kegiatan_kode,
                "ID_SUBKEGIATAN" => (int)$st->skegiatan_kode,
                "SUBKEGIATAN_NAMA" => $st->subkegiatan_nama,
                "KD_ORGANISASI" => $st->parent_kode,
                "KD_SUBORGANISASI" => $st->opd_kode,
                "KODE" => "K",
                "NO_DOKUMEN" => "",
                "URAIAN" => $st->uraian,
                "KREDIT" => $st->total,
                "DEBET" => "0",
                "TAHUN" => $st->tahun,
                "ACTIVED" => $st->actived,
                "STATUS"   => 2 // tambahan untuk status 1 = bku manual
            ];
        }

        $bkuTertunda = getSynchronizeBkuTertundaByDate($date);
        foreach ($bkuTertunda as $st) {
            $result[] = [
                "ID" => $st->bku_no,
                "NO_BUKTI" => $st->bukti_no,
                "TG_BUKTI" => $st->bukti_tgl,
                "KD_RINCI" => $st->rekening_kode,
                "KD_KEGIATAN" => $st->kegiatan_kode,
                "ID_SUBKEGIATAN" => (int)$st->skegiatan_kode,
                "SUBKEGIATAN_NAMA" => $st->subkegiatan_nama,
                "KD_ORGANISASI" => $st->parent_kode,
                "KD_SUBORGANISASI" => $st->opd_kode,
                "KODE" => "K",
                "NO_DOKUMEN" => "",
                "URAIAN" => $st->uraian,
                "KREDIT" => $st->total,
                "DEBET" => "0",
                "TAHUN" => $st->tahun,
                "ACTIVED" => $st->actived,
                "STATUS"   => 1 // tambahan untuk status 1 = bku tertunda
            ];
        }

        return response()->json([
            "status" => true,
            "result" => $result,
            "message" => "Data Realisasi E-Revenue"
        ],200,[],JSON_UNESCAPED_SLASHES);
    }

    function doPostBatchSynchronizeBkuToAkt(Request $request) {
        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', '0');
            $bkuRepo = new BkuImplement(new Bkupenerimaan());
            $allBkus = doGetBkuWithJurnalNotSuccess($request)->get();
            $setting = Configs::find(1);
            foreach ($allBkus as $value) {
                $inirekening = [];
                DB::beginTransaction();
                $skpd = Skpd::find($value->id_skpd);

                $bkuTbps = $bkuRepo->getBkuForJenisTbp(date('Y-m-d', strtotime($value->bukti_tgl)),$value->id_skpd, $value->bukti_no);
                $existsBlud = BludSkpd::whereKdSkpd($skpd->kd_skpd)->first();
                if($existsBlud == null) {
                    $allRincians = doGetRincianBkuWithJurnalNotSuccess($skpd->id,$value->bukti_no)->get();
                    foreach ($allRincians as $item) {
                        $_rekening = SourceRekening::find($item->rekening_id);
                        $rekening = ['kode'  => $_rekening->kode_rekening,'nominal'=> $item->total];
                        array_push($inirekening, $rekening);
                    }
                    Notification::sendMessageBku("OPD ".$skpd->kd_skpd." akan Synchronize BKU STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".Carbon::now()." Oleh user :By System dengan rincian rekening ". json_encode($inirekening));
                    try {
                        /**
                         * @since 06-09-2021
                         * @see update user blud transaction bku dishub
                         * @author Afes Oktavianus
                         */
                        if ($skpd->jenis_skpd == 4)  {
                            $source = 'STS_BUD';
                        }else{
                            $source = 'TBP';
                        }

                        try {
                            $sendSts = Sts::new()
                            ->setTahun($value->tahun)
                            ->setOpdKode(@$skpd->kd_skpd=="1 07 0101"?$skpd->parent:$skpd->kd_skpd)
                            ->setStsKode($value->bukti_no)
                            ->setStsJenis(0)
                            ->setStsTgl(date("Y-m-d",strtotime($value->bukti_tgl)))
                            ->setUraian($value->uraian)
                            ->setReferensiKode('')
                            ->setIsian('')
                            ->setSource($source)
                            ->setKegiatanKode($value->kegiatan_kode)
                            ->setIdBapeko($value->skegiatan_kode)
                            ->setRekening(
                                $inirekening
                            )
                            ->setPotongan([])
                            ->post($setting->url_sync_api_ecounting,[]);
                        }catch(\Exception $ex){
                            Notification::sendException($ex);
                            break;
                        }

                        $response = $sendSts->getResponse()['status'];
                        if ($response == "0" || $response == false) {
                            Notification::sendMessageBku(json_encode($sendSts->getResponse()));
                            if($sendSts->getResponse()['message'] == "Failed Added STS Data or duplicate Data,  please try again ") {
                                //Untuk tbp
                                foreach ($bkuTbps as $bkuTbp) {
                                    $bkuRepo->update(['status_jurnal'=>2, 'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $bkuTbp->id);
                                }
                                $bkuSts = Bkupenerimaan::where('bukti_no',$value->bukti_no)->where("id_skpd",$value->id_skpd)->where("bku_jenis",1)->get();
                                foreach ($bkuSts as $key => $value) {
                                    $bkuRepo->update(['status_jurnal'=>2,'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $value->id);
                                }
                                Notification::sendMessageBku("OPD ".$skpd->kd_skpd." Berhasil Synchronize BKU STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".Carbon::now()." Oleh user :By System ");
                            }else{
                                foreach ($bkuTbps as $bkuTbp) {
                                    $bkuRepo->update(['status_jurnal'=>2, 'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $bkuTbp->id);
                                }
                                //$bkuRepo->update(['status_jurnal'=>1], $bkuTbp->id);
                                //Untuk sts
                                $bkuSts = Bkupenerimaan::where('bukti_no',$value->bukti_no)->where("id_skpd",$value->id_skpd)->where("bku_jenis",1)->get();
                                foreach ($bkuSts as $key => $value) {
                                    $bkuRepo->update(['status_jurnal'=>1,'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $value->id);
                                }
                                Notification::sendMessageBku("Tidak Berhasil Synchronize BKU STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".date('Y-m-d H:i:s')." dengan kode rekening ".$value->rekening_kode." Oleh user :By System ");
                            }
                        }else{
                        //     //Untuk tbp
                            foreach ($bkuTbps as $bkuTbp) {
                                $bkuRepo->update(['status_jurnal'=>2, 'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $bkuTbp->id);
                            }
                            //$bkuRepo->update(['status_jurnal'=>2, 'tgl_synch_akt'=>date('Y-m-d')], $bkuTbp->id);
                            //Untuk sts
                            $bkuSts = Bkupenerimaan::where('bukti_no',$value->bukti_no)->where("id_skpd",$value->id_skpd)->where("bku_jenis",1)->get();
                            foreach ($bkuSts as $key => $value) {
                                $bkuRepo->update(['status_jurnal'=>2,'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $value->id);
                            }
                            // $bkuRepo->update(['status_jurnal'=>2,'bud'=>0,'send'=>0], $value->id);
                            Notification::sendMessageBku("OPD ".$skpd->kd_skpd." Berhasil Synchronize BKU STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".Carbon::now()." Oleh user :By System ");
                        }
                    }catch(Exception $ex) {
                        Notification::sendException($ex);
                        //Untuk tbp
                        foreach ($bkuTbps as $bkuTbp) {
                            $bkuRepo->update(['status_jurnal'=>2, 'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $bkuTbp->id);
                        }
                        $bkuSts = Bkupenerimaan::where('bukti_no',$value->bukti_no)->where("id_skpd",$value->id_skpd)->where("bku_jenis",1)->get();
                        foreach ($bkuSts as $key => $value) {
                            $bkuRepo->update(['status_jurnal'=>2,'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $value->id);
                        }


                        Notification::sendMessage("OPD ".$skpd->kd_skpd." Tidak Berhasil Synchronize BKU STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".date('Y-m-d H:i:s')." dengan kode rekening ".$value->subrekening_kode." Oleh user :By System "." Dengan error: ".$ex->getMessage());
                        // $response == 0;
                    }
                }else{
                    foreach ($bkuTbps as $bkuTbp) {
                        $bkuRepo->update(['status_jurnal'=>2, 'tgl_synch_akt'=>date('Y-m-d'), 'bud'=>1,'send'=>2], $bkuTbp->id);
                    }
                    //Untuk sts
                    $bkuSts = Bkupenerimaan::where('bukti_no',$value->bukti_no)->where("id_skpd",$value->id_skpd)->where("bku_jenis",1)->get();
                    foreach ($bkuSts as $key => $value) {
                        $bkuRepo->update(['status_jurnal'=>2,'tgl_synch_akt'=>date('Y-m-d'), 'bud'=>1,'send'=>2], $value->id);
                    }

                    Notification::sendMessageBku("OPD ".$skpd->kd_skpd." Berhasil Synchronize BKU STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".Carbon::now()." Oleh user :By System ");
                }
                DB::commit();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Notification::sendException($th);
        }
    }

    function doPostBatchSynchronizeBkuToAkt2(Request $request) {
        try {
            $bkuRepo = new BkuImplement(new Bkupenerimaan());
            $allBkus = doGetBkuWithJurnalNotSuccessWithOpdId($request)->get();
            $setting = Configs::find(1);
            foreach ($allBkus as $value) {
                $inirekening = [];
                DB::beginTransaction();
                $skpd = Skpd::find($value->id_skpd);

                $bkuTbps = $bkuRepo->getBkuForJenisTbp(date('Y-m-d', strtotime($value->bukti_tgl)),$value->id_skpd, $value->bukti_no);
                $existsBlud = BludSkpd::whereKdSkpd($skpd->kd_skpd)->first();
                if($existsBlud == null) {
                    $allRincians = doGetRincianBkuWithJurnalNotSuccess2($skpd->id,$value->bukti_no)->get();
                    foreach ($allRincians as $item) {
                        $_rekening = SourceRekening::find($item->rekening_id);
                        $rekening = ['kode'  => $_rekening->kode_rekening,'nominal'=> $item->total];
                        array_push($inirekening, $rekening);
                    }
                    Notification::sendMessageBku("OPD ".$skpd->kd_skpd." akan Synchronize BKU STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".Carbon::now()." Oleh user :By System dengan rincian rekening ". $inirekening);
                    try {
                        /**
                         * @since 06-09-2021
                         * @see update user blud transaction bku dishub
                         * @author Afes Oktavianus
                         */
                        if ($skpd->jenis_skpd == 4)  {
                            $source = 'STS_BUD';
                        }else{
                            $source = 'TBP';
                        }

                        try {
                            $sendSts = Sts::new()
                            ->setTahun($value->tahun)
                            ->setOpdKode(@$skpd->kd_skpd=="1 07 0101"?$skpd->parent:$skpd->kd_skpd)
                            ->setStsKode($value->bukti_no)
                            ->setStsJenis(0)
                            ->setStsTgl(date("Y-m-d",strtotime($value->bukti_tgl)))
                            ->setUraian($value->uraian)
                            ->setReferensiKode('')
                            ->setIsian('')
                            ->setSource($source)
                            ->setKegiatanKode($value->kegiatan_kode)
                            ->setIdBapeko($value->skegiatan_kode)
                            ->setRekening(
                                $inirekening
                            )
                            ->setPotongan([])
                            ->post($setting->url_sync_api_ecounting,[]);
                        }catch(\Exception $ex){
                            Notification::sendException($ex);
                            break;
                        }

                        $response = $sendSts->getResponse()['status'];
                        if ($response == "0" || $response == false) {
                            //Notification::sendMessageBku(json_encode($sendSts->getResponse()));
                            if($sendSts->getResponse()['message'] == "Failed Added STS Data or duplicate Data,  please try again ") {
                                //Untuk tbp
                                foreach ($bkuTbps as $bkuTbp) {
                                    $bkuRepo->update(['status_jurnal'=>2, 'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $bkuTbp->id);
                                }
                                $bkuSts = Bkupenerimaan::where('bukti_no',$value->bukti_no)->where("id_skpd",$value->id_skpd)->where("bku_jenis",1)->get();
                                foreach ($bkuSts as $key => $value) {
                                    $bkuRepo->update(['status_jurnal'=>2,'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $value->id);
                                }

                                Notification::sendMessageBku("OPD ".$skpd->kd_skpd." Berhasil Synchronize BKU STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".Carbon::now()." Oleh user :By System ");
                            }else{
                                foreach ($bkuTbps as $bkuTbp) {
                                    $bkuRepo->update(['status_jurnal'=>2, 'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $bkuTbp->id);
                                }
                                //$bkuRepo->update(['status_jurnal'=>1], $bkuTbp->id);
                                //Untuk sts
                                $bkuSts = Bkupenerimaan::where('bukti_no',$value->bukti_no)->where("id_skpd",$value->id_skpd)->where("bku_jenis",1)->get();
                                foreach ($bkuSts as $key => $value) {
                                    $bkuRepo->update(['status_jurnal'=>1,'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $value->id);
                                }
                                Notification::sendMessageBku("Tidak Berhasil Synchronize BKU STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".date('Y-m-d H:i:s')." dengan kode rekening ".$value->rekening_kode." Oleh user :By System ");
                            }
                        }else{
                            //Untuk tbp
                            foreach ($bkuTbps as $bkuTbp) {
                                $bkuRepo->update(['status_jurnal'=>2, 'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $bkuTbp->id);
                            }
                            //$bkuRepo->update(['status_jurnal'=>2, 'tgl_synch_akt'=>date('Y-m-d')], $bkuTbp->id);
                            //Untuk sts
                            $bkuSts = Bkupenerimaan::where('bukti_no',$value->bukti_no)->where("id_skpd",$value->id_skpd)->where("bku_jenis",1)->get();
                            foreach ($bkuSts as $key => $value) {
                                $bkuRepo->update(['status_jurnal'=>2,'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $value->id);
                            }
                            // $bkuRepo->update(['status_jurnal'=>2,'bud'=>0,'send'=>0], $value->id);
                            Notification::sendMessageBku("OPD ".$skpd->kd_skpd." Berhasil Synchronize BKU STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".Carbon::now()." Oleh user :By System ");
                        }
                    }catch(Exception $ex) {
                        Notification::sendException($ex);
                        //Untuk tbp
                        foreach ($bkuTbps as $bkuTbp) {
                            $bkuRepo->update(['status_jurnal'=>2, 'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $bkuTbp->id);
                        }
                        $bkuSts = Bkupenerimaan::where('bukti_no',$value->bukti_no)->where("id_skpd",$value->id_skpd)->where("bku_jenis",1)->get();
                        foreach ($bkuSts as $key => $value) {
                            $bkuRepo->update(['status_jurnal'=>2,'tgl_synch_akt'=>date('Y-m-d'),'bud'=>0,'send'=>2], $value->id);
                        }


                        Notification::sendMessage("OPD ".$skpd->kd_skpd." Tidak Berhasil Synchronize BKU STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".date('Y-m-d H:i:s')." dengan kode rekening ".$value->subrekening_kode." Oleh user :By System "." Dengan error: ".$ex->getMessage());
                        $response == 0;
                    }
                }else{
                    foreach ($bkuTbps as $bkuTbp) {
                        $bkuRepo->update(['status_jurnal'=>2, 'tgl_synch_akt'=>date('Y-m-d'), 'bud'=>1,'send'=>2], $bkuTbp->id);
                    }
                    //Untuk sts
                    $bkuSts = Bkupenerimaan::where('bukti_no',$value->bukti_no)->where("id_skpd",$value->id_skpd)->where("bku_jenis",1)->get();
                    foreach ($bkuSts as $key => $value) {
                        $bkuRepo->update(['status_jurnal'=>2,'tgl_synch_akt'=>date('Y-m-d'), 'bud'=>1,'send'=>2], $value->id);
                    }

                    Notification::sendMessageBku("OPD ".$skpd->kd_skpd." Berhasil Synchronize BKU STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".Carbon::now()." Oleh user :By System ");
                }
                DB::commit();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Notification::sendException($th);
        }
    }

    public function getAutorizeToken(Request $request) {
        $clientId = "9b56b6e9-8471-4f50-a991-9e0123e63bdb";
        $redirectUri = "https://erevenue.surabaya.go.id/sso/callback";
        $urlKantorku = "https://kantorku.surabaya.go.id/oauth/authorize?";

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => '',
        ]);
        //REDIRECT KE KANTORKU
        return redirect($urlKantorku . $query);
    }

    public function callbackKantorku(Request $request){
        $clientId = "9b56b6e9-8471-4f50-a991-9e0123e63bdb";
        $secretKey = "od7asTy78wKaUS9BARt3jjrbZPgTZacfMCANXWI8";
        $url_kantorku = "https://kantorku.surabaya.go.id/oauth/token";
        $url = "https://erevenue.surabaya.go.id/sso/callback"; //

        try {
            $data =  [
                'grant_type' => 'authorization_code',
                'client_id' => $clientId,
                'client_secret' => $secretKey,
                'redirect_uri' => $url,
                'code' => $request->code
            ];

            $response = Http::asForm()->post($url_kantorku, $data);

            Notification::sendMessageKantorku("Token Kantorku: ".json_encode($response->json()));

            $request->session()->put($response->json());
            $access_token = $request->session()->get('access_token');
            Notification::sendMessageKantorku('access token :'.$access_token);

            $response = Http::withHeaders([
                "Accept"    => "application/json",
                "Authorization" => "Bearer " . $access_token
            ])->get("https://kantorku.surabaya.go.id/api/user");

            $access = $response->json();

            Notification::sendMessageKantorku("Access Kantorku: ".json_encode($access));

            if (array_key_exists('user_detail', $access)) {
                Notification::sendMessageKantorku("user_detail: ".json_encode($access['user_detail'][0]));
                # Jika berhasil Mendapat UserDetail Dari Kantorku
                # Select User From UserDetail Aplikasi tanpa password
                # Di Case ini juga bisa menambahakan logic jika role user ada lebih dari satu bisa di handle di sini
                $user = User::where('username', $access['user_detail'][0]['nik'])->first();
                if ($user ) {
                    try {
                        Notification::sendMessageKantorku($user);
                        $this->redirectTo = "index";
                        $user = Auth::guard('web')->loginUsingId($user->id);
                        $skpd_id = SkpdBendahara::where('id_bendahara', $user->id)->where('actived', 1)->firstOrFail();

                        Session::flash('direct_login', '0');
                        Session::put("skpd_id", $skpd_id->id_skpd);
                        Session::put("bendahara_id", $skpd_id->id_bendahara);
                        LogUserActivity::get()->store($user, 'Login Attempt', User::class, "Login Attempt");
                        UsersLoginSession::get()->store($user,$skpd_id);
                        return $this->sendLoginResponse($request);
                    } catch (\Throwable $th) {
                        Notification::sendMessageKantorku($th);
                        return redirect()->route('user.login')->with('error', 'User not found in our system');
                    }
                }else{
                    return redirect()->route('user.login')->with('error', 'User not found in our system');
                }
            } else {
                return redirect()->route('user.login')->with('error', $access['message']);
            }
        } catch (RequestException $e) {
            Notification::sendException($e);
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $error = json_decode($e->getResponse()->getBody()->getContents(), true);
                return response()->json(['error' => $error['error']], $statusCode);
            } else {
                return response()->json(['error' => 'Failed to connect to the server.'], 500);
            }
        }
    }

    /**
     * doSendPendapatanToEpayment
     *
     * @param  mixed $request
     * @return void
     */
    public function doSendPendapatanToEpaymentWithRekening(Request $request)
    {
        $AUTH_USER = 'eRevenue';
        $AUTH_PASS = 'AlHaMdUlIlLaH';

        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $is_not_authenticated = (
            !$has_supplied_credentials ||
            $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
            $_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
        );

        if ($is_not_authenticated) {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            exit();
        }

        $bulan = checkMonthLabel($request->bulan);

        $skpd = SKPD::where("kd_skpd",''.$request->opd_kode.'')->first();
        $data = doCalculationBkuWithRekening($request->tahun, $bulan, $skpd->id);
        return response()->json([
                        "status" => true,
                        "message" => "Data Pendapatan E-Revenue BLUD ".$skpd->nama_skpd." - ".$request->opd_kode,
                        "rincian" => $data
                    ],200,[],JSON_UNESCAPED_SLASHES);
    }

    /**
     * doSendPendapatanToEpayment
     *
     * @param  mixed $request
     * @return void
     */
    public function doReceiveSP3BFromEpaymentWithRekening(Request $request)
    {
        $AUTH_USER = 'eRevenue';
        $AUTH_PASS = 'AlHaMdUlIlLaH';

        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $is_not_authenticated = (
            !$has_supplied_credentials ||
            $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
            $_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
        );

        if ($is_not_authenticated) {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            exit();
        }
        $bulan = checkMonthLabel($request->bulan);

        $skpd = SKPD::where("kd_skpd",''.$request->opd_kode.'')->first();
        $data = doCalculationBkuWithRekening($request->tahun, $bulan, $skpd->id);
        // Notification::sendMessageMutasi(json_encode($data));
        // Bkupenerimaan::where('tahun', $request->tahun)->where(DB::raw('month(bukti_tgl)'),'=',$bulan)->where('actived',1)
        // ->update(['epayment_sp2d'=>$request->sp3b, 'tgl_synch_sp3b'=>now()]);
        $bkus =  Bkupenerimaan::where('tahun', $request->tahun)
                ->where(DB::raw('month(bukti_tgl)'),'=',$bulan)
                ->where('id_skpd',$skpd->id)
                ->where('actived',1)
        ->get();
        foreach ($bkus as $value) {
            $value->epayment_sp2d = $request->sp3b;
            $value->tgl_synch_sp3b=now();
            $value->save();
        }
        return response()->json([
                        "status" => true,
                        "message" => "Data Pendapatan E-Revenue BLUD ".$skpd->nama_skpd." - ".$request->opd_kode,
                        "rincian" => $data
                    ],200,[],JSON_UNESCAPED_SLASHES);
    }

    /**
     * doSendPendapatanToEpayment
     *
     * @param  mixed $request
     * @return void
     */
    public function doCancelSP3BFromEpaymentWithRekening(Request $request)
    {
        $AUTH_USER = 'eRevenue';
        $AUTH_PASS = 'AlHaMdUlIlLaH';

        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $is_not_authenticated = (
            !$has_supplied_credentials ||
            $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
            $_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
        );

        if ($is_not_authenticated) {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            exit();
        }
        $bulan = checkMonthLabel($request->bulan);

        $skpd = SKPD::where("kd_skpd",''.$request->opd_kode.'')->first();
       // $data = doCalculationBkuWithRekening($request->tahun, $bulan, $skpd->id);

        $bkus =  Bkupenerimaan::where('tahun', $request->tahun)
                ->where(DB::raw('month(bukti_tgl)'),'=',$bulan)
                ->where('actived',1)->where('epayment_sp2d',$request->sp3b)
                ->where('id_skpd',$skpd->id)
                ->get();
        foreach ($bkus as $value) {
            $value->epayment_sp2d = null;
            $value->tgl_synch_sp3b=null;
            $value->save();
        }
        return response()->json([
                        "status" => true,
                        "message" => "Cancel SP3B dengan nomer ".$request->sp3b." Pendapatan E-Revenue BLUD ".$skpd->nama_skpd." - ".$request->opd_kode
                    ],200,[],JSON_UNESCAPED_SLASHES);
    }
}
