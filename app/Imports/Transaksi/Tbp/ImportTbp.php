<?php

namespace App\Imports\Transaksi\Tbp;

use App\Lib\Notification;
use App\Models\Kegiatan;
use App\Models\Skpd;
use App\Models\SkpdBendahara;
use App\Models\SourceRekening;
use App\Models\Tbpheader;
use App\Models\Tbprincian;
use App\Models\TempUploadTbp;
use App\Models\User;
use App\Repositories\Impl\SKPDImplement;
use App\Repositories\Impl\TbpHeaderImplement;
use App\Repositories\Impl\TbpRincianImplement;
use App\Repositories\Impl\UserImplement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportTbp implements ToCollection
{
    protected $rincian;
    protected $errors;
    protected $successCount;
    protected $errorCount;
    protected $errorsData=[];
    protected $successData=[];
    protected $messageErrors=[];


    /**
    * @param Collection $collection
    */
    public function collection(Collection $row)
    {
        $heading = [
            'NAMA SKPD',
            'USERNAME',
            'CARA BAYAR',
            'TANGGAL PENERIMAAN',
            'NAMA LENGKAP',
            'ALAMAT',
            'URAIAN',
            'KODE KEGIATAN',
            'NAMA KEGIATAN',
            'KODE REKENING',
            'NAMA REKENING',
            'NOMINAL REKENING',
        ];

        if ($heading != $row[0]->toArray()) {
            $tempError = [
                'key'   => "error",
                'message' => "Records not same format, check the file header or download the sample file"
            ];
            array_push($this->messageErrors,$tempError );
            $this->data = [
                'countExcel'        => 0,
                'countSuccess'      => 0,
                'countError'        => 1,
                'errorData'         => $this->errorsData,
                'successData'       => $this->successData,
                'messageError'      => $this->messageErrors
            ];
            return back()->withErrors('Error,  Records not same format, check the file header or download the sample file')->with($this->data);
        }

        if (count($row) == 1) {
            $tempError = [
                'key'   => "error",
                'message' => "Records is empty"
            ];
            array_push($this->messageErrors,$tempError );
            $this->data = [
                'countExcel'        => 0,
                'countSuccess'      => 0,
                'countError'        => 1,
                'errorData'         => $this->errorsData,
                'successData'       => $this->successData,
                'messageError'      => $this->messageErrors
            ];
            return back()->withErrors('Error, Records is empty')->with($this->data);
        }

        unset($row[0]);
        set_time_limit(0);

        $this->errors = "";
        $this->success=0;
        $this->errorCount=0;
        $baris = 0;
        DB::beginTransaction();
        try {
            foreach ($row as $key => $value) {
                $data = [
                    'nama_skpd'         => $value[0],
                    'email'             => $value[1],
                    'cara_bayar'        => $value[2],
                    'tgl_penerimaan'    => $value[3],
                    'nama_lengkap'      => $value[4],
                    'alamat'            => $value[5],
                    'uraian'            => $value[6],
                    'kode_kegiatan'     => $value[7],
                    'nama_kegiatan'     => $value[8],
                    'kode_rekening'     => $value[9],
                    'nama_rekening'     => $value[10],
                    'nominal'           => $value[11],
                    'process'           => 0,
                    'created_by'        => auth()->user()->id
                ];
                TempUploadTbp::create($data);
                $this->rincian++;
            }
        } catch (\Throwable $th) {
            Notification::sendException($th);
        }

        $this->getHeader();

        $this->data = [
            'countExcel'            => $this->rincian,
            'countSuccess'          => $this->successCount,
            'countError'            => $this->errorCount,
            'errorData'             => $this->errorsData,
            'successData'           => $this->successData,
            'messageError'          => $this->messageErrors,
        ];
        return back()->withStatus('Success imported order details')->with($this->data);
    }

    private function getHeader() {
        $tbpRepo = new TbpHeaderImplement(new Tbpheader());
        $tbpRincianRepo = new TbpRincianImplement(new Tbprincian());
        $skpdRepo = new SKPDImplement(new Skpd());
        $userRepo = new UserImplement(new User());
        try {
            $headers = TempUploadTbp::where('created_by',auth()->user()->id)->where('process',0)
                ->select('nama_skpd','tgl_penerimaan', 'uraian','nama_lengkap','cara_bayar','email','kode_kegiatan','nama_kegiatan','kode_rekening','nama_rekening','nominal')
                ->groupBy(['nama_skpd','tgl_penerimaan', 'uraian','nama_lengkap','cara_bayar','created_by','process','email','kode_kegiatan','nama_kegiatan','kode_rekening','nama_rekening','nominal'])
                ->get();
            foreach ($headers as $key => $value) {
                $skpd = $skpdRepo->where('nama_skpd',$value->nama_skpd,'first');
                $user = $userRepo->where('username',$value->email,'first');
                $akseSkpd = SkpdBendahara::whereIdSkpd($skpd->id)->whereIdBendahara($user->id)->first();
                if($akseSkpd==null) {
                    $this->errorCount++;
                    continue;
                }
                // if(auth()->user()->id != $user->id) {
                //     $this->errorCount++;
                //     continue;
                // }
                $code = doGenerateKode($skpd->id,1);
                $data = [
                    'tbp_kode' => $code,
                    'tbp_tgl' => $value->tgl_penerimaan,
                    'tbp_jns' => $skpd->jenis_skpd,
                    'id_skpd' => $skpd->id,
                    'uraian' => $value->uraian,
                    'atas_nama' => $value->nama_lengkap,
                    'alamat' => $value->alamat,
                    'id_bendahara' =>$akseSkpd->id,
                    'tahun' => '2023',
                    'jns_pembayaran'=> @$value->cara_bayar == "Tunai" ? 0 : 1,
                    'actived'=>1,
                    'thn_ketetapan'=>2023,
                    'status_cetak'=>0,
                ];
                $tbp = $tbpRepo->create($data);

                $kegiatan = Kegiatan::whereSubkegiatanKode("$value->kode_kegiatan")->where("skpd_kode",$skpd->id)->firstOrFail();
                $subrekening = SourceRekening::whereKodeRekening("$value->kode_rekening")->firstOrFail();
                $rekening = SourceRekening::where('kode_rekening',substr($value->kode_rekening,0,12))->firstOrFail();

                $data = [
                    'header_id'     => $tbp->id,
                    'skpd_id'       => $tbp->id_skpd,
                    'no_urut'       => getMaxNourut($tbp->id,$tbp->id_skpd,1),
                    'kegiatan_id'   => $kegiatan->id,
                    'rekening_id'   => $rekening->id,
                    'subrekening_id'=> $subrekening->id,
                    'skode_kegiatan'=> $kegiatan->kegiatan_skode,
                    'nominal'       => $value->nominal,
                ];
                $tbpRincianRepo->create($data);


                $total = $tbpRincianRepo->getSubTotal($tbp->id,$tbp->id_skpd);
                $tbpRepo->update(['total_pembayaran'=> $total],$tbp->id);
                $this->successCount++;
                DB::commit();
                $query = 'update temp_upload_tbp set process=1 where created_by='.auth()->user()->id.' and nama_lengkap ="'.$value->nama_lengkap.'" and process=0';
                DB::update($query);
            }
        } catch (\Throwable $th) {
            Notification::sendException($th);
            $this->errorCount++;
            DB::rollBack();
        }
    }
}
