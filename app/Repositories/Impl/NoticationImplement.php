<?php

namespace App\Repositories\Impl;

use App\Models\Notification;
use App\Repositories\BaseServiceInterface;
use App\Repositories\Impl\BaseServiceImpl;

class NoticationImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public function __construct(Notification $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Notification::class;
    }

    function getAllNotif(){
        if(auth()->user()->hasRole('administrator') || auth()->user()->hasRole('penyelia') || auth()->user()->hasRole('koor_puskesmas')) {
            return $this->model::orderBy('created_at','desc')->get();
        }else{
            return $this->model::where("created_by", auth()->user()->id)->orderBy('created_at','desc')->get();
        }
    }

}
