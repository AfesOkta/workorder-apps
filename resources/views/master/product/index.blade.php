@extends('layouts.master-layouts')
@section('title')
    @lang('translation.tahapan')
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
        @slot('pagetitle') Master @endslot
        @slot('title') Data Tahapan @endslot
    @endcomponent


    <div class="row">
        <div class="col-md-12">
            <div class="float-right form-group">
                @can('create-skpd', Skpd::class)
                    {{-- <button class="btn btn-outline-primary" data-toggle="modal" data-target="#add_skpd">Tambah</button> --}}
                @endcan

                @can('sync-tahapan', Skpd::class)
                    <button class="btn btn-primary" data-toggle="modal" data-target="#sync_tahapan">Synchronize Tahapan</button>
                @endcan
            </div>
            <br>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="10%">KODE</th>
                                <th width="85%">URAIAN</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($tahapan as $value)
                                <tr>
                                    <td>{{strtoupper($value->id)}}</td>
                                    <td>{{$value->nama}}</td>
                                    <td>
                                        {{-- @can('create-bendahara',Role::class)
                                        <a href="{{route('master.skpd.create.bendahara',['id'=>$value->id])}}" data-type="text" data-pk="{{$value->id}}" class="btn btn-info" data-title="Show Bendahara">Add Bendahara</a>
                                        @endcan --}}
                                        @can('delete-skpd',Role::class)
                                        <a href="javascript:void(0)" onclick="delete({{$value->id}})" class="btn btn-danger" data-title="Delete role">Delete</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">{{__('Data tidak ditemukan')}}</td>
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

    {{-- <div class="modal fade" id="add_skpd" tabindex="-1" role="dialog" aria-labelledby="composemodalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>Edit SKPD</h4>
                    <br>
                    <form autocomplete="false" class="form-group" method="post" action="{{route('master.skpd.update')}}">
                        @csrf
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group col-md-12">
                                    <label for="formrow-firstname-input ">Kode SKPD: </label> &nbsp;
                                    <input name="kd_skpd" id="kd_skpd" class="form-control" type="text" value="{{old('kd_skpd')}}" placeholder="Kode SKPD" readonly>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="formrow-firstname-input ">Nama SKPD: </label> &nbsp;
                                    <input name="nama_skpd" id="nama_skpd" class="form-control" type="text" value="{{old('kode_skpd')}}" placeholder="Kode SKPD" readonly>
                                </div>
                                <div class="form-group col-md-12">
                                    <button type="submit" class="btn btn-success">Tambah</button>
                                    <button type="button" class="btn btn-outline-secondary float-lg-right" data-dismiss="modal">Kembali</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="modal fade" id="sync_tahapan" tabindex="-1" role="dialog" aria-labelledby="composemodalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>Synchronize Tahapan</h4>
                    <br>
                    <form autocomplete="false" class="form-group" method="post" action="{{route('master.tahapan.synchronize')}}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block">Proces</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary float-lg-right" data-dismiss="modal">Kembali</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!-- datatables-->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
