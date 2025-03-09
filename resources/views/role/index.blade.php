@extends('layouts.master-layouts')
@section('title')
    @lang('translation.roles')
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
        @slot('pagetitle') Rples @endslot
        @slot('title') Data Roles @endslot
    @endcomponent


    <div class="row">
        <div class="col-md-12">
            <div class="float-right form-group">
                @can('create-role', Role::class)
                    <button class="btn btn-primary" data-toggle="modal" data-target="#add_role">Tambah</button>
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
                                <th width="70%">Name</th>
                                <th width="25%">Guard Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($roles as $value)
                                <tr>
                                    <td>{{strtoupper($value->name)}}</td>
                                    <td>{{$value->guard_name}}</td>
                                    <td>
                                        @can('edit-role',Role::class)
                                        <a href="{{route('settings.roles.show',[base64_encode($value->id)])}}" class="btn btn-warning" data-title="Edit role">Edit</a>
                                        @endcan
                                        @can('delete-role',Role::class)
                                        <a href="javascript:void(0)" onclick="delete({{$value->id}})" class="btn btn-danger" data-title="Delete role">Delete</a>
                                        @endcan
                                        {{-- <a class="btn btn-info" href="javascript:void(0);" onclick="show({{$value->id}})">Show</a> --}}
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


    <div class="modal fade" id="show_permission" tabindex="-1" role="dialog" aria-labelledby="composemodalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                            <h2 class="col-6"> Show Role</h2>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-secondary float-lg-right" data-dismiss="modal">Kembali</button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="m-3 col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <div class="role">
                                    <div class="role_name">
                                        <strong>Name:</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-3 col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Permissions:</strong>
                                <div class="permission">
                                    <div class="permission_lbl">
                                        {{-- @if(!empty($rolePermissions))
                                            @foreach($rolePermissions as $v)
                                                <label class="label label-success">{{ $v->name }},</label>
                                            @endforeach
                                        @endif --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_role" tabindex="-1" role="dialog" aria-labelledby="composemodalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content" style="width:100%">
                <div class="modal-body">
                    <h4>Edit Role</h4>
                    <br>
                    <form autocomplete="false" class="form-group" method="post" action="{{route('settings.roles.update')}}">
                        @csrf
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group col-md-12 role">
                                    <div class="role_name">
                                        <label for="formrow-firstname-input ">Role: </label> &nbsp;
                                        <input name="role" id="role" class="form-control" type="text" value="{{old('role')}}" placeholder="Role Name" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <strong>Permissions:</strong>
                                </div>
                                <div class="row show_permission">
                                    <div class="permission">
                                        @if(!empty($permission))
                                            @foreach($permission as $v)
                                                <div class="mt-2 col-sm-4">
                                                    <h5><input type="checkbox" value="{{$v->id}}" name="permission[]">&nbsp;&nbsp;<span>{{$v->name}}</span></h5>&nbsp;&nbsp;
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <input name="role_id" id="role_id" style="display: none" class="form-control" type="text" value="{{old('role_id')}}" placeholder="Role Name">
                                    <button type="submit" class="btn btn-success">Rubah</button>
                                    <button type="button" class="btn btn-outline-secondary float-lg-right" data-dismiss="modal">Kembali</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_role" tabindex="-1" role="dialog" aria-labelledby="composemodalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>Tambah Role</h4>
                    <hr>
                    <form autocomplete="false" class="form-group" method="post" action="{{route('settings.roles.store')}}">
                        @csrf
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group col-md-12">
                                    <label for="formrow-firstname-input ">Role: </label> &nbsp;
                                    <input name="name" id="name" class="form-control" type="text" value="{{old('role')}}" placeholder="Role Name">
                                </div>
                                <div class="form-group col-md-12">
                                    <strong>Permissions:</strong>
                                </div>
                                <div class="row show_permission">
                                    @if(!empty($permission))
                                        @foreach($permission as $v)
                                            <div class="mt-2 col-sm-4">
                                                <h5><input type="checkbox" value="{{$v->id}}" name="permission[]">&nbsp;&nbsp;<span>{{$v->name}}</span></h5>&nbsp;&nbsp;
                                            </div>
                                        @endforeach
                                    @endif
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
    </div>
@endsection
@section('script')
    <!-- datatables-->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
    <script>
        // var edit = function(id) {
        //     Loader.open();
        //     $.ajax({
        //         url: "{{url('settings/roles/show')}}/"+id,
        //         type: "get",
        //         success: function (res) {
        //             Loader.close();
        //             $('#edit_role #role').val(res['role'].name);
        //             $('#edit_role #role_id').val(res['role'].id);
        //             $('#edit_role .permission').remove();
        //             var myStringArray = res['permission'];
        //             var myRolesPermission = res['rolePermission'];
        //             var arrayLength = myStringArray.length;
        //             let permissionName = "<div class='pl-4 row permission'>";
        //             let checked = false;
        //             for (var i = 0; i < arrayLength; i++) {
        //                 console.log(myStringArray[i]);
        //                 for (var x = 0; x < myRolesPermission.length; x++) {
        //                     if(myRolesPermission[x]['name'] == myStringArray[i]['name']){
        //                         checked = true;
        //                     }
        //                     break;
        //                 }
        //                 if(checked) {
        //                     permissionName +=`<div class="mt-2 col-sm-3">
        //                         <h5><input type="checkbox" checked name="permission[]">&nbsp;&nbsp;<span>${myStringArray[i]['name']}</span></h5>
        //                         </div>`;
        //                 }else{
        //                     permissionName +=`<div class="mt-2 col-sm-3">
        //                     <h5><input type="checkbox" name="permission[]">&nbsp;&nbsp;<span>${myStringArray[i]['name']}</span></h5>
        //                     </div>`;
        //                 }
        //             }
        //             permissionName += '</div>';
        //             $('#edit_role .show_permission').html(permissionName);
        //             $('#edit_role').modal('show');
        //         }
        //     });
        // }

        // var show = function(id) {
        //     Loader.open();
        //     $.ajax({
        //         url: "{{url('settings/roles/show')}}/"+id,
        //         type: "get",
        //         success: function (res) {
        //             Loader.close();
        //             $('#show_permission .role_name').remove();
        //             $('#show_permission .permission_lbl').remove();
        //             let rolename= '<div class="mt-4 role_name"><strong>Role Name: '+res['role'].name+'</strong></div>';
        //             $('#show_permission .role').html(rolename);
        //             var myStringArray = res['rolePermission'];
        //             var arrayLength = myStringArray.length;
        //             let permissionName = "<div class='permission_lbl'>";
        //             for (var i = 0; i < arrayLength; i++) {
        //                 console.log(myStringArray[i]);
        //                 permissionName +=`<input type="checkbox" checked disabled>&nbsp;&nbsp;<span>${myStringArray[i]['name']}</span><br/>`;
        //             }
        //             permissionName += '</div>';
        //             $('#show_permission .permission').html(permissionName);
        //             $('#show_permission').modal('show');
        //         }
        //     });
        // }
    </script>
@endsection
