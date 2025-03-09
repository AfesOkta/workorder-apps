@extends('layouts.master-layouts')
@section('title')
@lang('translation.tbp')
@endsection

@section('css')

<link href="{{ URL::asset('/assets/libs//sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/libs/toastr/toastr.min.css') }}" rel="stylesheet">
<style>
.table th, .table td {
    padding: 0.75rem;
    vertical-align: middle;
    border-top: 1px solid #f6f6f6;
}
.tdMiddle {
    vertical-align: middle;
}
.select2-selection__rendered {
    line-height: 33px !important;
}
.select2-container .select2-selection--single {
    height: 37px !important;
}
.select2-selection__arrow {
    height: 35px !important;
}
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('pagetitle') Dashboard @endslot
        @slot('title') @lang('translation.tbp') @endslot
    @endcomponent
    @include('partials.alert_danger',[
        'data' => 'This is fail alert.'
    ])

    @include('partials.alert_success',[
        'data' => 'This is success.'
    ])

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nama Product</label>
                                <input type="text" class="form-control" name="product_name" id="product_name" placeholder="Nama Product">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status Orders</label>
                                <select class="form-control" name="status_orders" id="status_orders" required>
                                    <option value="All" selected>All</option>
                                    <option value="Pending">Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Canceled">Canceled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Asigned To</label>
                                <select class="form-control" name="assigned_operator" id="assigned_operator" required>
                                    <option value="All" selected>All</option>
                                    @foreach ($users as $user )
                                        <option value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="float-right form-group">
                @can('create-wo', WorkOrders::class)
                    <a href="{{route('transaksi.wo.tambah')}}" class="btn btn-primary">Tambah</a>
                @endcan

                @can('upload-wo', WorkOrders::class)
                    <button class="btn btn-outline-danger" data-toggle="modal" data-target="#upload_wo">Upload wo</button>
                @endcan

            </div>
            <br>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="table-1" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th style="text-align: center; border_right: 1px">Action</th>
                                <th style="text-align: left; border_right: 1px">No Work Orders</th>
                                <th style="text-align: left; border_right: 1px">Tanggal Deadline</th>
                                <th style="text-align: left; border_right: 1px">Nama Product</th>
                                <th style="text-align: right; border_right: 1px">Quantity Orders</th>
                                <th style="text-align: left; border_right: 1px">Assigned Operator</th>
                                <th style="text-align: left; border_right: 1px">Status</th>
                            </tr>
                        </thead>

                        <tbody class="font-size-16 font-weight-semibold">

                        </tbody>
                    </table>

                </div>
                <!-- end card-body -->
            </div>
            <!-- end card -->
        </div> <!-- end col -->
    </div>

    @include('transaction.workorder.components.modal_logs_info')
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- toastr js -->
    <script src="{{ URL::asset('/assets/libs/toastr/toastr.min.js') }}"></script>
    <!-- init js -->
    <script src="{{ URL::asset('/assets/js/pages/toastr.init.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("status_orders").value = "All";
            document.getElementById("assigned_operator").value = "All";
        });
        $(function(){
            var table;
            var numFormat = $.fn.dataTable.render.number('\,', '.', 2).display;

            $('#table-1').DataTable().destroy();
            table = $('#table-1').DataTable({
                processing: true,
                serverSide: true,
                method: 'get',
                ajax: {
                    "url": "{{ route('transaksi.wo.search') }}" ,
                    "data": function(d) {
                        d.product_name = $('#product_name').val();
                        d.assigned_operator =$('#assigned_operator').val();
                        d.status = $('#status_orders').val();
                    },
                },
                columns: [
                    {data: 'action', searchable: false, orderable: false},
                    {
                        data:'work_order_number',
                        name:'work_order_number',
                        searchable:false,
                        orderable:false,
                    },
                    {
                        data: 'deadlinefrmt',
                        name: 'deadlinefrmt',
                        searchable: false,
                        orderable: false,
                        className:'tdMiddle'
                    },
                    {
                        data: 'product_name',
                        name: 'product_name',
                        searchable: false,
                        orderable: false,
                        className:'tdMiddle'
                    },
                    {
                        data: 'quantity',
                        render: $.fn.dataTable.render.number(',', '.', 2, ''),
                        name: 'quantity',
                        searchable: false,
                        orderable: false,
                        className:'tdRight'
                    },
                    {
                        data: 'user.name',
                        name: 'user.name',
                        searchable: false,
                        orderable: false,
                        className:'tdMiddle'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        searchable: false,
                        orderable: false,
                        className:'tdMiddle'
                    },
                ],
                "order": [
                    [1, 'desc']
                ],
                autowidth:true,
                responsive: true,
            })

            $('#product_name').on('change', function(){
                table.draw();
            })

            $('#assigned_operator').on('change', function(){
                table.draw();
            })

            $('#status_orders').on('change', function(){
                table.draw();
            })


            $('body').on('click', '.deleteWo',function(){
                let id_wo = $(this).data('id_wo');
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Untuk cancel workorders!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, cancel!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('transaksi.wo.delete') }}",
                            type: 'POST',
                            data: {'id':id_wo, _token: '{{ csrf_token() }}'},
                            success: function (data) {
                                toastr.success(data.Message);
                                table.draw();
                            },
                            error: function (data) {
                                toastr.error('Tidak Berhasil delete/cancel Workorder')
                            }
                        });
                    }
                });
            })

            $('body').on('click','.infoLogs', function() {
                let id_wo = $(this).data('id');
                $.ajax({
                    url: "{{ url('transaction/work-order/showLogs') }}/"+id_wo,
                    type: 'POST',
                    data: {'id':id_wo, _token: '{{ csrf_token() }}'},
                    success: function (data) {
                        $('#no_work_orders').val(data.workOrder.work_order_number);
                        $('#product_name').val(data.workOrder.product_name);
                        $('#quantity').val(data.workOrder.quantity);
                        let logsTable = '';
                        data.logs.forEach(function(log) {
                            logsTable += '<tr>';
                            logsTable += '<td>' + log.status + '</td>';
                            logsTable += '<td>' + log.quantity_updated + '</td>';
                            logsTable += '<td>' + log.notes + '</td>';
                            logsTable += '</tr>';
                        });
                        $('#logs_table_body').html(logsTable);
                    },
                    error: function (data) {
                        toastr.error('Silahkan hubungi administrator!!!')
                    }
                });
                $('#modal_logs').modal('show');
            })
            // $('#tbp_tgl').datepicker({
            //     autoclose: true,
            //     format: "yyyy-mm-dd",
            //     immediateUpdates: true,
            //     todayBtn: true,
            //     todayHighlight: true,
            //     container:"#sync_sts"
            // }).datepicker("setDate", "0");
        });

    </script>
@endsection

