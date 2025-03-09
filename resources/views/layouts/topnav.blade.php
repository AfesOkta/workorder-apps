<div class="topnav">

    <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

        <div class="collapse navbar-collapse" id="topnav-menu-content">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle arrow-none" href="{{route('dashboard')}}">
                        <i class="icon nav-icon" data-feather="home"></i>
                        @lang('translation.Dashboard')
                    </a>
                </li>
                @can('show-input-data',User::class)
                <li class="nav-item dropdown">
                    @can('create-tbp',User::class)
                    <a href="{{route('transaksi.wo.tambah')}}"
                        class="nav-link dropdown-toggle arrow-none">
                        <i class="icon nav-icon" data-feather="file"></i>
                        <span class="menu-item">@lang('translation.input_tbp')</span>
                    </a>
                    @endcan
                </li>
                @endcan
                @can('show-tbp',wo::class)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-uielement" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon nav-icon" data-feather="gift"></i>
                        @lang('translation.penerimaan') <div class="arrow-down"></div>
                    </a>

                    <div class="px-2 dropdown-menu mega-dropdown-menu"
                        aria-labelledby="topnav-uielement">
                        <div class="row">
                            @can('show-tbp',wo::class)
                                <a href="{{route('transaksi.wo.index')}}" class="dropdown-item">@lang('translation.tbp_list')</a>
                            @endcan
                        </div>
                    </div>
                </li>
                @endcan

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon nav-icon" data-feather="server"></i>
                        @lang('translation.laporan') <div class="arrow-down"></div>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="topnav-components">
                        <div class="dropdown">
                            <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-form"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                @lang('translation.penerimaan') <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                <a href="pages-comingsoon"
                                    class="dropdown-item">@lang('translation.laporan_wo')</a>
                            </div>
                        </div>
                    </div>
                </li>
                {{-- @can('show-master',User::class)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-more" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon nav-icon" data-feather="file"></i>
                        @lang('translation.master') <div class="arrow-down"></div>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="topnav-more">
                        @can('show-tahapan',User::class)
                        <a href="{{route('master.tahapan')}}"
                            class="dropdown-item">@lang('translation.products')</a>
                        @endcan
                    </div>
                </li>
                @endcan --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-more" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon nav-icon" data-feather="settings"></i>
                        @lang('translation.settings') <div class="arrow-down"></div>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="topnav-more">
                        <a href="{{url('settings/roles')}}" class="dropdown-item">@lang('translation.roles')</a>
                        <hr/>
                        <a href="{{url('settings/users')}}" class="dropdown-item">@lang('translation.users')</a>

                        {{-- <a href="{{url('settings/configs')}}" class="dropdown-item">@lang('translation.config')</a> --}}
                    </div>
                </li>


            </ul>
        </div>
    </nav>
</div>
