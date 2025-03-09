<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UsersLogin
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $user_ip
 * @property int|null $user_skpd
 * @property string|null $location
 * @property string|null $browser
 * @property string|null $os
 * @property string|null $longitude
 * @property string|null $latitude
 * @property string|null $country
 * @property string|null $country_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property SkpdBendahara|null $skpd_bendahara
 * @property User $user
 *
 * @package App\Models
 */
class UsersLogin extends Model
{
	protected $table = 'users_logins';

	protected $casts = [
		'user_id' => 'int',
		'user_skpd' => 'int'
	];

	protected $fillable = [
		'user_id',
		'user_ip',
		'user_skpd',
		'location',
		'browser',
		'os',
		'longitude',
		'latitude',
		'country',
		'country_code'
	];

	public function skpd_bendahara()
	{
		return $this->belongsTo(SkpdBendahara::class, 'user_skpd', 'id_skpd');
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
