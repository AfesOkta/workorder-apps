<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Submenu
 * 
 * @property int $id
 * @property int $id_menu
 * @property string $nama
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Menu $menu
 *
 * @package App\Models
 */
class Submenu extends Model
{
	protected $table = 'submenus';

	protected $casts = [
		'id_menu' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'id_menu',
		'nama',
		'created_by',
		'updated_by'
	];

	public function menu()
	{
		return $this->belongsTo(Menu::class, 'id_menu');
	}
}
