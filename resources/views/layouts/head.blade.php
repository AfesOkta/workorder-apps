<!-- Bootstrap Css -->
<link href="{{ asset('/assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="{{ asset('/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="{{ asset('/assets/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />
<!-- DataTables -->
<link href="{{ asset ('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
<link href="{{ asset ('/assets/libs/select2/select2.min.css') }}" rel="stylesheet">

<link href="{{ asset('assets/css/custome.css') }}" rel="stylesheet">
<style>
    /* Define the class for proportional font size */
.proportional-font {
    color: white;
    font-size: large;
    /* Adjust font size for medium screens */
    font-family: 'Poppins';
}

/* Optional: Set a range of sizes using clamp() */
.proportional-font-clamp {
    color: white;
    font-size: clamp(24px, 5vw, 48px); /* Font size will vary between 24px and 48px, based on 5vw */
    font-family: 'Poppins';
}

/* Optional: Further fine-tuning with media queries */
@media (min-width: 768px) {
    .proportional-font {
        font-size: large;
        font-family: 'Poppins';
    }
}

@media (min-width: 1200px) {
    .proportional-font {
        font-size: large;
        font-family: 'Poppins';
    }
}
.datepicker {
    z-index: 1050 !important; /* Ensure it appears above other elements */
}

.datepicker-container {
    position: relative;
    width: 100%;
    max-width: 500px;
}
</style>
@yield('css')
