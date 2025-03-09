<?php

namespace App\Repositories\Impl;

use App\Models\Skpd;
use App\Models\SkpdBendahara;
use App\Models\SourceRekening;
use App\Repositories\BaseServiceInterface;
use App\Repositories\Impl\BaseServiceImpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SourceRekeningImplement extends BaseServiceImpl implements BaseServiceInterface
{
    public function __construct(SourceRekening $model) {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SourceRekening::class;
    }

    public function getFilterBySkpd($id)
    {
        return $this->model->where('id_skpd',$id)->get();
    }

    public function getAllCustom(Request $request) {
        // Cache key
        $cacheKey = 'rekenings_all';

        // Check if data exists in Redis cache
        $rekenings = Cache::remember($cacheKey, 3600, function () {
            // Fetch data from the database and cache it for 1 hour
            return $this->model->select('kode_rekening', 'uraian')->get();
        });

        return $rekenings;
    }
}
