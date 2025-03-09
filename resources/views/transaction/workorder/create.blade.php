@extends('layouts.master-layouts')
@section('title')
    @lang('translation.tbp')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet">
    <style>
        @media (max-width: 600px) {
            .card {
                width: 90%;
                margin: 0 auto;
            }

            .form-control {
                font-size: 16px;
                padding: 10px;
            }

            .btn {
                font-size: 16px;
                padding: 10px;
            }
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('pagetitle')
            Work Orders
        @endslot
        @slot('title')
            Tambah Work Order
        @endslot
    @endcomponent
    <div class="d-flex justify-content-center align-items-center">
        <div class="col-lg-8 col-md-10 col-sm-12">
            <form action="{{ route('transaksi.wo.save') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="mb-3">
                            <label for="production_deadline" class="form-label">Tenggat Waktu Produksi</label>
                            <input type="date" class="form-control" id="production_deadline" name="production_deadline" required>
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
                        <input type="hidden" name="id_skpds" id="id_skpds" value="{{ \Session::get('skpd_id') }}">
                        <input type="hidden" name="id_bendahara_" id="id_bendahara_" value="{{ \Session::get('bendahara_id') }}">
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
