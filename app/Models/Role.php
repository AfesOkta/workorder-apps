<?php

namespace App\Models;

use App\Traits\Auth\RoleJoin;
use App\Traits\Auth\RoleRelation;
use App\Traits\EloquentFilters;

class Role extends \Spatie\Permission\Models\Role
{
    use EloquentFilters, RoleJoin, RoleRelation;
	public static $alias = 'roles';

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

    public static function getTableName($column = null)
    {
        $tableName = with(new static)->getTable();

        if (isset($column)) {
            $tableName = "$tableName.$column";
        }

        return $tableName;
    }
}
