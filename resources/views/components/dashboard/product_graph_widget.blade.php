<div class="col-md-6 col-xl-3">
    <div class="card clickable-card" onclick="window.location.href='{{ $url }}';">
        <div class="card-body">
            <div class="media align-items-center">
                <div class="avatar-sm mr-3 p-1 border rounded border-soft-primary">
                    <div class="avatar-title rounded bg-soft-primary text-primary">
                        <i class="icon-sm" data-feather="{{ $icon }}"></i>
                    </div>
                </div>
                <div class="media-body">
                    <h5 class="mt-0 mb-1 font-size-24"><span class="text-dark">{{ $title }}</span></h5>
                </div>
            </div>
            <div class="row align-items-center mt-4">
                <div class="col-7">
                    <h6 class="mb-1 font-size-24">{{ $value }}</h6>
                </div>
                <div class="col-5">
                    <div>
                        <div class="apex-charts" id="{{ $chart }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
