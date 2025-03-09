<?php

namespace App\Repositories\Impl;

use App\Models\Configs;
use App\Repositories\BaseServiceInterface;
use App\Repositories\Impl\BaseServiceImpl;

class ConfigImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public function __construct(Configs $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Configs::class;
    }
}
