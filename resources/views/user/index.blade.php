@extends('layouts.master-layouts')
@section('title')
    @lang('translation.user')
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
        @slot('pagetitle') User @endslot
        @slot('title') Data User @endslot
    @endcomponent


    <div class="row">
        <div class="col-md-12">
            <div class="float-right form-group">
                @can('create-user', Skpd::class)
                    <button class="btn btn-primary" data-toggle="modal" id="addUserBtn">Tambah</button>
                @endcan

                @can('upload-user', Skpd::class)
                    <button class="btn btn-outline-primary" data-toggle="modal" data-target="#upload_user">Upload User</button>
                @endcan
            </div>
            <br>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable" class="table data-table dt-responsive nowrap w-100" width="100%">
                        <thead>
                            <tr>
                                <th width="2%">No</th>
                                <th width="70%">Name</th>
                                <th width="23%">Username</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            {{-- @forelse ($users as $value)
                                <tr>
                                    <td>{{strtoupper($value->name)}}</td>
                                    <td>{{$value->username}}</td>
                                    <td>
                                        @can('edit-role',Role::class)
                                        <a href="#" data-type="text" data-pk="{{$value->id}}" class="btn btn-warning" data-url="" data-title="Edit role">Edit</a>
                                        @endcan
                                        @can('delete-role',Role::class)
                                        <a href="#" data-type="text" data-pk="{{$value->id}}" class="btn btn-danger" data-url="" data-title="Delete role">Delete</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">{{__('Data tidak ditemukan')}}</td>
                                </tr>
                            @endforelse --}}
                        </tbody>
                    </table>

                </div>
                <!-- end card-body -->
            </div>
            <!-- end card -->
        </div> <!-- end col -->
    </div> <!-- end row -->

    <div class="modal fade" id="add_user" tabindex="-1" role="dialog" aria-labelledby="composemodalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 id="modelHeading">Tambah/Edit User</h4>
                    <br>
                    <form autocomplete="false" class="form-group" method="post" action="#" enctype="multipart/form-data" id="formUser">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="formrow-firstname-input ">Nama Lengkap: <span class="text-danger">*</span></label> &nbsp;
                                <input name="name" id="name" class="form-control" type="text" value="{{old('kd_skpd')}}" placeholder="Nama Lengkap">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="formrow-firstname-input ">Email : <span class="text-danger">*</span></label> &nbsp;
                                <input name="email" id="email" class="form-control" type="email" value="{{old('kode_skpd')}}" placeholder="Email">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formrow-firstname-input ">NIP : <span class="text-danger">*</span></label> &nbsp;
                                <input name="nip" id="nip" class="form-control" type="text" value="{{old('kode_skpd')}}" placeholder="NIP">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formrow-firstname-input ">Jabatan : </label> &nbsp;
                                <input name="jabatan" id="jabatan" class="form-control" type="text" value="{{old('kode_skpd')}}" placeholder="Jabatan">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formrow-firstname-input ">NPWP : </label> &nbsp;
                                <input name="npwp" id="npwp" class="form-control" type="text" value="{{old('kode_skpd')}}" placeholder="NPWP">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formrow-firstname-input ">Nama Bank : </label> &nbsp;
                                <input name="nama_bank" id="nama_bank" class="form-control" type="text" value="{{old('kode_skpd')}}" placeholder="Nama Bank">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="formrow-firstname-input ">No. Rekening : </label> &nbsp;
                                <input name="no_rekening" id="no_rekening" class="form-control" type="text" value="{{old('kode_skpd')}}" placeholder="No. Rekening">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="formrow-firstname-input ">Username : <span class="text-danger">*</span></label> &nbsp;
                                <input name="username" id="username" class="form-control" type="text" value="{{old('kode_skpd')}}" placeholder="Username">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="formrow-firstname-input ">Role : <span class="text-danger">*</span></label> &nbsp;
                                <select class="form-control select2" name="roles" id="roles">
                                    <option value="">Silahkan pilih roles</option>
                                    @foreach ($roles as $role )
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="formrow-firstname-input ">Tanda Tangan : <span class="text-danger">*</span></label> &nbsp;
                                <input name="image" id="image" type="file" class="form-control" onchange="readURL(this);">
                            </div>
                            <div class="form-group col-md-6">
                                <img id="img_ttd" src="#" alt="ttd_user" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="formrow-firstname-input ">Alamat : </label> &nbsp;
                                <input name="nama_skpd" id="nama_skpd" class="form-control" type="text" value="{{old('kode_skpd')}}" placeholder="Alamat">
                            </div>
                            <div class="form-group col-md-12">
                                <input type="hidden" id="userId" name="userId">
                                <button type="submit" class="btn btn-success" id="saveBtn">Tambah</button>
                                <button type="button" class="btn btn-outline-secondary float-lg-right" data-dismiss="modal">Kembali</button>
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
    {{-- <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script> --}}
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#addUserBtn').click(function () {
                $('#saveBtn').val("create-user");
                $('#userId').val('');
                $('#formUser').trigger("reset");
                $('#modelHeading').html("Create New User");
                $('#add_user').modal('show');
            });

            $('.data-table').DataTable().destroy();
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('settings.users.json') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'username', name: 'username'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            // $('form#saveBtn').click(function (e) {
            //     e.preventDefault();
            //     $(this).html('Sending..');

            //     $.ajax({
            //         data: $('#formUser').serialize(),
            //         url: "{{ route('settings.users.store') }}",
            //         type: "POST",
            //         dataType: 'json',
            //         success: function (data) {

            //             $('#formUser').trigger("reset");
            //             $('#add_user').modal('hide');
            //             table.draw();

            //         },
            //         error: function (data) {
            //             console.log('Error:', data);
            //             $('#saveBtn').html('Save Changes');
            //         }
            //     });
            // });

            $("form#formUser").submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('settings.users.store') }}",
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        $('#formUser').trigger("reset");
                        $('#add_user').modal('hide');
                        table.draw();
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });

            $('body').on('click', '.editUser', function () {
                var transactionId = $(this).data('id');
                $.get("{{ route('settings.users') }}" +'/' + transactionId +'/edit', function (data) {
                    $('#modelHeading').html("Edit User");
                    $('#saveBtn').html("Edit");
                    $('#add_user').modal('show');
                    $('#userId').val(data.user.id);
                    $('#name').val(data.user.name);
                    $('#username').val(data.user.username);
                    $('#email').val(data.user.email);
                    $('#nip').val(data.user.nip);
                    $('#jabatan').val(data.user.jabatan);
                    $('#nama_bank').val(data.user.nama_bank);
                    $('#no_rekening').val(data.user.no_rekening);
                    $('#roles').change().val(data.userRole.id);
                    $('#alamat').val(data.user.alamat);
                })
            });

            $('body').on('click', '.deleteUser', function () {
                var userId = $(this).data("id");
                confirm("Are You sure want to delete !");

                $.ajax({
                    type: "post",
                    data: {'id':userId, _token: '{{ csrf_token() }}'},
                    url: "{{ route('settings.users.delete') }}",
                    success: function (data) {
                        table.draw();
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            });
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                $('#img_ttd').attr('src', e.target.result).width(150).height(200);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
