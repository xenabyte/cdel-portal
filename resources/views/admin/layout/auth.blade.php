
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Authentication | {{ env('APP_NAME') }} - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ env('APP_NAME') }} Dashboard" name="description" />
    <meta content="Oladipo Damilare(KoderiaNG)" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('favicon.png')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Layout config Js -->
    <script src="{{asset('assets/js/layout.js')}}"></script>
    <!-- Bootstrap Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{asset('assets/css/custom.min.css')}}" rel="stylesheet" type="text/css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <style>
        .cke_notifications_area{
            display: none;
        }
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(255,255,255,0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-size: 2em;
            color: #333;
        }

        .loading-overlay.active {
            display: flex;
        }
    </style>

</head>

<body>

    @include('sweetalert::alert', ['cdn' => "https://cdn.jsdelivr.net/npm/sweetalert2@9/dist/sweetalert2.all.min.js"])
    
    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" style="background-image:url({{asset('auth_images/auth-one-bg.jpg')}})" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <a href="{{ env('WEBSITE_URL')  }}" class="d-inline-block auth-logo">
                                    <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="200">
                                </a>
                            </div>
                            <p class="mt-3 fs-15 fw-medium">{{ env('APP_NAME') }} Admin Dashboard</p>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                @yield('content')
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                <script>document.write(new Date().getFullYear())</script> {{ env('APP_NAME') }}.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>
    <script src="{{asset('assets/libs/feather-icons/feather.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
    {{-- <script src="{{asset('assets/js/plugins.js')}}"></script> --}}

    <!-- particles js -->
    <script src="{{asset('assets/libs/particles.js/particles.js')}}"></script>
    <!-- particles app js -->
    <script src="{{asset('assets/js/pages/particles.app.js')}}"></script>
    <!-- password-addon init -->
    <script src="{{asset('assets/js/pages/password-addon.init.js')}}"></script>

    <script>
        $(document).ready(function() {
            $("#submit-button").click(function(e) {
                e.preventDefault(); // prevent double submit if needed

                // Disable the button
                $(this).prop("disabled", true);

                // Show the full-screen overlay
                $("#loading-overlay").addClass("active");

                // Submit the form
                $(this.form).submit();
            });
        });
    </script>
</body>

</html>
