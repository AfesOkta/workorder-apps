<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Permission extends \Spatie\Permission\Models\Permission
{
    public static $alias  = 'permissions';

	public static function isExists($name)
    {
        if (! self::where('name', $name)->first()) {
            return false;
        }

        return true;
    }

    public static function createIfNotExists($name)
    {
        if (! self::isExists($name)) {
            self::create(['name' => $name, 'guard_name' => 'api']);
        }

        return self::where('name', $name)->first();
    }
}
