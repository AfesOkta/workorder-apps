<?php

namespace App\Repositories\Impl;

use App\Models\User;
use App\Repositories\BaseService;

class UserServiceImpl extends BaseServiceImpl implements BaseService
{
    public function __construct(User $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }
}
