<?php

namespace App\Repositories\Impl;

use App\Models\Anggaran;
use App\Models\Configs;
use App\Models\Skpd;
use App\Models\SkpdBendahara;
use App\Models\SourceRekening;
use App\Repositories\BaseServiceInterface;
use App\Repositories\Impl\BaseServiceImpl;

class AnggaranImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public function __construct(Anggaran $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Anggaran::class;
    }

    public function getFilterBySkpd($id)
    {
        return $this->model->withAllRelations()->where('skpd_id',$id)->get();
    }

    public function getFilterByParentSkpd($id,$subkegiatan_id, $rekening=null)
    {
        if($rekening != null) {
            return $this->model->with(['rekening'])
                ->whereHas('rekening',function($query) use($rekening){
                    $query->where('kode_rekening',$rekening);
                })
                ->where('skpd_id',$id)
                ->where('subkegiatan_skode',$subkegiatan_id)->get();
        }else{
            return $this->model->withAllRelations()->where('skpd_id',$id)->where('subkegiatan_skode',$subkegiatan_id)->get();
        }

    }

    public function firstExistsAnggarannominal($skpd,$kegiatan,$rekening, $tahapan,$subkegiatan_id,$nominal)
    {
        return $this->model->withAllRelations()->where('skpd_id', $skpd->id)
        ->where('subkegiatan_skode',$subkegiatan_id)
        ->where('subkegiatan_id',$kegiatan->id)
        ->where('subrekening_id',$rekening->id)
        ->where('tahapan_id',$tahapan->id)
        ->where('nominal',$nominal)->first();
    }

    public function firstExistsAnggaran($skpd,$kegiatan,$rekening, $tahapan,$subkegiatan_id)
    {
        return $this->model->withAllRelations()->where('skpd_id', $skpd->id)
        ->where('subkegiatan_skode',$subkegiatan_id)
        ->where('subkegiatan_id',$kegiatan->id)
        ->where('subrekening_id',$rekening->id)
        ->where('tahapan_id',$tahapan->id)->first();
    }

    public function firstExistsAnggaran2($skpd,$kegiatan,$rekening, $tahapan,$subkegiatan_id)
    {
        return $this->model->withAllRelations()->where('skpd_id', $skpd)
        ->where('subkegiatan_skode',$subkegiatan_id)
        ->where('subkegiatan_id',$kegiatan)
        ->where('subrekening_id',$rekening)
        ->where('tahapan_id',$tahapan->id)->first();
    }

    public function firstExistsAnggaran2Nominal($skpd,$kegiatan,$rekening, $tahapan,$subkegiatan_id,$nominal)
    {
        return $this->model->withAllRelations()->where('skpd_id', $skpd)
        ->where('subkegiatan_skode',$subkegiatan_id)
        ->where('subkegiatan_id',$kegiatan)
        ->where('subrekening_id',$rekening)
        ->where('tahapan_id',$tahapan->id)
        ->where('nominal',$nominal)->first();
    }

    public function getExistsRekeningWithNominalZeroAnggaran($skpd,$kegiatan,$rekening, $tahapan,$subkegiatan_id)
    {
        return $this->model->withAllRelations()->where('skpd_id', $skpd->id)
        ->where('subkegiatan_skode',$subkegiatan_id)
        ->where('tahapan_id','<',$tahapan->id)
        ->where('nominal',0)->get();
    }

    public function getExistsRekeningAnggaran($skpd,$kegiatan,$rekening, $tahapan,$subkegiatan_id)
    {
        return $this->model->withAllRelations()->where('skpd_id', $skpd->id)
        ->where('subkegiatan_skode',$subkegiatan_id)
        ->where('tahapan_id','<',$tahapan->id)->get();
    }

    public function findOrCreate($dataAll)
    {
        return $this->model->updateOrCreate(['subkegiatan_id'=>$dataAll['subkegiatan_id'],'subrekening_id'=>$dataAll['subrekening_id'],'tahapan_id'=>$dataAll['tahapan_id']], $dataAll);
    }

    public function getPpkdAnggaran($skpd)
    {
        $config = Configs::find(1);
        return $this->model->withAllRelations()->where('skpd_id', $skpd)
        ->where('ppkd',1)
        ->where('tahapan_id',$config->tahapan_id)
        ->where('actived',1)->get()->pluck('subrekening_id','subrekening_id');
    }

    public function firstExistsAnggaranById($skpd,$kegiatan,$rekening, $tahapan,$subkegiatan_id)
    {
        return $this->model->withAllRelations()->where('skpd_id', $skpd)
        ->where('subkegiatan_skode',$subkegiatan_id)
        ->where('subkegiatan_id',$kegiatan)
        ->where('subrekening_id',$rekening)
        ->where('tahapan_id',$tahapan)->first();
    }
}
