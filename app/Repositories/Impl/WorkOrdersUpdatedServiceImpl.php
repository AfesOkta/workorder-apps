<?php

namespace App\Repositories\Impl;

use App\Models\WorkOrdersUpdate;
use App\Repositories\BaseService;
use App\Repositories\Impl\BaseServiceImpl as ImplBaseServiceImpl;

class WorkOrdersUpdatedServiceImpl extends ImplBaseServiceImpl implements BaseService
{
    public function __construct(WorkOrdersUpdate $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return WorkOrdersUpdate::class;
    }
}
