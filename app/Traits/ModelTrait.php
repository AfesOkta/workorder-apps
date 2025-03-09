<?php

namespace App\Traits;

use App\Observers\ModelObserver;

trait ModelTrait
{
    public static function bootModelTrait()
    {
        static::observe(ModelObserver::class);
    }
}
