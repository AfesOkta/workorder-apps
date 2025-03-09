<header id="page-topbar" style="background-image: url(assets/images/hori-nav-bg.png)">
    <div class="navbar-header">
        <div class="d-flex pt-3">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{route('dashboard')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-light-sm.png') }}" alt="" height="85">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-light-sm.png') }}" alt="" height="75">
                    </span>

                    <span class="proportional-font logo-lg" >
                        Erevenue {{ date('Y') }}
                    </span>
                </a>

                <a href="{{route('dashboard')}}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-light-sm.png') }}" alt="" height="85">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-light-sm.png') }}" alt="" height="75">
                    </span>
                    <span class="proportional-font logo-lg">
                        {{config('app.name','WorkOrders')}} {{ date('Y') }}
                    </span>
                </a>
            </div>

            <button type="button" class="px-3 btn btn-sm font-size-16 d-lg-none header-item waves-effect waves-light"
                data-toggle="collapse" data-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>


        </div>

        <div class="d-flex">

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{asset('assets/images/users/avatar-4.jpg')}}"
                        alt="Header Avatar">
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <!-- item-->
                    <a class="dropdown-item" href="#">
                        <i class="mr-1 align-middle uil uil-user-circle font-size-16 text-muted"></i>
                        <span class="align-middle">@lang('translation.View_Profile')</span></a>
                    <a class="dropdown-item d-block" href="#">
                        <i class="mr-1 align-middle uil uil-cog font-size-16 text-muted"></i>
                        <span class="align-middle">@lang('translation.Settings')</span>
                        <span class="mt-1 ml-2 badge badge-soft-success badge-pill">03</span></a>
                    <a class="dropdown-item" href="#">
                        <i class="mr-1 align-middle uil uil-lock-alt font-size-16 text-muted"></i>
                        <span class="align-middle">@lang('translation.Lock_screen')</span></a>
                    <a class="dropdown-item" href="javascript:void();"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="mr-1 align-middle uil uil-sign-out-alt font-size-16 text-muted"></i>
                        <span class="align-middle">@lang('translation.Sign_out')</span></a>
                    <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @include('layouts.topnav')
    </div>
</header>
