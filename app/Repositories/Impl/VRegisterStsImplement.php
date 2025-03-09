<?php

namespace App\Repositories\Impl;

use App\Models\VRegisterSts;
use App\Repositories\BaseServiceInterface;

class VRegisterStsImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public function __construct(VRegisterSts $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return VRegisterSts::class;
    }

    public function doGetAllLatests() {
        return $this->model->orderBy('created_at','asc');
    }
}
