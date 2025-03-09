<?php

namespace App\Repositories\Impl;

use App\Models\Skpd;
use App\Models\SkpdBendahara;
use App\Repositories\BaseServiceInterface;
use App\Repositories\Impl\BaseServiceImpl;

class SKPDBendaharaImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public function __construct(SkpdBendahara $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SkpdBendahara::class;
    }

    public function getFilterBySkpd($id)
    {
        return $this->model->scopeWithAllRelations()->where('id_skpd',$id)->whereStatus(0)->get();
    }

    public function getOtorisatorFilterBySkpd($id)
    {
        return $this->model->scopeWithAllRelations()->where('id_skpd',$id)->whereStatus(1)->get();
    }

    public function getFilterByUser($id)
    {
        return $this->model->scopeWithAllRelations()->where('id_bendahara',$id)->get();
    }

    public function updateOrCreate($id, $data)
    {
        return $this->model->updateOrCreate(['id'=>$id], $data);
    }

    public function getActivedBendahara($id) {
        return $this->model->where("id_bendahara", $id)->where('actived', 1)->first();;
    }
}
