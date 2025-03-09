<?php

namespace App\Repositories\Impl;

use App\Models\SkpdBendahara;
use App\Models\Stsheader;
use App\Repositories\BaseServiceInterface;

class StsHeaderImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public $skpdBendaharRepo;
    public function __construct(Stsheader $model, SKPDBendaharaImplement $skpdBendaharRepo) {
        parent::__construct($model);
        $this->skpdBendaharRepo = $skpdBendaharRepo;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Stsheader::class;
    }

    /**
     * doGetMaxKode
     *
     * @param  mixed $field
     * @param  mixed $value
     * @return void
     */
    public function doGetMaxKode($field, $value)
    {
        return $this->model->where($field,$value)->max('sts_kode');
    }

    public function doGetAllLatest() {
        return $this->model->withAllRelations()->where('actived',1)->orderByRaw('id_skpd asc, created_at desc');
    }

    public function doGetAllLatestBatal() {
        return $this->model->withAllRelations()->where('actived',0)->orderByRaw('id_skpd asc, created_at desc');
    }

    public function doWithRelations($id) {
        return $this->model->withAllRelations()->where('id',$id)->first();
    }

    public function getAllLunas($tgl, $id_skpd) {
        return $this->model->where("sts_tgl","<=",$tgl)->where('id_skpd',$id_skpd)
                    ->where('lunas',1)->where('akt',0)
                    ->where('actived',1)
                    ->where('approved','>',0)->get();
    }

    function getCountSts(){
        if (auth()->user()->hasRole("administrator") || auth()->user()->hasRole("penyelia")  || auth()->user()->hasRole("koor_puskesmas")) {
            return $this->model->where('actived',1)->count();
        }else{
            $userActived = $this->skpdBendaharRepo->getActivedBendahara(auth()->user()->id);
            return $this->model->where('skpd_bend_id', $userActived->id)->where('actived',1)->count();
        }

    }

    public function getCountStsBatal() {
        if (auth()->user()->hasRole("administrator") || auth()->user()->hasRole("penyelia")  || auth()->user()->hasRole("koor_puskesmas")) {
            return $this->model->where('actived',0)->count();
        }else{
            $userActived = $this->skpdBendaharRepo->getActivedBendahara(auth()->user()->id);
            return $this->model->where('skpd_bend_id', $userActived->id)->where('actived',0)->count();
        }

    }
}
