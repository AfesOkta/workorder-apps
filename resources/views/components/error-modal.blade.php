@extends('layouts.master')
@section('title')
    @lang('Akses')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
    <style>

    </style>
@endsection
@section('content')
<div class="modal" id="error" role="dialog" aria-labelledby="composemodalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="text-2xl">{{$pesan}}</h4>
            </div>
            <div class="modal-footer">
                <a href="{{url('/')}}" class="btn btn-outline-secondary float-lg-right">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        $('#error').show();
    </script>
@endsection
