@extends('layouts.master-layouts')
@section('title')
    @lang('translation.roles')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb-subtitle')
        @slot('pagetitle') Roles @endslot
        @slot('title') Data Roles @endslot
        @slot('subtitle') Edit Roles @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4>Edit Role</h4>
                    <br>
                    <form autocomplete="false" class="form-group" method="post" action="{{route('settings.roles.update')}}">
                        @csrf
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group col-md-12 role">
                                    <div class="role_name">
                                        <label for="formrow-firstname-input ">Role: </label> &nbsp;
                                        <input name="name" id="role" class="form-control" type="text"
                                            value="{{$data['role']->name}}" placeholder="Role Name" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <strong>Permissions:</strong>
                                </div>
                                <div class="row show_permission">
                                    <div class="pl-4 row">
                                        @if(!empty($data['permission']))
                                            @foreach($data['permission'] as $v)
                                            <div class="mt-2 col-sm-2">
                                                <h5><input type="checkbox" value="{{$v->id}}"
                                                        name="permission[]" {{ in_array($v->id, $data['rolePermission']) ? 'checked' : '' }}>&nbsp;&nbsp;<span>{{$v->name}}</span></h5>
                                                &nbsp;&nbsp;
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <input name="role_id" id="role_id" style="display: none" class="form-control"
                                        type="text" value="{{$data['role']->id}}" placeholder="Role Name">
                                    <button type="submit" class="btn btn-success">Rubah</button>
                                    @can('create-role', Role::class)
                                        <a class="btn btn-outline-info float-lg-right" href="{{route('roles')}}">Kembali</a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
