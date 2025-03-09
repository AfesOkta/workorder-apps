<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserActivityLog
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $admin_id
 * @property string|null $activity
 * @property string|null $subject
 * @property string|null $table
 * @property string|null $url
 * @property string|null $method
 * @property string|null $ip
 * @property string|null $agent
 * @property string|null $log
 * @property string|null $desc
 * @property string|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Admin|null $admin
 * @property User|null $user
 * @package App\Models
 * @method static Builder|UserActivityLog newModelQuery()
 * @method static Builder|UserActivityLog newQuery()
 * @method static Builder|UserActivityLog query()
 * @method static Builder|UserActivityLog whereActivity($value)
 * @method static Builder|UserActivityLog whereAdminId($value)
 * @method static Builder|UserActivityLog whereAgent($value)
 * @method static Builder|UserActivityLog whereCreatedAt($value)
 * @method static Builder|UserActivityLog whereData($value)
 * @method static Builder|UserActivityLog whereDesc($value)
 * @method static Builder|UserActivityLog whereId($value)
 * @method static Builder|UserActivityLog whereIp($value)
 * @method static Builder|UserActivityLog whereLog($value)
 * @method static Builder|UserActivityLog whereMethod($value)
 * @method static Builder|UserActivityLog whereSubject($value)
 * @method static Builder|UserActivityLog whereTable($value)
 * @method static Builder|UserActivityLog whereUpdatedAt($value)
 * @method static Builder|UserActivityLog whereUrl($value)
 * @method static Builder|UserActivityLog whereUserId($value)
 * @mixin \Eloquent
 */
class UserActivityLog extends Model
{
	const ID = 'id';
	const USER_ID = 'user_id';
	const ADMIN_ID = 'admin_id';
	const ACTIVITY = 'activity';
    const TABLE = 'table';
    const SUBJECT = 'subject';
	const URL = 'url';
	const METHOD = 'method';
	const IP = 'ip';
	const AGENT = 'agent';
	const DESC = 'desc';
	const DATA = 'data';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
    protected $table = 'users_activity_logs';

	protected $casts = [
		self::ID => 'int',
		self::USER_ID => 'int',
		self::ADMIN_ID => 'int'
	];

	protected $dates = [
		self::CREATED_AT,
		self::UPDATED_AT
	];

	protected $fillable = [
		self::USER_ID,
		self::ADMIN_ID,
		self::ACTIVITY,
		self::TABLE,
		self::SUBJECT,
		self::URL,
		self::METHOD,
		self::IP,
		self::AGENT,
		self::DESC,
		self::DATA
	];

	public function admin()
	{
		return $this->belongsTo(Admin::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
