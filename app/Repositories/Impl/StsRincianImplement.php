<?php

namespace App\Repositories\Impl;

use App\Models\Stsrincian;
use App\Repositories\BaseServiceInterface;

class StsRincianImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public function __construct(Stsrincian $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Stsrincian::class;
    }

    /**
     * doGetMaxKode
     *
     * @param  mixed $field
     * @param  mixed $value
     * @return void
     */
    public function doGetMaxNourut($field, $value, $field2, $value2)
    {
        return $this->model->where($field,$value)
            ->where($field2,$value2)->max('line_no');
    }

    public function doGetAllRincian($id, $skpd)
    {
        return $this->model->withAllRelations()->where('id_hdr', $id)->where('id_skpd',$skpd)->orderBy('line_no','desc')->get();
    }

    public function getSubTotal($id, $skpd)
    {
        return $this->model->where('id_hdr', $id)->where('id_skpd',$skpd)->sum('sub_total');
    }

    function getAllRincianNoExistsPpkd($id,$skpd, $rekening) {
        return $this->model->withAllRelations()->where('id_hdr', $id)->where('id_skpd',$skpd)
            ->whereNotIn('subrekening_kode',$rekening)->orderBy('line_no','desc')->get();
    }
}
