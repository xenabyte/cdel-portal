<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

    <head>

        <meta charset="utf-8" />
        <title>{{ env('APP_NAME') }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="{{ env('APP_NAME') }} Dashboard" name="description" />
        <meta content="Oladipo Damilare(KoderiaNG)" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('favicon.png')}}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


        <!--Swiper slider css-->
        <link href="{{asset('assets/libs/swiper/swiper-bundle.min.css')}}" rel="stylesheet" type="text/css" />

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
		<!--End of Tawk.to Script-->

    </head>

    <body data-bs-spy="scroll" data-bs-target="#navbar-example">
        @include('sweetalert::alert')
        <!-- Begin page -->
        <div class="layout-wrapper landing">
            <nav class="navbar navbar-expand-lg bg-primary navbar-landing fixed-top job-navbar" id="navbar">
                <div class="container-fluid custom-container">
                    <a class="navbar-brand" href="{{ env('WEBSITE_URL') }}">
                        <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" class="card-logo card-logo-dark" alt="logo dark" height="50">
                        <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" class="card-logo card-logo-light" alt="logo light" height="50">
                    </a>
                    <button class="navbar-toggler py-0 fs-20 text-body" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="mdi mdi-menu"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto mt-2 mt-lg-0" id="navbar-example">
                            <li class="nav-item">
                                <a class="nav-link text-light fs-14 active" href="#hero">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-light fs-14" href="{{ url('student/hallOfFame') }}">Hall of Fame</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-light fs-14" data-bs-toggle="modal" data-bs-target="#exampleModal">Check Bandwidth Balance</a>
                            </li>
                        </ul>

                        <div class="">
                            <a href="{{url('/applicant/register')}}" class="btn btn-soft-light"><i class="ri-user-3-line align-bottom me-1 text-light"></i> Applicant Portal</a>
                            <a href="{{url('/student')}}" class="btn btn-soft-light"><i class="ri-user-3-line align-bottom me-1 text-light"></i> Student Portal</a>
                            <a href="{{url('/guardian')}}" class="btn btn-soft-light"><i class="ri-user-3-line align-bottom me-1 text-light"></i> Guardian Portal</a>
                        </div>
                    </div>

                </div>
            </nav>
            <!-- end navbar -->

            <!-- start hero section -->
            <section class="section job-hero-section bg-light pb-0" id="hero">
                <div class="container">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-lg-6">
                            <div>
                                <h1 class="display-6 fw-semibold text-capitalize mb-3 lh-base">{{ env('APP_NAME') }}</h1>
                                <p class="lead text-muted lh-base mb-4">{{ env('APP_DESCRIPTION') }}</p>

                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-lg-4">
                            <div class="position-relative home-img text-center mt-5 mt-lg-0">
                                <div class="card p-3 rounded shadow-lg inquiry-box">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm flex-shrink-0 me-3">
                                            <div class="avatar-title bg-soft-warning text-warning rounded fs-18">
                                                <i class="ri-rotate-lock-fill"></i>
                                            </div>
                                        </div>
                                        <h5 class="fs-15 lh-base mb-0">Access the university portal</h5>
                                    </div>
                                </div>

                                <img src="{{asset('assets/images/profile-bg3.png')}}" alt="" class="user-img">

                                <div class="circle-effect">
                                    <div class="circle"></div>
                                    <div class="circle2"></div>
                                    <div class="circle3"></div>
                                    <div class="circle4"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </section>
            <!-- end hero section -->


            <!-- start services -->
            <section class="section" id="categories">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="text-center mb-5">
                                <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Explore Our Portals</h1>
                                <p class="text-muted">Discover a variety of portals tailored to your needs. Whether you're looking for job opportunities, academic resources, or community engagement, our portals provide a seamless experience to connect you with the information and services you seek.</p>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row justify-content-center">
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none text-center py-3">
                                <div class="card-body py-4">
                                    <div class="avatar-sm position-relative mb-4 mx-auto">
                                        <div class="job-icon-effect"></div>
                                        <div class="avatar-title bg-transparent text-success rounded-circle">
                                            <i class=" ri-user-add-line fs-1"></i>
                                        </div>
                                    </div>
                                    <a href="{{ url('/applicant/register') }}" class="stretched-link">
                                        <h5 class="fs-17 pt-1">Applicant Portal</h5>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none text-center py-3">
                                <div class="card-body py-4">
                                    <div class="avatar-sm mb-4 mx-auto position-relative">
                                        <div class="job-icon-effect"></div>
                                        <div class="avatar-title bg-transparent text-success rounded-circle">
                                            <i class="ri-account-circle-line fs-1"></i>
                                        </div>
                                    </div>
                                    <a href="{{ url('/student') }}" class="stretched-link">
                                        <h5 class="fs-17 pt-1">Student Portal</h5>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none text-center py-3">
                                <div class="card-body py-4">
                                    <div class="avatar-sm position-relative mb-4 mx-auto">
                                        <div class="job-icon-effect"></div>
                                        <div class="avatar-title bg-transparent text-success rounded-circle">
                                            <i class="ri-user-2-line fs-1"></i>
                                        </div>
                                    </div>
                                    <a href="{{ url('/staff') }}" class="stretched-link">
                                        <h5 class="fs-17 pt-1">Staff Portal</h5>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none text-center py-3">
                                <div class="card-body py-4">
                                    <div class="avatar-sm position-relative mb-4 mx-auto">
                                        <div class="job-icon-effect"></div>
                                        <div class="avatar-title bg-transparent text-success rounded-circle">
                                            <i class="ri-group-2-line fs-1"></i>
                                        </div>
                                    </div>
                                    <a href="{{ url('/guardian') }}" class="stretched-link">
                                        <h5 class="fs-17 pt-1">Parent Portal</h5>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card shadow-none text-center py-3">
                                <div class="card-body py-4">
                                    <div class="avatar-sm position-relative mb-4 mx-auto">
                                        <div class="job-icon-effect"></div>
                                        <div class="avatar-title bg-transparent text-success rounded-circle">
                                            <i class="ri-user-voice-line fs-1"></i>
                                        </div>
                                    </div>
                                    <a href="{{ url('/partner') }}" class="stretched-link">
                                        <h5 class="fs-17 pt-1">Partner Portal</h5>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </section>
            <!-- end services -->


            <!-- start cta -->
            <section class="py-5 bg-primary position-relative">
                <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
                <div class="container">
                    <div class="row align-items-center gy-4">
                        <div class="col-sm">
                            <div>
                                <h4 class="text-white fw-semibold">New Applicant?</h4>
                                <p class="text-white-75 mb-0">Welcome to our university! If you're a new applicant, get started on your academic journey by exploring our programs and admission requirements. We're here to support you at every step of the application process.</p>
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-sm-auto">
                            <a href="{{ url('applicant/register') }}" class="btn btn-danger" type="button">Apply Now <i class="ri-arrow-right-line align-bottom"></i></a>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </section>
            <!-- end cta -->

            <!-- Start footer -->
            <footer class="custom-footer bg-dark py-5 position-relative">
                <div class="container">
                    

                    <div class="row text-center text-sm-start align-items-center mt-5">
                        <div class="col-sm-12">
                            <div>
                                <p class="text-center mb-0">
                                    <script> document.write(new Date().getFullYear()) </script> © {{ env('APP_NAME') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end footer -->

            <!--start back-to-top-->
            <button onclick="topFunction()" class="btn btn-info btn-icon landing-back-top" id="back-to-top">
                <i class="ri-arrow-up-line"></i>
            </button>
            <!--end back-to-top-->

            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">
                        <div class="modal-header p-3">
                            <h5 class="modal-title text-uppercase fw-bold" id="exampleModalLabel">Bandwidth Balance</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <hr>
                        <div class="modal-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="pills-bill-info" role="tabpanel" aria-labelledby="pills-bill-info-tab">
                                    <form class="needs-validation" method="POST" novalidate action="{{ url('checkDataBalance') }}" enctype="multipart/form-data">
                                        @csrf
                        
                                        <div class="col-lg-12 mt-3">
                                            <div class="form-floating">
                                                <input class="form-control" type="text" name="bandwidth_username" id="cusername">
                                                <label for="cpassword">Bandwidth Username</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mt-3">
                                            <div class="form-floating">
                                                <input class="form-control" type="password" name="bandwidth_password" id="cpass">
                                                <label for="cpass">Bandwidth Password</label>
                                            </div>
                                        </div>
            
                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" id='submit-button' type="submit">Check Balance</button>
                                        </div>
            
                                    </form>
                                </div>
                                <!-- end tab pane -->
                            </div>
                            <!-- end tab content -->
                        </div>
                    </div>
                </div>
            </div>
            <!--end modal-->

        </div>
        <!-- end layout wrapper -->


        <!-- JAVASCRIPT -->
        <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>
        <script src="{{asset('assets/libs/feather-icons/feather.min.js')}}"></script>
        <script src="{{asset('assets/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
        <script src="{{asset('assets/js/plugins.js')}}"></script>

        <!--Swiper slider js-->
        <script src="{{asset('assets/libs/swiper/swiper-bundle.min.js')}}"></script>

        <!--job landing init -->
        <script src="assets/js/pages/job-lading.init.js"></script>
    </body>

</html>