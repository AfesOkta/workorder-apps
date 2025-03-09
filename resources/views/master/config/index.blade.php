@extends('layouts.master-layouts')
@section('title')
    @lang('translation.configs')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">

@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('pagetitle') @lang('translation.settings') @endslot
        @slot('title') @lang('translation.config') @endslot
    @endcomponent
    <div class="row">
        <div class="card col-6">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mt-5 mt-lg-4">
                            <form method="POST" action="{{route('settings.configs.store')}}">
                                @csrf
                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Nama Aplikasi</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="appname" id="subname" class="form-control" value="{{@$config->appname ??""}}">
                                    </div>
                                </div>
                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Nama Sub Aplikasi</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="subname" id="subname" class="form-control" value="{{@$config->subname ??""}}">
                                    </div>
                                </div>
                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Skin</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="skin" id="skin" class="form-control" value="{{@$config->skin ??""}}">
                                    </div>
                                </div>
                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Logo</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="logo" id="logo" class="form-control" value="{{@$config->logo ??""}}">
                                    </div>
                                </div>
                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Url Synchronize BJTM</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="url_sync_bjtm" id="url_sync_bjtm" class="form-control" value="{{@$config->url_sync_bjtm ??""}}">
                                    </div>
                                </div>
                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Proxy Synchronize BJTM</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="url_proxy_sync_bjtm" id="url_proxy_sync_bjtm" class="form-control" value="{{@$config->url_proxy_sync_bjtm ??""}}">
                                    </div>
                                </div>
                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Port Proxy Synchronize BJTM</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="port_proxy_sync_bjtm" id="port_proxy_sync_bjtm" class="form-control" value="{{@$config->port_proxy_sync_bjtm ??""}}">
                                    </div>
                                </div>
                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">User Synchronize BJTM</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="user_auth" id="user_auth" class="form-control" value="{{@$config->user_auth ??""}}">
                                    </div>
                                </div>
                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Password Synchronize BJTM</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="pass_auth" id="pass_auth" class="form-control" value="{{@$config->pass_auth ??""}}">
                                    </div>
                                </div>
                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">URL Syncronize VA BJTM</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="url_sync_va_bjtm" id="url_sync_va_bjtm" class="form-control" value="{{@$config->url_sync_va_bjtm ??""}}">
                                    </div>
                                </div>

                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Identity VA BJTM</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="identity_bjtm_va" id="identity_bjtm_va" class="form-control" value="{{@$config->identity_bjtm_va ??""}}">
                                    </div>
                                </div>

                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Identity VA KASDA</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="identity_kasda_va" id="identity_kasda_va" class="form-control" value="{{@$config->identity_kasda_va ??""}}">
                                    </div>
                                </div>

                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">URL Synchronize API Ecounting</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="url_sync_api_ecounting" id="url_sync_api_ecounting" class="form-control" value="{{@$config->url_sync_api_ecounting ??""}}">
                                    </div>
                                </div>

                                <div class="mb-4 form-group row">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Tahapan Active</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2" name="tahapan_id" id="id_tahapan">
                                            @foreach ($tahapans as $tahapan )
                                                <option value="{{$tahapan->id}}" @if((@$config->tahapan_id ??"") == $tahapan->id) selected @endif>{{$tahapan->nama}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row justify-content-end">
                                    <div class="col-sm-9">
                                        <div>
                                            <input type="hidden" name="id" id="config_id" value="{{@$config->id??""}}">
                                            <a href="{{route('dashboard')}}" class="btn btn-outline-primary w-md">Cancel</a>
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
