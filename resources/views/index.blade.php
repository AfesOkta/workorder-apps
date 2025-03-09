@extends('layouts.master-layouts')
@section('content')
    <div class="row">
        {{-- @component('components.dashboard.product_graph_widget')
            @slot('icon') archive @endslot
            @slot('title') TBP @endslot
            @slot('rating') 5.0 @endslot
            @slot('value') {{$tbpActived ?? 0}} @endslot
            @slot('price') $250.00 - $650.00 @endslot
            @slot('chart') sparkline-chart-1 @endslot
            @slot('url') {{route('transaksi.tbp.index')}} @endslot
        @endcomponent
        @component('components.dashboard.product_graph_widget')
            @slot('icon') delete @endslot
            @slot('title') TBP BATAL @endslot
            @slot('rating') 4.9 @endslot
            @slot('value') {{$tbpBatal ?? 0}} @endslot
            @slot('price') $36.00 - $75.00 @endslot
            @slot('chart') sparkline-chart-2 @endslot
            @slot('url') {{route('transaksi.tbp.index.batal')}} @endslot
        @endcomponent
        @component('components.dashboard.product_graph_widget')
            @slot('icon') archive @endslot
            @slot('title') STS @endslot
            @slot('rating') 5.0 @endslot
            @slot('value') {{$stsActived ?? 0}} @endslot
            @slot('price') $160.00 - $720.00 @endslot
            @slot('chart') sparkline-chart-3 @endslot
            @slot('url') {{route('transaksi.sts.index')}} @endslot
        @endcomponent
        @component('components.dashboard.product_graph_widget')
            @slot('icon') delete @endslot
            @slot('title') STS BATAL @endslot
            @slot('rating') 4.8 @endslot
            @slot('value') {{$stsBatal ?? 0}} @endslot
            @slot('price') $500.00 - $1200.00 @endslot
            @slot('chart') sparkline-chart-4 @endslot
            @slot('url') {{route('transaksi.sts.index.batal')}} @endslot
        @endcomponent

        @component('components.dashboard.product_graph_widget')
            @slot('icon') archive @endslot
            @slot('title') BKU @endslot
            @slot('rating') 4.8 @endslot
            @slot('value') {{$bku ?? 0}} @endslot
            @slot('price') $500.00 - $1200.00 @endslot
            @slot('chart') sparkline-chart-4 @endslot
            @slot('url') {{route('transaksi.bku.index')}} @endslot
        @endcomponent --}}
    </div>
@endsection
