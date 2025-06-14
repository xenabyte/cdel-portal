<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">


<head>

    <meta charset="utf-8" />
    <title>{{ env('APP_NAME') }} - Student</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ env('APP_NAME') }}" name="description" />
    <meta content="Ogundele Damilare(KoderiaNG)" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('favicon.png')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Layout config Js -->
    <script src="{{asset('assets/js/layout.js')}}"></script>
    <!-- Bootstrap Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet"   type="text/css" />
    <!-- App Css-->
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{asset('assets/css/custom.min.css')}}" rel="stylesheet" type="text/css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10842521152">
    </script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'AW-10842521152');
    </script>
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/618245306885f60a50ba175c/1fjidsi9e';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
    </script>

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
    <!--End of Tawk.to Script-->
    {{-- <a href="{{ url('/admin/logout') }}"onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
    <form id="logout-form" action="{{ url('/admin/logout') }}" method="POST" style="display: none;">@csrf</form> --}}
</head>

<body>
    @include('sweetalert::alert')

    <form id="logout-form" action="{{ url('/applicant/logout') }}" method="POST" style="display: none;">@csrf</form> 
     <!-- auth-page wrapper -->
     <div class="auth-page-wrapper  py-5 d-flex justify-content-center align-items-center min-vh-100">
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden">
                            <div class="row g-0">
                                <div class="col-lg-4">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100" style="background-image:url({{asset('auth_images/auth-one-bg.jpg')}})">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a @auth href="{{ url('/applicant/logout') }}"onclick="event.preventDefault();document.getElementById('logout-form').submit();"  @else href="{{url('/')}}" @endauth class="d-block">
                                                    <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="250">
                                                </a>
                                            </div>
                                            <div class="mt-auto">
                                                {{-- <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-success"></i>
                                                </div> --}}

                                                {{-- <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                    </div>
                                                    <div class="carousel-inner text-center text-white pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">" Great! Clean code, clean design, easy for customization. Thanks very much! "</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" The theme is really great with an amazing customer support."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Great! Clean code, clean design, easy for customization. Thanks very much! "</p>
                                                        </div>j
                                                    </div>
                                                </div> --}}
                                                <!-- end carousel -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->

                                @yield('content')

                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                </div>
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
                        <div class="text-center text-light">
                            <p class="mb-0 text-dark">&copy;
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

    <!-- password-addon init -->
    <script src="{{asset('assets/js/pages/password-addon.init.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
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
