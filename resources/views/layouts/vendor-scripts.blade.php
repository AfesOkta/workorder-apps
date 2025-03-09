<!-- JAVASCRIPT -->
<script src="{{ asset('/assets/libs/jquery/jquery.min.js')}}"></script>
<script src="{{ asset('/assets/libs/bootstrap/bootstrap.min.js')}}"></script>
<script src="{{ asset('/assets/libs/metismenu/metismenu.min.js')}}"></script>
<script src="{{ asset('/assets/libs/simplebar/simplebar.min.js')}}"></script>
<script src="{{ asset('/assets/libs/node-waves/node-waves.min.js')}}"></script>
<script src="{{ asset('/assets/libs/waypoints/waypoints.min.js')}}"></script>
<script src="{{ asset('/assets/libs/jquery-counterup/jquery-counterup.min.js')}}"></script>
<script src="{{ asset('/assets/libs/feather-icons/feather-icons.min.js')}}"></script>
<script src="{{ asset('/assets/js/loader.js')}}"></script>
<!-- datatables-->
<script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
<!-- init js -->
<script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>

@yield('script')
<!-- App js -->
<script src="{{ asset('/assets/js/app.min.js')}}"></script>

@yield('script-bottom')
