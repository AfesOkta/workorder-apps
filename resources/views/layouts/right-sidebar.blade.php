<!-- Right Sidebar -->
<div class="right-bar">
    <div data-simplebar class="h-100">
        <div class="px-3 py-4 rightbar-title">
            <a href="javascript:void(0);" class="float-right right-bar-toggle">
                <i class="mdi mdi-close noti-icon"></i>
            </a>
            <h5 class="m-0">Settings</h5>
        </div>
        <div class="divider"></div>
        <!-- Settings -->
        <hr class="mt-0" />
        <h6 class="mb-0 text-center">Choose Layouts</h6>

        <div class="p-4">
            <div class="mb-2">
                <img src="{{ asset('/assets/images/layouts/layout-1.jpg') }}" class="img-fluid img-thumbnail" alt="">
            </div>
            <div class="mb-3 custom-control custom-switch">
                <input type="checkbox" class="custom-control-input theme-choice" id="light-mode-switch" checked />
                <label class="custom-control-label" for="light-mode-switch">Light Mode</label>
            </div>

            <div class="mb-2">
                <img src="{{ asset('/assets/images/layouts/layout-2.jpg') }}" class="img-fluid img-thumbnail" alt="">
            </div>
            <div class="mb-3 custom-control custom-switch">
                <input type="checkbox" class="custom-control-input theme-choice" id="dark-mode-switch" data-bsStyle="{{ asset('/assets/css/bootstrap-dark.min.css') }}" data-appStyle="{{ asset('/assets/css/app-dark.min.css') }}" />
                <label class="custom-control-label" for="dark-mode-switch">Dark Mode</label>
            </div>

            <div class="mb-2">
                <img src="{{ asset('/assets/images/layouts/layout-3.jpg') }}" class="img-fluid img-thumbnail" alt="">
            </div>
            <div class="mb-5 custom-control custom-switch">
                <input type="checkbox" class="custom-control-input theme-choice" id="rtl-mode-switch" data-appStyle="{{ asset('/assets/css/app-rtl.min.css') }}" />
                <label class="custom-control-label" for="rtl-mode-switch">RTL Mode</label>
            </div>


        </div>
    </div> <!-- end slimscroll-menu-->
</div>
<!-- /Right-bar -->

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>
