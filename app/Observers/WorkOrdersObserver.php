<?php

namespace App\Observers;

use App\Models\WorkOrder;
use App\Models\WorkOrdersUpdate;
use Illuminate\Support\Facades\DB;

class WorkOrdersObserver
{
    public function updated(WorkOrder $workOrder)
    {
        $data =[
            'work_order_id' => $workOrder->id,
            'status' => $workOrder->status,
            'quantity_updated' => $workOrder->quantity,
            'assigned_operator_id'=> $workOrder->assigned_operator_id
        ];
        WorkOrdersUpdate::create($data);
    }

    public function created(WorkOrder $workOrder)
    {
        $data = [
            'work_order_id' => $workOrder->id,
            'status' => $workOrder->status,
            'quantity_updated' => $workOrder->quantity,
            'assigned_operator_id'=> $workOrder->assigned_operator_id
        ];
        WorkOrdersUpdate::create($data);
    }
}
