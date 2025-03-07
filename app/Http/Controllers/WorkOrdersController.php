<?php

namespace App\Http\Controllers;

use App\Models\WorkOrders;
use App\Repositories\Impl\WorkOrdersServiceImpl as ImplWorkOrdersServiceImpl;
use Illuminate\Http\Request;
use App\Services\WorkOrdersServiceImpl;

class WorkOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $repository = New ImplWorkOrdersServiceImpl(new WorkOrders);
        $perPage = $request->input('per_page', 15);
        $workOrders = $repository->getAllWorkOrders($perPage);

        return response()->json($workOrders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkOrders $workOrders)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkOrders $workOrders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkOrders $workOrders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkOrders $workOrders)
    {
        //
    }
}
