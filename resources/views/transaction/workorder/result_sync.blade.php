@extends('layouts.master-layouts')
@section('title')
@lang('translation.list-synchronize-sts')
@endsection

@section('css')
<!-- DataTables -->
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/libs//sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/libs/toastr/toastr.min.css') }}" rel="stylesheet">
<style>

    td.details-control {
        background: url('{{asset('assets/img/details_open.png')}}') no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url('{{asset('assets/img/details_close.png')}}') no-repeat center center;
    }
    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid blue;
        border-right: 16px solid green;
        border-bottom: 16px solid red;
        border-left: 16px solid pink;
        width: 60px;
        height: 60px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
    }

    tr.Highlight {
        /* background-color: rgb(255, 0, 0);
        color: black; */
        color: red;
    }
    .Highlight-white{
        color: #f8f8fa;
    }

    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
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

@component('components.breadcrumb-subtitle')
@slot('pagetitle') Transaksi @endslot
@slot('title') TBP @endslot
@slot('subtitle') Synchronize STS @endslot
@endcomponent


<div class="row">
    <div class="col-md-12">
        <div class="float-right form-group">
            <a href="javascript:void(0)" class="btn btn-danger process_all">Process</a>

            <a href="{{route('transaksi.tbp.index')}}" class="btn btn-outline-info">Kembali</a>
        </div>
        <div class="loader" id="loader"></div>
        <br>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-1" class="table dt-responsive wrap w-100">
                        <thead>
                            <th style="max-width: 10px">
                                <input type="checkbox" id="selectAll" disabled="disabled" />
                            </th>
                            <th style="min-width: 200px">Nama SKPD</th>
                            <th style="min-width: 200px">Kode TBP</th>
                            <th style="max-width: 100px">Tgl. Penerimaan</th>
                            <th style="max-width: 100px">Cara Bayar</th>
                            <th style="max-width: 100px">Total</th>
                            <th style="max-width: 50px">Status</th>
                            {{-- <th style="max-width: 50px">Aksi</th> --}}
                        </thead>

                        <tbody class="font-size-16 font-weight-semibold">
                        </tbody>
                    </table>
                </div>
                <div style="display: none">
                    <table class="table table-bordered table-striped table-child" id="table-child" style="width:100%">
                        <thead>
                            <th style="max-width: 50px">@lang("Kode Kegiatan")</th>
                            <th style="max-width: 200px">@lang("Nama Kegiatan")</th>
                            <th style="max-width: 100px">@lang("Kode Rekening")</th>
                            <th style="min-width: 100px">@lang("Nama Rekening")</th>
                            <th style="max-width: 100px">@lang("Nominal")</th>
                        </thead>
                        <tbody class="detail-transaction font-size-14 font-weight-semibold"></tbody>
                    </table>
                </div>
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card -->
    </div> <!-- end col -->
</div> <!-- end row -->

@include('transaction.tbp.components.modal_sync_sts_result')
@endsection
@section('script')
<script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<!-- datatables-->
<script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
<!-- init js -->
<script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- toastr js -->
<script src="{{ URL::asset('/assets/libs/toastr/toastr.min.js') }}"></script>
<!-- init js -->
<script src="{{ URL::asset('/assets/js/pages/toastr.init.js') }}"></script>

