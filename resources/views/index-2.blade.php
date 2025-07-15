<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from demo.dashboardpack.com/admindek-html/default/sample-page.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 15 Jul 2025 16:36:24 GMT -->

<head>
    <title>@yield('title', 'Accueil')</title>
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
        content="Admindek Bootstrap admin template made using Bootstrap 5 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
    <meta name="keywords"
        content="flat ui, admin Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="colorlib" />
    <!-- Favicon icon -->
    <link rel="icon" href="../files/assets/images/favicon.ico" type="image/x-icon">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="{{ asset('storage/assets_vendor2/bootstrap/css/bootstrap.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset("storage/assets/bootstrap-5.1.3-dist/css/bootstrap.min.css") }}"> --}}

    <!-- waves.css -->
    <link rel="stylesheet" href="{{ asset('storage/assets_vendor2/pages/waves/css/waves.min.css') }}" type="text/css"
        media="all">

        {{-- <link rel="stylesheet" href="{{ asset("storage/assets/js/bootstrap-select/css/bootstrap-select.min.css") }}"> --}}
    <!-- feather icon -->
    <link rel="stylesheet" type="text/css" href="{{ asset('storage/assets_vendor2/icon/feather/css/feather.css') }}">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('storage/assets_vendor2/css/style.css') }}">

      <link rel="stylesheet" href="{{ asset("storage/assets/select2/select2.min.css") }}">
    <link rel="stylesheet" href="{{ asset("storage/assets/select2-bootstrap-theme/select2-bootstrap.min.css") }}">

    <link rel="stylesheet" href="{{ asset("storage/assets/css/custom_style.css") }}"/>

    <link rel="shortcut icon" href="{{ asset('storage/assets/logo/airid.png') }}" />
    
</head>

<body>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-bar"></div>
    </div>
    <!-- [ Pre-loader ] end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            <!-- [ Header ] start -->
            @include('partials.nav2')
            <!-- [ Header ] end -->

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <!-- [ navigation menu ] start -->
                    @include('partials.menu2')
                    <!-- [ navigation menu ] end -->
                    <div class="pcoded-content">
                        <!-- [ breadcrumb ] start -->
                        @yield('breadcrumb')
                        <!-- [ breadcrumb ] end -->
                        {{-- <div class="pcoded-inner-content">
							<div class="main-body">
								<div class="page-wrapper">
									<div class="page-body">
										<!-- [ page content ] start -->
										<div class="row">
											<div class="col-sm-12">
												<div class="card">
													<div class="card-header">
														<h5>Hello card</h5>
														<div class="card-header-right">
															<ul class="list-unstyled card-option">
																<li class="first-opt"><i
																		class="feather icon-chevron-left open-card-option"></i>
																</li>
																<li><i class="feather icon-maximize full-card"></i></li>
																<li><i class="feather icon-minus minimize-card"></i>
																</li>
																<li><i class="feather icon-refresh-cw reload-card"></i>
																</li>
																<li><i class="feather icon-trash close-card"></i></li>
																<li><i
																		class="feather icon-chevron-left open-card-option"></i>
																</li>
															</ul>
														</div>
													</div>
													<div class="card-block">
														<p>
															"Lorem ipsum dolor sit amet, consectetur adipiscing elit,
															sed do eiusmod tempor incididunt ut labore et dolore magna
															aliqua. Ut enim ad minim veniam, quis nostrud exercitation
															ullamco laboris nisi ut aliquip ex ea commodo consequat.
															Duis aute irure dolor
															in reprehenderit in voluptate velit esse cillum dolore eu
															fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
															proident, sunt in culpa qui officia deserunt mollit anim id
															est laborum."
														</p>
													</div>
												</div>
											</div>
										</div>
										<!-- [ page content ] end -->
									</div>
								</div>
							</div>
						</div> --}}

                        @yield('content')
                    </div>
                    <!-- [ style Customizer ] start -->
                    {{-- <div id="styleSelector">
					</div> --}}
                    <!-- [ style Customizer ] end -->
                </div>
            </div>
        </div>
    </div>
    <!-- Warning Section Starts -->
    <!-- Older IE warning message -->
    <!--[if lt IE 10]>
    <div class="ie-warning">
        <h1>Warning!!</h1>
        <p>You are using an outdated version of Internet Explorer, please upgrade
            <br/>to any of the following web browsers to access this website.
        </p>
        <div class="iew-container">
            <ul class="iew-download">
                <li>
                    <a href="http://www.google.com/chrome/">
                        <img src="../files/assets/images/browser/chrome.png" alt="Chrome">
                        <div>Chrome</div>
                    </a>
                </li>
                <li>
                    <a href="https://www.mozilla.org/en-US/firefox/new/">
                        <img src="../files/assets/images/browser/firefox.png" alt="Firefox">
                        <div>Firefox</div>
                    </a>
                </li>
                <li>
                    <a href="http://www.opera.com">
                        <img src="../files/assets/images/browser/opera.png" alt="Opera">
                        <div>Opera</div>
                    </a>
                </li>
                <li>
                    <a href="https://www.apple.com/safari/">
                        <img src="../files/assets/images/browser/safari.png" alt="Safari">
                        <div>Safari</div>
                    </a>
                </li>
                <li>
                    <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                        <img src="../files/assets/images/browser/ie.png" alt="">
                        <div>IE (9 & above)</div>
                    </a>
                </li>
            </ul>
        </div>
        <p>Sorry for the inconvenience!</p>
    </div>
    <![endif]-->
    <!-- Warning Section Ends -->
    <!-- Required Jquery -->
    <script type="text/javascript" src="{{ asset('storage/assets_vendor2/jquery/js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('storage/assets_vendor2/jquery-ui/js/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('storage/assets_vendor2/popper.js/js/popper.min.js') }}"></script>
    {{-- <script type="text/javascript" src="{{ asset('storage/assets_vendor2/bootstrap/js/bootstrap.min.js') }}"></script> --}}
    
    <script src="{{ asset("storage/assets/bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js") }}"></script>
    
    <!-- waves js -->
    <script src="{{ asset('storage/assets_vendor2/pages/waves/js/waves.min.js') }}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="{{ asset('storage/assets_vendor2/jquery-slimscroll/js/jquery.slimscroll.js') }}">
    </script>

    {{-- <script src="{{ asset("storage/assets/js/bootstrap-select/js/bootstrap-select.min.js") }}"></script> --}}
    <script src="{{ asset('storage/assets_vendor2/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('storage/assets_vendor2/js/vertical/vertical-layout.min.js') }}"></script>
    <!-- Custom js -->
    <script type="text/javascript" src="{{ asset('storage/assets_vendor2/js/script.min.js') }}"></script>

    <script src="{{ asset("storage/assets/select2/select2.min.js") }}"></script>


     <script src="{{ asset("storage/assets/js/javascript-custom.js") }}"></script>
</body>



</html>
