<?php

namespace App\Repositories\Impl;

use App\Models\BludSkpd;
use App\Models\Kegiatan;
use App\Models\Skpd;
use App\Repositories\BaseServiceInterface;
use App\Repositories\Impl\BaseServiceImpl;
use Illuminate\Support\Facades\Cache;

class KegiatanImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public function __construct(Kegiatan $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Kegiatan::class;
    }

    public function findWhereKegiatanSkpdSkode($kodeKegiatan, $skpd, $skode)
    {
        return $this->model->where('kegiatan_kode',"'$kodeKegiatan'")->where('skpd_kode',"'$skpd'")->where('kegiatan_skode',"'$skode'")->first();
    }

    public function getAllKegiatan($skpd,$skode)
    {
        return $this->model->select('subkegiatan_kode','subkegiatan_nama','id')
            ->where('skpd_kode',$skpd)->where('kegiatan_skode',$skode)
            ->where(function($q){
                $q->where('subkegiatan_nama','like','pendapatan%')
                  ->orWhere('subkegiatan_nama','like','aset%');
            })->orderBy('kegiatan_kode','asc')->get();
    }
    public function getFilterByParentSkpd($skpd,$subkegiatan_id)
    {
        return $this->model->select('subkegiatan_kode','subkegiatan_nama','id')
            ->where('skpd_kode',$skpd)->where('kegiatan_skode',$subkegiatan_id)
            ->where(function($q){
                $q->where('subkegiatan_nama','like','pendapatan%')
                  ->orWhere('subkegiatan_nama','like','aset%');
            })->orderBy('kegiatan_kode','asc')->get();
    }

    public function getAllKegiatans()
    {
        return $this->model->select('subkegiatan_kode','subkegiatan_nama','id')
            ->where('subkegiatan_nama','like','pendapatan%')->orderBy('kegiatan_kode','asc')->get();
    }

    public function getAllKegiatansCustom()
    {
        // Cache key
        $cacheKey = 'kegiatans_all';

        // Check if data exists in Redis cache
        $kegiatans = Cache::remember($cacheKey, 3600, function () {
            // Fetch data from the database and cache it for 1 hour
            return $this->model->select('tahun','subkegiatan_kode','subkegiatan_nama','id','lokasi')
            ->orderBy('kegiatan_kode','asc')->get();
        });

        return $kegiatans;
    }

    public function getFilterByParentSkpdAllKegiatan($skpd,$subkegiatan_id)
    {
        return $this->model->select('subkegiatan_kode','subkegiatan_nama','id')
            ->where('skpd_kode',$skpd)
            // ->where('kegiatan_skode',$subkegiatan_id)
            ->orderBy('kegiatan_kode','asc')->get();
    }

    public function getFilterByParentSkpdAllKegiatanPkm($skpd,$subkegiatan_id,$filter="")
    {
        $_skpd = Skpd::find($skpd);
        $existsBlud = BludSkpd::whereKdSkpd($_skpd->kd_skpd)->first();
        if($existsBlud != null) {
            $_skpdParent = Skpd::whereKdSkpd($existsBlud->parent)->first();
            if($filter != "") {
                return $this->model->select('subkegiatan_kode','subkegiatan_nama','id')
                ->where('skpd_kode',$_skpdParent->id)
                ->where(function($q) use($_skpd){
                    $q->where('kegiatan_skode',$_skpd->subkegiatan_id)
                        ->orWhere('kegiatan_skode',1);
                })
                ->where('subkegiatan_nama',"like","%".$filter."%")
                ->orderBy('kegiatan_kode','asc')->get();
            }else{
                return $this->model->select('subkegiatan_kode','subkegiatan_nama','id')
                ->where('skpd_kode',$_skpdParent->id)
                ->where(function($q) use($_skpd){
                    $q->where('kegiatan_skode',$_skpd->subkegiatan_id)
                        ->orWhere('kegiatan_skode',1);
                })
                ->orderBy('kegiatan_kode','asc')->get();
            }
        }else{
            if($filter != "") {
                return $this->model->select('subkegiatan_kode','subkegiatan_nama','id')
                ->where('skpd_kode',$_skpd->id)
                ->where(function($q) use($_skpd){
                    $q->where('kegiatan_skode',$_skpd->subkegiatan_id)
                        ->orWhere('kegiatan_skode',1);
                })
                ->where('subkegiatan_nama',"like","%".$filter."%")
                ->orderBy('kegiatan_kode','asc')->get();
            }else{
                return $this->model->select('subkegiatan_kode','subkegiatan_nama','id')
                ->where('skpd_kode',$_skpd->id)
                ->where(function($q) use($_skpd){
                    $q->where('kegiatan_skode',$_skpd->subkegiatan_id)
                        ->orWhere('kegiatan_skode',1);
                })
                ->orderBy('kegiatan_kode','asc')->get();
            }
        }
    }

    public function getAllKegiatanNoFilters()
    {
        return $this->model->select('subkegiatan_kode','subkegiatan_nama','id')
            ->orderBy('kegiatan_kode','asc')->get();
    }
}
