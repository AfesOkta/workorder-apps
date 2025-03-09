<?php

namespace App\Repositories\Impl;

use App\Models\User;
use App\Repositories\BaseServiceInterface;
use App\Repositories\Impl\BaseServiceImpl;

class UserImplement extends BaseServiceImpl implements BaseServiceInterface
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
