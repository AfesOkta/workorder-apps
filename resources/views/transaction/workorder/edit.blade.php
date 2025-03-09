@extends('layouts.master-layouts')
@section('title')
@lang('translation.tbp')
@endsection

@section('css')
<!-- DataTables -->
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
<link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/assets/libs//sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/libs/toastr/toastr.min.css') }}" rel="stylesheet">
<!-- DataTables -->
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    /* .dropdown-menu {
        background-color: #f8f9fa;
        border: none;
        box-shadow: none;
        padding: 5px;
        margin: 0;
        width: auto
    } */

    .dropdown-item {
        color: #333;
        padding: 8px 20px;
    }

    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: #e9ecef;
    }

    .dropdown-item.active {
        background-color: #007bff;
        color: #fff;
    }
</style>
@endsection

@section('content')
@include('partials.alert_danger',[
'data' => 'This is fail alert.'
])

@include('partials.alert_success',[
'data' => 'This is success.'
])
@component('components.breadcrumb')
@slot('pagetitle') TBP @endslot
@slot('title') Edit TBP @endslot
@endcomponent

<div class="d-flex justify-content-center align-items-center">
    <div class="col-lg-8 col-md-10 col-sm-12">
        <form action="{{ route('transaksi.wo.save') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Nomor Orders</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" readonly value={{$workOrder->work_order_number}}>
                    </div>
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" readonly value={{$workOrder->product_name}}>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value={{$workOrder->quantity}}>
                    </div>
                    <div class="mb-3">
                        <label for="production_deadline" class="form-label">Tenggat Waktu Produksi</label>
                        <input type="date" class="form-control" id="production_deadline" name="production_deadline" readonly value={{$workOrder->deadline}}>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" name="status" id="status" required>
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Canceled">Canceled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assigned_operator" class="form-label">Operator yang Ditugaskan</label>
                        <select class="form-control" name="assigned_operator" id="assigned_operator" required>
                            @foreach ($users as $user )
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <input type="hidden" name="id_wo" id="id_wo" value="{{$workOrder->id}}">
                    <button type="submit" name="submit" class="w-100 btn btn-primary pull-left" id="btn-submit"><i
                            class="fa fa-database"></i> Proses</button>
                </div>

            </div>
        </form>
    </div>
</div>

@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("status").value = "{{ $workOrder->status }}";
            document.getElementById("assigned_operator").value = "{{ $workOrder->assigned_operator_id }}";
        });
        $(document).ready(function() {
            $("#status").select2({ width: '100%' });
            $('#wo_tgl').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                immediateUpdates: true,
                todayBtn: true,
                todayHighlight: true
            }).datepicker("setDate", "0");
        })
    </script>
@endsection
