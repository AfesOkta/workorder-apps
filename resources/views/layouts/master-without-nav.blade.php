<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.title-meta')
    @include('layouts.head')
    <style>
        body[data-sidebar=dark] .menu-title {
        color: #f4f5f7;
    }
    </style>
</head>

<body class="authentication-bg" style="background-image: url({{ asset('/assets/images/auth-bg.png') }})">
    @yield('content')
    @include('layouts.vendor-scripts')
</body>

</html>
