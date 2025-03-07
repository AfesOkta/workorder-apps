<?php

namespace App\Repositories\Impl;

use App\Models\Reports;
use App\Repositories\BaseService;

class ReportServiceImpl extends BaseServiceImpl implements BaseService
{
    public function __construct(Reports $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Reports::class;
    }
}
