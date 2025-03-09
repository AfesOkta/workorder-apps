<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ModelTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WorkOrdersUpdate
 *
 * @property int $id
 * @property int $work_order_id
 * @property string $status
 * @property int $quantity_updated
 * @property string|null $notes
 * @property int $created_by
 * @property int $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property WorkOrder $work_order
 * @property User $user
 *
 * @package App\Models
 */
class WorkOrdersUpdate extends Model
{
    use ModelTrait;
	protected $table = 'work_orders_updates';

	protected $casts = [
		'work_order_id' => 'int',
		'quantity_updated' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int',
        'assigned_operator_id' => 'int'
	];

	protected $fillable = [
		'work_order_id',
		'status',
		'quantity_updated',
		'notes',
        'assigned_operator_id',
		'created_by',
		'updated_by'
	];

	public function work_order()
	{
		return $this->belongsTo(WorkOrder::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}
}
