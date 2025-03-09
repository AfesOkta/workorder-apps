<?php
namespace App\Repositories\Impl;

use App\Models\SkpdBendahara;
use App\Models\Tbpheader;
use App\Repositories\BaseServiceInterface;
use App\Repositories\Impl\BaseServiceImpl;

class TbpHeaderImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public $skpdBendaharRepo;
    public function __construct(Tbpheader $model, SKPDBendaharaImplement $skpdBendaharRepo) {
        parent::__construct($model);
        $this->skpdBendaharRepo = $skpdBendaharRepo;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Tbpheader::class;
    }

    /**
     * doGetMaxKode
     *
     * @param  mixed $field
     * @param  mixed $value
     * @return void
     */
    public function doGetMaxKode($field, $value)
    {
        return $this->model->where($field,$value)->max('tbp_kode');
    }

    /**
     * doGetAllLatest
     *
     * @return void
     */
    public function doGetAllLatest()
    {
        return $this->model->withAllRelations()->where('actived',1)->orderByRaw('id_skpd asc, created_at desc');
    }

    public function doWithRelations($id) {
        return $this->model->withAllRelations()->where('id',$id)->first();
    }

    /**
     * doGetAllLatest
     *
     * @return void
     */
    public function doGetAllLatestBatal()
    {
        return $this->model->withAllRelations()->where('actived',0)->orderByRaw('id_skpd asc, created_at desc');
    }

    public function getAllTbpSts($id_skpd, $sts){
        return $this->model->withAllRelations()->where('id_skpd',$id_skpd)->where('sts_kode',$sts)->where('actived',1)->get();
    }

    public function findAllTbpSts($id_skpd, $sts){
        return $this->model->withAllRelations()->where('id_skpd',$id_skpd)->where('sts_kode',$sts)->where('actived',1)->first();
    }

    public function getCountTbp() {
        if (auth()->user()->hasRole("administrator") || auth()->user()->hasRole("penyelia")  || auth()->user()->hasRole("koor_puskesmas")) {
            return $this->model->where('actived',1)->count();
        }else{
            $userActived = $this->skpdBendaharRepo->getActivedBendahara(auth()->user()->id);
            return $this->model->where('id_bendahara', $userActived->id)->where('actived',1)->count();
        }

    }

    public function getCountTbpBatal() {
        if (auth()->user()->hasRole("administrator") || auth()->user()->hasRole("penyelia")  || auth()->user()->hasRole("koor_puskesmas")) {
            return $this->model->where('actived',0)->count();
        }else{
            $userActived = $this->skpdBendaharRepo->getActivedBendahara(auth()->user()->id);
            return $this->model->where('id_bendahara', $userActived->id)->where('actived',0)->count();
        }

    }
}
