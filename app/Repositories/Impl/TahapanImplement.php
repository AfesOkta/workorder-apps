<?php

namespace App\Repositories\Impl;

use App\Models\Tahapan;
use App\Repositories\BaseServiceInterface;
use App\Repositories\Impl\BaseServiceImpl;


class TahapanImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public function __construct(Tahapan $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Tahapan::class;
    }

}