<script>
    $(function () {
        var child_table_html = $('.table-child').html();
        var child_table;
        var child_table_counter = 1;
        var table = $('#table-1').DataTable({
            "displayLength": -1,
            processing: true,
            serverSide: true,
            ajax: {
                "url": '{{ route("transaksi.tbp.sync.json.sts",[$url]) }}'
            },
            columns: [
                {
                    data:'checkAll',
                    name:"checkAll",
                    orderable : false
                },
                {
                    data: 'skpd.nama_skpd',
                    name: 'skpd.nama_skpd',
                    searchable: false,
                    orderable: false,
                    className: 'tdMiddleLeft',
                },
                {data : 'tbp_kode', searchable: false,
                    orderable: false},
                {data : 'tbp_tgl', searchable: false,
                    orderable: false},
                {data : 'caraBayar', searchable: false,
                    orderable: false},
                {
                    data: 'total',render: $.fn.dataTable.render.number(',', '.', 2, ''), className: 'tdRight',name: 'total',searchable: true,orderable: true
                },
                {data : 'statusTbp', searchable: false,
                    orderable: false},
                // {
                //     data: 'action',name: 'action', className: 'tdCenter',searchable:false, orderable:true,
                // },
            ],
            rowReorder: {
                selector: 'td:nth-child(4)'
            },
            autowidth: true,
            responsive:false,
            "initComplete": function(settings, json){
                table.on('click', '.details-control', function(){
                    var Tr = $(this).closest('tr');
                    var row_parent = table.row(Tr);
                    var data_parent = table.row(Tr).data();
                    var tgl_bayar = data_parent.tbp_tgl;
                    if ( row_parent.child.isShown() ) {
                        // This row is already open - close it
                        row_parent.child.hide();
                        Tr.removeClass('shown');
                    }else {
                        $('#loader').show();
                        $('.modal-dialog').attr('class','modal-dialog modal-sm');
                        Tr.addClass('shown');
                        // Open this row
                        ajaxDetail = $.ajax({
                            method: 'get',
                            url: "{{route('transaksi.tbp.sync.json.sts.detail',[$url])}}",
                            "datatype": "json",
                            success: function(data){
                                $('#loader').hide();
                                var child = "<table class='child_table_" + child_table_counter + " table table-hover order-column'  style='font-size:12px'>";
                                child += child_table_html;
                                child += "</table>";
                                row_parent.child(child).show();
                                var sum= 0;
                                child_table = $('.child_table_'+child_table_counter).DataTable({
                                    "searching": false,
                                    "destroy": true,
                                    "data": data.collections,
                                    "ordering" : false,
                                    "paging": false,
                                    "info": false,
                                    "columns": [
                                        {data: 'kegiatan.subkegiatan_kode', name: 'kegiatan.subkegiatan_kode', searchable: true, orderable: true},
                                        {data: 'kegiatan.subkegiatan_nama', name: 'kegiatan.subkegiatan_nama', searchable: true, orderable: true},
                                        {data: 'source_rekening.kode_rekening', name: 'source_rekening.kode_rekening',className:'thumbnailProdut', searchable: true, orderable: true},
                                        {data: 'source_rekening.uraian', name: 'source_rekening.uraian',className:'thumbnailProdut', searchable: true, orderable: true},
                                        {data: 'total', render: $.fn.dataTable.render.number(',', '.', 0, ''), className: 'tdRight', name: 'nominal',
                                            searchable: true, orderable: true},
                                    ],
                                    "columnDefs": [
                                        {
                                            "targets": [ 0 ],
                                            "orderable": false
                                        },{
                                            "target":1,
                                            "width":"150px"
                                        }
                                        ,{
                                            "target":3,
                                            "width":"150px"
                                        }
                                    ],
                                    "createdRow": function ( row, data, index ) {
                                    },
                                    "order": [[0, 'asc']],
                                });
                                child_table_counter = child_table_counter+1;
                            },
                            error:function (xhr, ajaxOptions, thrownError) {
                                $('#loader').hide();
                                toastr.error('Tidak Berhasil synchronize Tbp to STS')

                            }
                        });
                    }
                })
            },
            rowCallback: function( row, data ) {
                if (data['daysTbpNotSync'] > 1){
                    $(row).addClass('Highlight');
                }else{
                    $(row).addClass('Highlight-blue');
                }
            },
        });

        $('#selectAll').removeAttr('disabled');
        $('#loader').hide();
        $('#loaderProcess').hide();

        $('body').on('click', '.proses_sts', function(){
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Untuk melanjutkan process Tbp to Sts!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Lanjutkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let sts_tgl     = $('body #modal_process_tbp #sts_tgl').val();
                    let uraian      = $('body #modal_process_tbp #uraian_input').val();
                    let id_skpd     = $('body #modal_process_tbp #id_skpd').val();
                    let jns_bayar   = $('body #modal_process_tbp #jenis_pembayaran').val();
                    let user_id     = $('body #modal_process_tbp #user_id').val();
                    let tbp_tgl     = $('body #modal_process_tbp #tgl_tbp').val();
                    let id_hdr      = $('body #modal_process_tbp #id_hdr').val();
                    let data = {
                        _token: '{{csrf_token()}}',
                        sts_tgl: sts_tgl,
                        uraian: uraian,
                        id_skpd:id_skpd,
                        user_id:user_id,
                        jns_bayar:jns_bayar,
                        tbp_tgl:tbp_tgl,
                        id: id_hdr
                    };
                    $('.proses_sts').attr('disabled','disabled');
                    $('#loaderProcess').show();
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        data: data,
                        url: "{{ route('transaksi.tbp.process.sts') }}",
                        success: function(data) {
                            if(data.status) {
                                $('#loaderProcess').hide();
                                $('.proses_sts').removeAttr('disabled');
                                $('#modal_process_tbp').modal("hide");
                                toastr.success(data.Message);
                                table.ajax.reload();
                                window.location.href = "{{ route('transaksi.sts.index') }}";
                            }else{
                                $('#loaderProcess').hide();
                                $('.proses_sts').removeAttr('disabled');
                                toastr.error(data.Message)
                            }
                        },
                        error: function(data) {
                            $('#loaderProcess').hide();
                            $('.proses_sts').removeAttr('disabled');
                            toastr.error('Tidak Berhasil synchronize Tbp to STS')
                            console.log(data);
                        }
                    });
                }else{}
            });
        })

        $('#selectAll').on('click', function(e){
            var rows = table.rows({ 'search': 'applied' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        $('.process_all').on('click', function(){
            syncIDArray= [];
            countIndex = 0;
            countIndex= $('#table-1 tbody input[type=checkbox]:checked').length;
            $('.process-all').attr('disabled','disabled');
            $('#table-1 tbody').find('input[type=checkbox]:checked').each(function(){
                syncIDArray.push($(this).val());
            });
            if(countIndex == 0) {
                toastr.error('Mohon pilih dahulu TBP yang akan dibuat menjadi STS');
                return false;
            }
            $('.process_all').attr('disabled','disabled');
            $('#loader').show();
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Untuk Syncronize Penerimaan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, synchronize!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#loader').show();
                    $.ajax({
                        type: "GET",
                        dataType: "json",
                        data: {_token: '{{ csrf_token() }}',idSysHdr:syncIDArray,url:'{{$url2}}'},
                        url: "{{ route('transaksi.tbp.sync.json.sts.detail') }}",
                        success: function(data) {
                            if(data.status) {
                                var dataHtml='';
                                var totalHtml='';
                                var total=0;
                                console.log(data);
                                $.each(data.collections, function(key, value) {
                                    dataHtml += `<tr>
                                                <td>${value.kegiatan.subkegiatan_kode}</td>
                                                <td>${value.source_rekening.kode_rekening}</td>
                                                <td>${value.source_rekening.uraian}</td>
                                                <td>${new Intl.NumberFormat("en-Id").format(value.total)}</td>
                                            </tr>`;
                                });
                                $('#rincianTbp').html(dataHtml);
                                $.each(data.total, function(key, value) {
                                    total = value.total;
                                });
                                totalHtml=`<tr>
                                                <th colspan="3" style="text-align:right">Total:</th>
                                                <th>${new Intl.NumberFormat("en-Id").format(total)}</th>
                                            </tr>`;
                                $('#totalRincian').html(totalHtml);
                                $('#loader').hide();
                                $('.process_all').removeAttr("disabled");
                                $('#modal_process_tbp #jenis_pembayaran').val(data.collectionHdr);
                                $('#modal_process_tbp #tgl_tbp').val(data.tgl_tbp);
                                $('#modal_process_tbp #id_skpd').val(data.id_skpd);
                                $('#modal_process_tbp #id_hdr').val(syncIDArray);
                                $('#modal_process_tbp #uraian_input').val('');
                                $('#modal_process_tbp #uraian_input');
                                $('#modal_process_tbp').find('.modal-dialog').addClass('modal-lg');
                                $('#modal_process_tbp').modal('show');
                            }else{
                                $('#loader').hide();
                                toastr.error(data.message);
                                $('.process').removeAttr("disabled");
                            }
                        },
                        error: function(data) {
                            $('#loader').hide();
                            toastr.error('Tidak berhasil synchronize Tbp to Sts')
                            $('.process').removeAttr("disabled");
                            console.log(data);
                        }
                    });
                }else{}
            });
        })

        $('body').on('click','.process', function(){
            let id_skpd=$(this).data('id_skpd'),
            tgl=$(this).data("tgl"),
            bayar=$(this).data("id_bayar"),
            id_hdr=$(this).data("id_hdr");
            let url2 =  btoa(id_skpd+"&"+tgl+"&"+id_hdr);
        Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Untuk Syncronize Penerimaan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, synchronize!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.process').attr("disabled","disabled");
                    $('#loader').show();
                    $('#modal_process_tbp #jenis_pembayaran').val(bayar);
                    $('#modal_process_tbp #id_skpd').val(id_skpd);
                    $.ajax({
                        type: "GET",
                        dataType: "json",
                        data: {_token: '{{ csrf_token() }}', id_skpd:id_skpd, jenis_pembayaran:bayar,tgl:tgl,url:url2},
                        url: "{{ route('transaksi.tbp.sync.json.sts.detail') }}",
                        success: function(data) {
                            var dataHtml='';
                            var totalHtml='';
                            var total=0;
                            console.log(data);
                            $.each(data.collections, function(key, value) {
                                dataHtml += `<tr>
                                            <td>${value.kegiatan.subkegiatan_kode}</td>
                                            <td>${value.source_rekening.kode_rekening}</td>
                                            <td>${value.source_rekening.uraian}</td>
                                            <td>${new Intl.NumberFormat("en-Id").format(value.total)}</td>
                                          </tr>`;
                            });
                            $('#rincianTbp').html(dataHtml);
                            $.each(data.total, function(key, value) {
                                total = value.total;
                            });
                            totalHtml=`<tr>
                                            <th colspan="3" style="text-align:right">Total:</th>
                                            <th>${new Intl.NumberFormat("en-Id").format(total)}</th>
                                        </tr>`;
                            $('#totalRincian').html(totalHtml);
                            $('#loader').hide();
                            $('.process').removeAttr("disabled");
                            $('#modal_process_tbp #tgl_tbp').val(data.tgl_tbp);
                            $('#modal_process_tbp #id_hdr').val(id_hdr);
                            $('#modal_process_tbp #uraian_input').val('');
                            $('#modal_process_tbp #uraian_input');
                            $('#modal_process_tbp').find('.modal-dialog').addClass('modal-lg');
                            $('#modal_process_tbp').modal('show');
                        },
                        error: function(data) {
                            $('#loader').hide();
                            toastr.error('Tidak berhasil synchronize Tbp to Sts')
                            $('.process').removeAttr("disabled");
                            console.log(data);
                        }
                    });
                }else{}
            });
        })

        $('#modal_process_tbp').on('shown.bs.modal', function () {
            $('body #modal_process_tbp #sts_tgl').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                immediateUpdates: true,
                todayBtn: true,
                todayHighlight: true,
                container:"#modal_process_tbp"
            }).datepicker("setDate", "0");
        });

        // $('body #sts_tgl').on('change', function(){
        //     let oldDate = $(this).data('tbp_tgl'),
        // })
    })


</script>
@endsection
