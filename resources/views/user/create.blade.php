@extends('layouts.master-layouts')
@section('title')
    @lang('translation.bendahara')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('pagetitle') Bendahara @endslot
        @slot('title') Tambah Bendahara @endslot
    @endcomponent
    <div class="row">
        <div class="card col-6">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mt-5 mt-lg-4">
                            <h5 class="mb-4 font-size-14">
                                Nama Dinas : {{$skpd->nama_skpd}}
                            </h5>
                            <form>
                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Nama Bendahara</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2" name="id_bendahara">
                                            <option value="">Silahkan pilih bendahara</option>
                                            @foreach ($users as $user )
                                                <option value="{{$user->id}}">{{$user->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row justify-content-end">
                                    <div class="col-sm-9">
                                        <div>
                                            <input type="hidden" value="{{$skpd->id}}" name="id_skpd">
                                            <button type="submit" class="btn btn-primary w-md">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script>
        function ($) {
            "use strict";
            $(".select2").select2();
        }
    </script>
@endsection
