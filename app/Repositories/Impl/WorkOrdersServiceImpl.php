<?php

namespace App\Repositories\Impl;

use App\Models\WorkOrders;
use App\Repositories\BaseService;

class WorkOrdersServiceImpl extends BaseServiceImpl implements BaseService
{
    public function __construct(WorkOrders $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return WorkOrders::class;
    }

    public function getAllWorkOrders($perPage)
    {
        return $this->model()::all()->paginate($perPage);
    }
}
