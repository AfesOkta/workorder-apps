<?php

namespace App\Repositories\Impl;

use App\Models\Skpd;
use App\Models\SkpdBendahara;
use App\Models\SkpdOtorisator;
use App\Repositories\BaseServiceInterface;
use App\Repositories\Impl\BaseServiceImpl;

class SKPDOtorisatorImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public function __construct(SKPDOtorisator $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SkpdOtorisator::class;
    }

    public function getFilterBySkpd($id)
    {
        return $this->model->scopeWithAllRelations()->where('id_skpd',$id)->get();
    }

    public function getFilterByUser($id)
    {
        return $this->model->scopeWithAllRelations()->where('id_bendahara',$id)->get();
    }

    public function updateOrCreate($id, $data)
    {
        return $this->model->updateOrCreate(['id'=>$id], $data);
    }
}
