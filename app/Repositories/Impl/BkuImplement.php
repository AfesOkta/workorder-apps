<?php

namespace App\Repositories\Impl;

use App\Models\Bkupenerimaan;
use App\Models\Stsheader;
use App\Repositories\BaseServiceInterface;
use App\Repositories\Impl\BaseServiceImpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BkuImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public function __construct(Bkupenerimaan $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Bkupenerimaan::class;
    }

    public function getBkupenerimaan(Request $request) {
        $bkuPenerimaans = $this->model->withAllRelations()->whereActived(1)
             ->whereIn('status_jurnal',[1,2]);

        if(isset($request->id_skpd)) {
            $bkuPenerimaans->where('id_skpd',$request->id_skpd);
        }

        if(isset($request->bku_no)) {
            $bkuPenerimaans->where('bku_no',"like","%".$request->bku_no."%");
        }

        if(isset($request->no_bukti)) {
            $bkuPenerimaans->where('bukti_no',"like","%".$request->no_bukti."%");
        }

        if(isset($request->nomor_sts)) {
            $stsKode = $request->nomor_sts;
            $bkuPenerimaans->where(function($q)use($stsKode){
                $q->where('bukti_no',"like","%".$stsKode."%")
                  ->orWhere('nomor_sts',"like","%".$stsKode."%");
            });
        }

        $bkuPenerimaans = $bkuPenerimaans->orderByRaw('id_skpd asc, bku_no desc');

        return $bkuPenerimaans;
    }

    public function doGetMaxKode($field, $value) {
        return $this->model->where($field,$value)->max('bku_no');
    }

    public function countBkuPenerimaan($idSkpd,$userId,$bkuNo) {
        return $this->model->where('id_skpd',$idSkpd)->where('created_by',$userId)
            ->where('source',3)->where('status_jurnal', "<", 2)
            ->where('bku_no',$bkuNo)->count();
    }

    function deleteBkuErrorByBuktiNo($idSkpd,$userId,$bukti_no) {
        return $this->model->where('id_skpd',$idSkpd)->where('created_by',$userId)
            ->where('source',3)->where('status_jurnal', "<", 1)
            ->where('bukti_no',$bukti_no)->delete();
    }

    function deleteAllBkuError($idSkpd,$userId) {
        return $this->model->where('id_skpd',$idSkpd)->where('created_by',$userId)
            ->where('source',3)->where('status_jurnal', "<", 1)->delete();
    }

    function getBkuNoExists($no_bukti, $id_skpd) {
        return $this->model->where('id_skpd',$id_skpd)->where('bukti_no',$no_bukti)
            ->where('status_jurnal',"<", 2)->where('bku_jenis', 1)->first()->bku_no;
    }

    function getListBkuNoSyncAkt($tgl, $id_skpd) {
        return $this->model->where('status_jurnal',0)
        ->where('bku_jenis',1)
        ->where('send',0)
        ->where('source',3)
        ->where('actived',1)->orderByRaw('id_skpd asc, bukti_no asc, bukti_tgl asc')
        ->SELECT(DB::raw("DISTINCT id_parent, id_skpd, bku_jenis,bukti_no,bukti_tgl, uraian, kegiatan_kode, skegiatan_kode"))
        ->get();
    }

    public function getBkupenerimaanBeforeSyncAkt(Request $request) {
        $bkuPenerimaans = $this->model->withAllRelations()
        ->where('id_skpd',$request->id_skpd)
        ->where('bukti_tgl',"<=",$request->tgl)
        ->whereActived(1)->where('bku_jenis', 1)
        ->where('status_jurnal', 0);
        // ->where('status_jurnal',2);

        if(isset($request->skpd)) {
            $bkuPenerimaans->where('id_skpd',$request->skpd);
        }

        if(isset($request->bku_no)) {
            $bkuPenerimaans->where('bku_no',$request->bku_no);
        }

        if(isset($request->tbp_kode)) {
            $bkuPenerimaans->where('bukti_no',$request->tbp_kode);
        }

        if(isset($request->sts_kode)) {
            $stsKode = $request->sts_kode;
            $bkuPenerimaans->where(function($q)use($stsKode){
                $q->where('bukti_no',$stsKode)
                  ->orWhere('sts_kode',$stsKode);
            });
        }

        $bkuPenerimaans = $bkuPenerimaans->orderBy('bku_no', 'desc');

        return $bkuPenerimaans;
    }

    public function getBkuForJenisTbp($tgl, $id_skpd,$no_bukti)  {
        return $this->model->where('id_skpd',$id_skpd)->where('bukti_tgl',"<=",$tgl)
            ->where('status_jurnal', 1)
            ->where('bku_jenis', 0)->where('nomor_sts',$no_bukti)->get();
    }

    function getListBkuNoAftSyncAkt($bkuNo, $id_skpd) {
        return $this->model->where('id_skpd',$id_skpd)->where('bku_no',$bkuNo)
            ->whereIn('status_jurnal', [1,2])->where('bku_jenis', 1)->get();
    }

    function getListBkuTertunda($bkuNo, $id_skpd) {
        return $this->model->where('id_skpd',$id_skpd)->where('bku_no',$bkuNo)
        ->whereIn('status_jurnal', [1,2])->where('source',1)->where('bku_jenis', 0)->get();
    }

    function getListBkuManual($bkuNo, $id_skpd) {
        return $this->model->where('id_skpd',$id_skpd)->where('bku_no',$bkuNo)
            ->whereIn('status_jurnal', [1,2])->where('source',2)->where('bku_jenis', 1)->get();
    }

    public function getValidationBkuPpkd(Request $request) {
        $bkuPenerimaans = $this->model->withAllRelations()->whereActived(1)
             ->where('status_jurnal','=',0)->wherePpkd(1)->whereBkuJenis(1);

        $bkuPenerimaans = $bkuPenerimaans->orderByRaw('id_skpd asc, bku_no desc');

        return $bkuPenerimaans;
    }

    function deleteBkuUpload(Request $request){
        return $this->model->withAllRelations()->whereActived(1)
             ->where('status_jurnal','=',0)->wherePpkd(1)->delete();

    }

    function getUpdateStatusJurnal($tgl, $id_skpd) {
        $stsList = $this->model->where('bukti_tgl', "<=", $tgl)
            ->where('id_skpd', $id_skpd)
            ->where('status_jurnal', 0)
            ->where('source', 3)
            ->where('actived', 1)
            ->where('bku_jenis', 1)
            ->get();
        foreach ($stsList as $value) {
            Stsheader::where('sts_kode', $value->bukti_no)
                ->where('id_skpd', $id_skpd)
                ->where('actived', 1)
                ->update(['akt' => 1, 'tgl_sync_akt' => $tgl]);
        }
        $this->model->where('bukti_tgl', "<=", $tgl)
            ->where('id_skpd', $id_skpd)
            ->where('status_jurnal', 0)
            ->where('source', 3)
            ->where('actived', 1)
            ->update(['status_jurnal' => 1]);

    }
}
