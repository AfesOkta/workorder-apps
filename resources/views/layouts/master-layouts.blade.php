<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.title-meta')
    @include('layouts.head')
    <style>
        body[data-sidebar=dark] .menu-title {
        color: #f4f5f7;
    }
    @media (min-width: 1200px) {
        body[data-layout="horizontal"] .container-fluid, body[data-layout="horizontal"] .navbar-header {
            max-width: 95% !important;
        }
    }
    </style>

    <style>
        .clickable-card {
            cursor: pointer; /* Changes cursor to a pointer when hovering over the card */
        }
    </style>
</head>

@section('body')
    <body data-layout="horizontal" data-topbar="colored">
@show
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.horizontal')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <!-- Start content -->
                <div class="container-fluid">
                    @yield('content')
                </div> <!-- content -->
            </div>
            @include('layouts.footer')
        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Right Sidebar -->
    @include('layouts.right-sidebar')
    <!-- END Right Sidebar -->

    @include('layouts.vendor-scripts')
</body>

</html>
