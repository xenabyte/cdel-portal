<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">


<head>

    <meta charset="utf-8" />
    <title>Token Error | {{ env('APP_NAME') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ env('APP_NAME') }} Token" name="description" />
    <meta content="Oladipo Damilare(KoderiaNG)" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('favicon.png')}}">

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


</head>

<body>

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper py-5 d-flex justify-content-center align-items-center min-vh-100">

        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden p-0">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-4 text-center">
                        <div class="error-500 position-relative">
                            <img src="assets/images/error500.png" alt="" class="img-fluid error-500-img error-img" />
                            <h1 class="title text-muted">Error</h1>
                        </div>
                        <div>
                            <h4>Browser Token Expired!</h4>
                            <div class="text-muted text-start w-75 mx-auto">
                                <p>Please try refreshing the page. If the problem persists, you may want to try the following:</p>
                                <ul>
                                    <li>Click on the Home link in the navigation.</li>
                                    <li>Look for the portal link you want to navigate to.</li>
                                </ul>
                            </div>
                            <a href="{{ url('/') }}" class="btn btn-success"><i class="mdi mdi-home me-1"></i>Back to home</a>
                        </div>
                    </div><!-- end col-->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth-page content -->
    </div>
    <!-- end auth-page-wrapper -->

</body>


</html>