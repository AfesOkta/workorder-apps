<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Menu
 *
 * @property int $id
 * @property string $nama
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|Submenu[] $submenus
 *
 * @package App\Models
 */
class Menu extends Model
{
    protected $table = 'menus';

	protected $casts = [
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'nama',
		'created_by',
		'updated_by'
	];

	public function submenus()
	{
		return $this->hasMany(Submenu::class, 'id_menu');
	}
}
