<?php

namespace App\Imports\Transaksi\Bku;

use App\Lib\Notification;
use App\Models\Anggaran;
use App\Models\Bkupenerimaan;
use App\Models\BludSkpd;
use App\Models\Configs;
use App\Models\Kegiatan;
use App\Models\Skpd;
use App\Models\SkpdBendahara;
use App\Models\SourceRekening;
use App\Models\Stsrincian;
use App\Models\Tbpheader;
use App\Models\Tbprincian;
use App\Repositories\Impl\AnggaranImplement;
use App\Repositories\Impl\BkuImplement;
use App\Repositories\Impl\SKPDBendaharaImplement;
use App\Repositories\Impl\StsRincianImplement;
use App\Repositories\Impl\TbpHeaderImplement;
use App\Repositories\Impl\TbpRincianImplement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportBkuPenerimaanPPKD implements ToCollection
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
            'STS_KD',
            'STS_TGL',
            'ORG_KD',
            'ORG_NM',
            'URAIAN',
            'SUB_KODE_KEGIATAN',
            'SUB_KODE_REKENING',
            'NAMA_REKENING',
            'NILAI_STS',
            'KODE',
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
        $bkuTemp = [];
        try {
            foreach ($row as $key => $value) {
                $data = [
                    'sts_kd'            => $value[0],
                    'sts_tgl'           => $value[1],
                    'org_kd'            => $value[2],
                    'org_nama'          => $value[3],
                    'uraian'            => $value[4],
                    'sub_kode_kegiatan' => $value[5],
                    'sub_kode_rekening' => $value[6],
                    'nama_rekening'     => $value[7],
                    'nilai_sts'         => $value[8],
                    'kode'              => $value[9],
                ];
                array_push($bkuTemp,$data);
                $this->rincian++;
            }
        } catch (\Throwable $th) {
            Notification::sendException($th);
        }

        $this->getHeader($bkuTemp);

        $this->data = [
            'countExcel'            => $this->rincian,
            'countSuccess'          => $this->successCount,
            'countError'            => $this->errorCount,
            'errorData'             => $this->errorsData,
            'successData'           => $this->successData,
            'messageError'          => $this->messageErrors,
        ];
        return back()->withStatus('Berhasil Upload Bku PPKD')->with($this->data);
    }

    private function getHeader(array $bkuTemp) {
        try {
            $bkuRepo = new BkuImplement(new Bkupenerimaan());
            $i = 0;
            DB::beginTransaction();
            foreach ($bkuTemp as $value) {
                $skpd = Skpd::whereKdSkpd($value['org_kd'])->first();
                $ppkd = Skpd::whereKdSkpd("1 04 0201")->first();
                $parentPpkd = Skpd::whereKdSkpd("1 04 0200")->first();
                $bkuNo = doGenerateKode($ppkd->id,3);
                $kegiatan = Kegiatan::where("subkegiatan_kode","1.04.00.0.40.00")
                    ->where('skpd_kode',$parentPpkd->id)->first();
                $rekening = SourceRekening::whereKodeRekening($value['sub_kode_rekening'])->first();
                if ($rekening == null) {
                    break;
                }

                $bkuPenerimaan = new Bkupenerimaan();
                $bkuPenerimaan->id_parent =  $parentPpkd->id;
                $bkuPenerimaan->id_skpd=$ppkd->id;
                $bkuPenerimaan->tahun=2024;
                $bkuPenerimaan->bku_jenis=1;
                $bkuPenerimaan->bku_no=$bkuNo;
                $bkuPenerimaan->bku_tgl=date('Y-m-d');
                $bkuPenerimaan->bukti_no=$value['sts_kd'];
                $bkuPenerimaan->bukti_tgl=$value['sts_tgl'];
                $bkuPenerimaan->kegiatan_id=$kegiatan->id;
                $bkuPenerimaan->kegiatan_kode="1.04.00.0.40.00";
                $bkuPenerimaan->skegiatan_kode=$kegiatan->kegiatan_skode;
                $bkuPenerimaan->rekening_id=$rekening->id;
                $bkuPenerimaan->rekening_kode=$value['sub_kode_rekening'];
                $bkuPenerimaan->uraian=$value['uraian'];
                $bkuPenerimaan->pembayaran=1;
                $bkuPenerimaan->total=$value['nilai_sts'];
                $bkuPenerimaan->asal_id_skpd=$skpd->id;
                $bkuPenerimaan->status_jurnal=0;
                $bkuPenerimaan->source=3;
                $bkuPenerimaan->ppkd=1;
                $bkuPenerimaan->actived=1;
                $bkuPenerimaan->save();

                $bkuPenerimaanTbp = new Bkupenerimaan();
                $bkuPenerimaanTbp->id_parent =  $parentPpkd->id;
                $bkuPenerimaanTbp->id_skpd=$ppkd->id;
                $bkuPenerimaanTbp->tahun=2024;
                $bkuPenerimaanTbp->bku_jenis=0;
                $bkuPenerimaanTbp->bku_no=$bkuNo;
                $bkuPenerimaanTbp->bku_tgl=date('Y-m-d');
                $bkuPenerimaanTbp->bukti_no=$value['sts_kd'];
                $bkuPenerimaanTbp->bukti_tgl=$value['sts_tgl'];
                $bkuPenerimaanTbp->kegiatan_id=$kegiatan->id;
                $bkuPenerimaanTbp->kegiatan_kode="1.04.00.0.40.00";
                $bkuPenerimaanTbp->skegiatan_kode=$kegiatan->kegiatan_skode;
                $bkuPenerimaanTbp->rekening_id=$rekening->id;
                $bkuPenerimaanTbp->rekening_kode=$value['sub_kode_rekening'];
                $bkuPenerimaanTbp->uraian=$value['uraian'];
                $bkuPenerimaanTbp->pembayaran=1;
                $bkuPenerimaanTbp->total=$value['nilai_sts'];
                $bkuPenerimaanTbp->nomor_sts=$value['sts_kd'];
                $bkuPenerimaanTbp->asal_id_skpd=$skpd->id;
                $bkuPenerimaanTbp->status_jurnal=0;
                $bkuPenerimaanTbp->source=3;
                $bkuPenerimaanTbp->ppkd=1;
                $bkuPenerimaanTbp->actived=1;
                $bkuPenerimaanTbp->save();

                // $bkuRepo->create($dataTbp);
                ++$i;
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            Notification::sendException($th);
        }

    }
}
