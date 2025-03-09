<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Api\SynchronizeController;
use App\Http\Controllers\Controller;
use App\Models\Configs;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class WorkOrdersController extends Controller
{
    public function create()
    {
        $users = User::all();
        return view('transaction.workorder.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'production_deadline' => 'required|date',
            'status' => 'required|in:Pending,In Progress,Completed,Canceled',
            'assigned_operator' => 'required|string|max:255',
        ]);
        // Generate work order number
        $workOrderNumber = 'WO-' . date('Ymd') . '-' . sprintf('%03d', WorkOrder::count() + 1);

        try {
            DB::beginTransaction();
            if($request->id_wo) {
                $id = $request->id_wo;
                $workOrder = WorkOrder::find($id);
                $workOrder->product_name = $request->product_name;
                $workOrder->quantity = $request->quantity;
                $workOrder->deadline = $request->production_deadline;
                $workOrder->status = $request->status;
                $workOrder->assigned_operator_id = $request->assigned_operator;
                $workOrder->save();
            }else{
                WorkOrder::create([
                    'work_order_number' => $workOrderNumber,
                    'product_name' => $request->product_name,
                    'quantity' => $request->quantity,
                    'deadline' => $request->production_deadline,
                    'status' => $request->status,
                    'assigned_operator_id' => $request->assigned_operator,
                ]);
            }
            DB::commit();
            //code...
            if($request->id_wo) {
                Session::flash('status','Work Order updated successfully');
                return redirect()->route('transaksi.wo.index');
            }else{
                Session::flash('status','Work Order created successfully');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }

    public function index()
    {
        $users = User::all();
        return view('transaction.workorder.index', compact('users'));
    }

    public function search(Request $request)
    {
        $query = WorkOrder::withAllRelations()
            ->select('work_order_number','product_name','quantity',DB::raw("to_char(deadline, 'DD-MM-YYYY') as deadlinefrmt"),'status','assigned_operator_id','id');

        if (!empty($request->product_name)) {
            $query->where('product_name', 'like', '%' . $request->product_name . '%');
        }

        if ($request->status != 'All') {
            $query->where('status', $request->status);
        }

        if ($request->assigned_operator != 'All') {
            $query->where('assigned_operator_id', $request->assigned_operator);
        }

        $workOrders = $query;
        $roleProductionManager = auth()->user()->hasRole('production-manager');
        $roleAdmin = auth()->user()->hasRole('administrator');
        $rolePenyelia = auth()->user()->hasRole('operator');
        return DataTables::of($workOrders)
                ->addIndexColumn()
                ->addColumn('action', function($row) use($roleAdmin,$rolePenyelia,$roleProductionManager){
                        if($roleAdmin || $rolePenyelia || $roleProductionManager  ) {
                            $btn = '<a href='.route('transaksi.wo.edit',["id"=>$row->id]).' data-toggle="tooltip"  data-original-title="Login" class="btn btn-warning btn-sm editWo">Edit</a>';
                        }else{
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editWo">Edit</a>';
                        }
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteWo">Delete</a>';
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Logs" class="btn btn-info btn-sm infoLogs">Logs</a>';

                        return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    public function show($id)
    {
        $workOrder = WorkOrder::findOrFail($id);
        $users = User::all();
        return view('transaction.workorder.edit', compact('workOrder', 'users'));
    }

    public function showLogs($id)
    {
        $workOrder = WorkOrder::withAllRelations()->findOrFail($id);
        $logs = $workOrder->work_orders_updates; // Assuming there is a logs relationship defined in the WorkOrder model

        return response()->json([
            'success' => true,
            'logs' => $logs,
            'workOrder'=>$workOrder
        ]);
    }
}
