<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from themewagon.github.io/mega_able/sample-page.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 15 Jul 2025 17:36:34 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
    <title> @yield('title', 'Accueil')</title>
    <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 10]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description"
        content="Mega Able Bootstrap admin template made using Bootstrap 4 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
    <meta name="keywords"
        content="flat ui, admin Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="codedthemes" />
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="theme-color" content="#c20102">
    <meta name="app_url" content="{{ url('/') }}">
    <!-- Favicon icon -->
    <link rel="shortcut icon" href="{{ asset('storage/assets/logo/airid.png') }}" />
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('storage/assets_vendor3/css/bootstrap/css/bootstrap.min.css') }}">
    <!-- waves.css")}} -->
    <link rel="stylesheet" href="{{ asset('storage/assets_vendor3/pages/waves/css/waves.min.css') }}" type="text/css"
        media="all">

        <link rel="stylesheet" href="{{ asset("storage/assets/datatable/datatables.min.css") }}">

        <link rel="stylesheet" href="{{ asset("storage/assets/gigo-master/css/gijgo.min.css") }}">
        <link rel="stylesheet" href="{{ asset("storage/assets/fileinput/css/fileinput.min.css") }}">

    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('storage/assets_vendor3/icon/themify-icons/themify-icons.css') }}">

        <link rel="stylesheet" href="{{ asset("storage/assets/js/bootstrap-select/css/bootstrap-select.min.css") }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('storage/assets_vendor3/icon/font-awesome/css/font-awesome.min.css') }}">
    <!-- Style.css")}} -->
    <link rel="stylesheet" type="text/css" href="{{ asset('storage/assets_vendor3/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('storage/assets_vendor3/css/jquery.mCustomScrollbar.css') }}">


      <link rel="stylesheet" href="{{ asset("storage/assets/css/custom_style.css") }}"/>
</head>

<body>
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="loader-track">
            <div class="preloader-wrapper">
                <div class="spinner-layer spinner-blue">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>

                <div class="spinner-layer spinner-yellow">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>

                <div class="spinner-layer spinner-green">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            @include('partials.nav3')

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">

                    @include('partials.menu3')
                    <div class="pcoded-content">
                        <!-- Page-header start -->
                        @yield('breadcrumb')
                        <!-- Page-header end -->
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">
                                    <div class="page-body">
                                        @yield('content')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div id="styleSelector">

                    </div> --}}
                </div>
            </div>
        </div>
    </div>


   
    <!-- Warning Section Ends -->
    <!-- Required Jquery -->
    {{-- <script type="text/javascript" src="{{ asset('storage/assets/js/jquery.3.7.1.min.js') }}"></script> --}}
    <script type="text/javascript" src="{{ asset('storage/assets_vendor3/js/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset("storage/assets_vendor3/js/jquery-ui/jquery-ui.min.js")}}"></script>
    <script type="text/javascript" src="{{ asset("storage/assets_vendor3/js/popper.js/popper.min.js")}}"></script>
    <script type="text/javascript" src="{{ asset("storage/assets_vendor3/js/bootstrap/js/bootstrap.min.js")}}"></script>
    <!-- waves js -->
    <script src="{{ asset('storage/assets_vendor3/pages/waves/js/waves.min.js') }}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="{{ asset('storage/assets_vendor3/js/jquery-slimscroll/jquery.slimscroll.js') }}">
    </script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{ asset('storage/assets_vendor3/js/SmoothScroll.js') }}"></script>
    <script src="{{ asset('storage/assets_vendor3/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script src="{{ asset('storage/assets_vendor3/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('storage/assets_vendor3/js/vertical-layout.min.js') }}"></script>
    <script src="{{ asset('storage/assets_vendor3/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <!-- Custom js -->
    <script type="text/javascript" src="{{ asset('storage/assets_vendor3/js/script.js') }}"></script>

    <script src="{{ asset("storage/assets/js/bootstrap-select/js/bootstrap-select.min.js") }}"></script>

    <script src="{{ asset("storage/assets/datatable/datatables.min.js") }}"></script>
	<script src="{{ asset('storage/assets/alertifyjs/alertify.min.js') }}"></script>
    <script src="{{ asset('storage/assets/notify/notify.min.js') }}"></script>
    <script src="{{ asset("storage/assets/gigo-master/js/gijgo.min.js") }}"></script>
    <script src="{{ asset("storage/assets/fileinput/js/fileinput.min.js") }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/sumeetghimire/AlertJs/Alert.js"></script>
    


      <script src="{{ asset("storage/assets/js/javascript-custom.js") }}"></script>
      <script src="{{ asset("storage/assets/js/javascript_ajax.js") }}"></script>
</body>


<!-- Mirrored from themewagon.github.io/mega_able/sample-page.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 15 Jul 2025 17:36:34 GMT -->

</html>
