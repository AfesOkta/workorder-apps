<?php

namespace App\Repositories\Impl;

use App\Models\Tbprincian;
use App\Repositories\BaseServiceInterface;

class TbpRincianImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public function __construct(Tbprincian $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Tbprincian::class;
    }

    public function doGetMaxNourut($field, $value, $field2, $value2)
    {
        return $this->model->where($field,$value)
            ->where($field2,$value2)->max('no_urut');
    }

    public function doGetAllRincian($id, $skpd)
    {
        return $this->model->withAllRelations()->where('header_id', $id)->where('skpd_id',$skpd)->where('actived',1)->orderBy('no_urut','desc')->get();
    }

    public function getSubTotal($id, $skpd)
    {
        return $this->model->where('header_id', $id)->where('skpd_id',$skpd)->where('actived',1)->sum('nominal');
    }

    public function updateOrCreate(array $id, array $attributes)
    {
        return $this->model->updateOrCreate($id, $attributes);
    }

    public function getTbpRincianById($id, $id_skpd) {
        return $this->model->withAllRelations()->where('header_id',$id)->where('skpd_id',$id_skpd)->where('actived',1)->orderBy('no_urut')->get();
    }

    function findRincianByRekeningNSubKegiatan($rekeningId, $subKegiatanKode, $idSkpd, $hdrId) {
        return $this->model->withAllRelations()->where('rekening_id',$rekeningId)
        ->where('skode_kegiatan',$subKegiatanKode)
        ->where('skpd_id',$idSkpd)
        ->where('header_id',$hdrId)
        ->where('actived',1)->first();
    }
}
