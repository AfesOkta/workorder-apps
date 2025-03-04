<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

class ModelObserver
{
    public function creating(Model $biaya) {
        $biaya->created_by = auth()->user()->id;
        $biaya->created_at = date('Y-m-d H:i:s');
    }

    public function updating(Model $biaya) {
        $biaya->updated_by = auth()->user()->id;
        $biaya->updated_at = date('Y-m-d H:i:s');
    }
}
