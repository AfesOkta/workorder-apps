<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ModelTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WorkOrder
 *
 * @property int $id
 * @property string $work_order_number
 * @property string $product_name
 * @property int $quantity
 * @property Carbon $deadline
 * @property string $status
 * @property int $assigned_operator_id
 * @property int $created_by
 * @property int $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $user
 * @property Collection|WorkOrdersUpdate[] $work_orders_updates
 *
 * @package App\Models
 */
class WorkOrder extends Model
{
    use ModelTrait;
	protected $table = 'work_orders';

	protected $casts = [
		'quantity' => 'int',
		'deadline' => 'datetime',
		'assigned_operator_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
        'id',
		'work_order_number',
		'product_name',
		'quantity',
		'deadline',
		'status',
		'assigned_operator_id',
		'created_by',
		'updated_by'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'assigned_operator_id');
	}

	public function work_orders_updates()
	{
		return $this->hasMany(WorkOrdersUpdate::class);
	}

    function scopeWithAllRelations()  {
        return $this->with('user','work_orders_updates');
    }
}
