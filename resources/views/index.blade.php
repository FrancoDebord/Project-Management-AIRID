<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from themewagon.github.io/connect-plus/pages/samples/blank-page.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 11 Jul 2025 12:11:50 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title',"AIRID Project Management")</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('storage/assets_vendor/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('storage/assets_vendor/vendors/flag-icon-css/css/flag-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/assets_vendor/vendors/css/vendor.bundle.base.css') }}">

    <link rel="stylesheet" href="{{ asset("storage/assets/bootstrap-5.1.3-dist/css/bootstrap.min.css") }}">
    {{-- <link rel="stylesheet" href="{{ asset("storage/assets/select2/select2.min.css") }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset("storage/assets/select2-bootstrap-theme/select2-bootstrap.min.css") }}"> --}}
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('storage/assets_vendor/css/vertical-light-layout/style.css') }}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('storage/assets/logo/airid.png') }}" />
</head>

<body>
    <div class="container-scroller">
        <!-- partial:../../partials/_navbar.html -->
        @include('partials.navbar')
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:../../partials/_sidebar.html -->
            @include('partials.menu')
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">

                    @yield('content')
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:../../partials/_footer.html -->
                <footer class="footer">
                    <div class="footer-inner-wraper">
                        <div class="d-sm-flex justify-content-center justify-content-sm-between">
                            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright ©
                                {{ date('Y') }} <a href="https://www.airid-africa.com/" target="_blank">AIRID</a>.
                                All
                                rights reserved.
                                {{-- <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted &
                                made with <i class="mdi mdi-heart text-danger"></i></span> --}}
                        </div>
                    </div>
                </footer>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('storage/assets_vendor/vendors/js/vendor.bundle.base.js') }}"></script>
    {{-- <script src="{{ asset("storage/assets/js/jquery.3.7.1.min.js") }}"></script> --}}
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('storage/assets_vendor/js/off-canvas.js') }}"></script>
    <script src="{{ asset('storage/assets_vendor/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('storage/assets_vendor/js/misc.js') }}"></script>
    <script src="{{ asset('storage/assets_vendor/js/settings.js') }}"></script>
    <script src="{{ asset('storage/assets_vendor/js/todolist.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <!-- End custom js for this page -->

    <script src="{{ asset("storage/assets/bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js") }}"></script>
    {{-- <script src="{{ asset("storage/assets/select2/select2.min.js") }}"></script> --}}
    <script src="{{ asset("storage/assets/js/javascript-custom.js") }}"></script>
</body>

<!-- Mirrored from themewagon.github.io/connect-plus/pages/samples/blank-page.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 11 Jul 2025 12:11:50 GMT -->

</html>
