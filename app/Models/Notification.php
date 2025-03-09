<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ModelTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification
 *
 * @property int $id
 * @property int|null $type
 * @property string $data
 * @property Carbon $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 * @method static Builder|Notification withAllRelations()
 */
class Notification extends Model
{
    use ModelTrait;

	protected $table = 'notification';

	protected $casts = [
		'type' => 'int',
		'read_at' => 'date'
	];

	protected $fillable = [
		'type',
		'data',
		'read_at'
	];

    public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}


    public function scopeWithAllRelations()
    {
        return $this->with('user');
    }
}
