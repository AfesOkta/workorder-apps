@extends('layouts.master-layouts')
@section('title')
    @lang('translation.notification')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @include('partials.alert_danger',[
        'data' => 'This is fail alert.'
    ])
    @include('partials.alert_success',[
        'data' => 'This is success.'
    ])

    @component('components.breadcrumb')
        @slot('pagetitle') Notification @endslot
        @slot('title') Data History @endslot
    @endcomponent


    <div class="row">

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable" class="table data-table dt-responsive nowrap w-100" width="100%">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Type</th>
                                <th width="75%">Uraian</th>
                                <th width="10%">Created By</th>
                            </tr>
                        </thead>

                        <tbody>

                        </tbody>
                    </table>

                </div>
                <!-- end card-body -->
            </div>
            <!-- end card -->
        </div> <!-- end col -->
    </div> <!-- end row -->

@endsection
@section('script')
    <!-- datatables-->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- init js -->
    {{-- <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script> --}}
    <script>
        $(function () {

            $('.data-table').DataTable().destroy();
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('settings.notification.json') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'type', name: 'name'},
                    {data: 'data', name: 'data'},
                    {data: 'user.name', name: 'data'},
                ]
            });


        });
    </script>
@endsection
