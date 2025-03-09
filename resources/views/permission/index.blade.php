@extends('layouts.master-layouts')
@section('title')
    @lang('translation.roles')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('pagetitle') Permission @endslot
        @slot('title') Data Permission @endslot
    @endcomponent


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="5%"><input type="checkbox" id="selectAll" disabled="disabled" /></th>
                                <th width="70%">Name</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($roles as $value)
                                <tr>
                                    <td><input type="checkbox" id="selectAll" value="{{value->id}}" /></td>
                                    <td>{{$value->name}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">{{__('Data tidak ditemukan')}}</td>
                                </tr>
                            @endforelse
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
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
