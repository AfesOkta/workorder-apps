<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{route('dashboard')}}" class="logo logo-dark">
            <span class="logo-sm" style="color: white; font-size: large; font-weight: 900;">
                <img src="{{ asset('/assets/images/logo.png') }}" alt="" height="40px"> {{Str::limit(config('app.name','Erevenue'),1)}}
            </span>
            <span class="logo-lg" style="color: white; font-size: large; font-weight: 900;">
                <img src="{{ asset('/assets/images/logo.png') }}" alt="" height="60"> {{config('app.name','Erevenue')}}
            </span>
        </a>

        <a href="{{route('dashboard')}}" class="logo logo-light">
            <span class="logo-sm" style="color: white; font-size: large; font-weight: 900;">
                <img src="{{ asset('/assets/images/logo.png') }}" alt="" height="40px"> {{Str::limit(config('app.name','Erevenue'),1)}}
            </span>
            <span class="logo-lg" style="color: white; font-size: large; font-weight: 900;">
                <img src="{{ asset('/assets/images/logo.png') }}" alt="" height="60"> {{config('app.name','Erevenue')}}
            </span>
        </a>
    </div>

    <button type="button" class="px-3 btn btn-sm font-size-16 header-item waves-effect vertical-menu-btn">
        <i class="fa fa-fw fa-bars"></i>
    </button>

    <div data-simplebar class="sidebar-menu-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">@lang('translation.Menu')</li>

                <li>
                    <a href="{{route('dashboard')}}" class="waves-effect">
                        <i class="icon nav-icon" data-feather="home"></i>
                        <span class="menu-item">@lang('translation.Dashboard')</span>
                    </a>
                </li>
                @can('show-master',User::class)
                <li class="menu-title">@lang('translation.master_data')</li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="icon nav-icon" data-feather="layout"></i>
                        <span class="menu-item">@lang('translation.master')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('show-tahapan',User::class)
                        <li><a href="{{route('master.tahapan')}}">@lang('translation.tahapan')</a></li>
                        @endcan
                        @can('show-kegiatan',User::class)
                        <li><a href="{{route('master.kegiatan')}}">@lang('translation.kegiatan')</a></li>
                        @endcan
                        @can('show-rekening',User::class)
                        <li><a href="{{route('master.rekening')}}">@lang('translation.rekening')</a></li>
                        @endcan
                        @can('show-anggaran',User::class)
                        <li><a href="{{route('master.anggaran')}}">@lang('translation.anggaran')</a></li>
                        @endcan
                        @can('show-skpd',User::class)
                        <li><a href="{{route('master.skpd')}}">@lang('translation.skpd')</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @can('show-input-data',User::class)
                <li class="menu-title">@lang('translation.input_data')</li>
                <li>
                    @can('create-tbp',User::class)
                    <a href="{{route('transaksi.tbp.tambah')}}" class="waves-effect">
                        <i class="icon nav-icon" data-feather="file"></i>
                        <span class="menu-item">@lang('translation.input_tbp')</span>
                    </a>
                    @endcan
                </li>
                @endcan
                @can('show-transaction',User::class)
                <li class="menu-title">Data Transaksi</li>
                @can('show-tbp',TBP::class)
                <li>
                    <a href="javascript:void(0)" class="has-arrow waves-effect">
                        <i class="icon nav-icon" data-feather="database"></i>
                        <span class="menu-item">@lang('translation.tbp')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('show-tbp',TBP::class)
                            <li><a href="{{route('transaksi.tbp.index')}}">@lang('translation.tbp')</a></li>
                        @endcan
                        @can('show-tbp-batal',TBP::class)
                            <li><a href="{{route('transaksi.tbp.index.batal')}}">@lang('translation.tbp_batal')</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="icon nav-icon" data-feather="database"></i>
                        <span class="menu-item">@lang('translation.sts')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('show-sts',STS::class)
                        <li><a href="{{route('transaksi.sts.index')}}">@lang('translation.sts')</a></li>
                        @endcan
                        @can('show-sts-batal',STS::class)
                        <li><a href="{{route('transaksi.sts.index.batal')}}">@lang('translation.sts_batal')</a></li>
                        @endcan
                    </ul>
                </li>
                @can('show-bku',TBP::class)
                <li>
                    <a href="{{route('transaksi.bku.index')}}" class="waves-effect">
                        <i class="icon nav-icon" data-feather="database"></i>
                        <span class="menu-item">@lang('translation.bku')</span>
                    </a>
                    {{-- <ul class="sub-menu" aria-expanded="false">
                        @can('show-bku',TBP::class)
                        <li><a href="{{route('transaksi.bku.index')}}">@lang('translation.bku')</a></li>
                        @endcan
                        @can('synchronize-bku',TBP::class)
                        <li><a href="layouts-horizontal">@lang('translation.synchronize_bku')</a></li>
                        @endcan
                        @can('create-bku-manual',TBP::class)
                        <li><a href="layouts-horizontal">@lang('translation.manual_bku')</a></li>
                        @endcan
                        @can('create-bku-tertunda',TBP::class)
                        <li><a href="layouts-horizontal">@lang('translation.tertunda_bku')</a></li>
                        @endcan
                    </ul> --}}
                </li>
                @endcan
                @endcan
                @can('show-control-data',User::class)
                <li class="menu-title">Kontrol Data</li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="icon nav-icon" data-feather="printer"></i>
                        <span class="menu-item">@lang('translation.laporan')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('show-register-sts',TBP::class)
                        <li><a href="{{route('kontrol-data.register.index')}}">@lang('translation.register_sts')</a></li>
                        @endcan
                        @can('show-realisasi-sts',TBP::class)
                        <li><a href="{{route('kontrol-data.realisasi.index')}}">@lang('translation.realisasi_sts')</a></li>
                        @endcan
                        @can('show-bku',TBP::class)
                        <li><a href="{{route('kontrol-data.bku.index',["source=0"])}}">@lang('translation.laporan_bku')</a></li>
                        @endcan
                        @can('show-bku-tunai',TBP::class)
                        <li><a href="{{route('kontrol-data.bku.index',["source=1"])}}">@lang('translation.laporan_bku_tunai')</a></li>
                        @endcan
                        @can('show-bku-transfer',TBP::class)
                        <li><a href="{{route('kontrol-data.bku.index',["source=2"])}}">@lang('translation.laporan_bku_transfer')</a></li>
                        @endcan
                        @can('show-fungsional',TBP::class)
                        <li><a href="{{route('kontrol-data.fungsional.index',["source=1"])}}">@lang('translation.laporan_fungsional')</a></li>
                        @endcan
                        @can('show-administrasi',TBP::class)
                        <li><a href="{{route('kontrol-data.fungsional.index',["source=2"])}}">@lang('translation.laporan_administrasi')</a></li>
                        @endcan
                        @can('show-rekapitulasi',TBP::class)
                        <li><a href="pages-comingsoon">@lang('translation.laporan_rekapitulasi')</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @can('show-settings',User::class)
                <li class="menu-title">Setting</li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="icon nav-icon" data-feather="settings"></i>
                            <span class="menu-item">@lang('translation.settings')</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            {{-- @can('show-permission',Role::class)
                            <li><a href="{{url('settings/permission')}}">@lang('translation.permission')</a></li>
                            @endcan --}}
                            @can('show-role',Role::class)
                            <li><a href="{{url('settings/roles')}}">@lang('translation.roles')</a></li>
                            @endcan
                            @can('show-users',User::class)
                            <li><a href="{{url('settings/users')}}">@lang('translation.users')</a></li>
                            @endcan
                            <div class="divider"></div>
                            <li><a href="{{url('settings/configs')}}">@lang('translation.config')</a></li>
                        </ul>
                    </li>
                @endcan
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
