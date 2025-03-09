<?php

namespace App\Repositories\Impl;

use App\Models\Skpd;
use App\Repositories\BaseServiceInterface;
use App\Repositories\Impl\BaseServiceImpl;

class SKPDImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public function __construct(Skpd $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Skpd::class;
    }

    /**
     * getPenghasil
     *
     * @return void
     */
    public function getPenghasil($id = null){
        if($id == null) {
           $penghasil =  $this->model->where('status',1)->orderBy('id','asc')->get();
        }else{
            $skpd= $this->model->find($id);
            if($skpd->kd_skpd == "1 02 0100") {
                $penghasil =  $this->model->where('status',1)->where('parent',$skpd->kd_skpd)->orderBy('id','asc')->get();
            }else {
                $penghasil =  $this->model->where('status',1)->where('id',$id)->orderBy('id','asc')->get();
            }
        }
        return $penghasil;
    }

    public function getPenghasilAll($id = null){
        if($id == null) {
           $penghasil =  $this->model->where('status',1)->orderBy('id','asc')->get();
        }else{
            $skpd= $this->model->find($id);
            if($skpd->kd_skpd == "1 02 0100") {
                $penghasil =  $this->model->where('status',1)->where('parent',$skpd->kd_skpd)->orderBy('id','asc')->get();
            }else {
                $penghasil =  $this->model->where('status',1)->where('id',$id)->orderBy('id','asc')->get();
            }
        }
        return $penghasil;
    }

    public function getDinkes($id = null){
        return $this->model->where('status',1)->where('parent',"1 02 0100")->orderBy('id','asc')->get();
    }
}
