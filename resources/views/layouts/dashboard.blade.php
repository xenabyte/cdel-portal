
<!doctype html>
<html lang="en" data-layout="horizontal" data-layout-style="default" data-layout-position="fixed" data-topbar="dark" data-sidebar="dark" data-sidebar-size="lg" data-layout-width="fluid" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>{{ env('APP_NAME') }} </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta content="{{ env('APP_NAME') }} Dashboard" name="description" />
    <meta content="Oladipo Damilare(KoderiaNG)" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('favicon.png')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">


    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    

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
    <script src="{{ env('CKEDITOR_CDN') }}"></script>
    <script>
        // Select all textarea elements and initialize CKEditor on each
        document.querySelectorAll('ckeditor').forEach((textarea) => {
            CKEDITOR.replace(textarea);
        });
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
   
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/6672b07219f6c616eadbe18d/1i0o02fu8';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
    </script>
    <!--End of Tawk.to Script-->
</head>

<body>
    @include('sweetalert::alert', ['cdn' => "https://cdn.jsdelivr.net/npm/sweetalert2@9/dist/sweetalert2.all.min.js"])
    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="{{ env('WEBSITE_URL') }}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="50">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="200">
                                </span>
                            </a>

                            <a href="{{ env('WEBSITE_URL') }}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="50">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="200">
                                </span>
                            </a>
                        </div>

                        <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none" id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>

                    </div>

                    <div class="d-flex align-items-center">
                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" data-toggle="fullscreen">
                                <i class='bx bx-fullscreen fs-22'></i>
                            </button>
                        </div>

                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                                <i class='bx bx-moon fs-22'></i>
                            </button>
                        </div>

                        @if(!empty($student))
                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user" src="{{asset(empty($student->image)?'assets/images/users/user-dummy-img.jpg':$student->image)}}" alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ $student->applicant->lastname.' '.$student->applicant->othernames }}</span>
                                        <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">{{ $student->matric_number }}</span>
                                    </span>
                                </span>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </header>


        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="{{url('admin/home')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="50">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="200">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{ env('WEBSITE_URL') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="50">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="200">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">

                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="nav-item"></li>
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                   @yield('content')

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © {{ env('APP_NAME') }}.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Design & Develop by TAU ICT
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

<!-- removeNotificationModal -->
<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Are you sure ?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

     <!-- JAVASCRIPT -->
    <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>
    <script src="{{asset('assets/libs/feather-icons/feather.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
    {{-- <script src="{{asset('assets/js/plugins.js')}}"></script> --}}

    <script src="{{ asset('assets/js/lga.min.js') }}"></script>
    <!-- form wizard init -->
    <script src="{{ asset('assets/js/pages/form-wizard.init.js') }}"></script>
    <!-- App js -->
    <script src="{{asset('assets/js/app.js')}}"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const validateButton = document.querySelector('.validate-button');
            const hiddenFields = document.querySelector('.hidden-fields');
            const studentIdInput = document.querySelector('#student_id');
            const matricNumberInput = document.querySelector('#matricNumber');

            hiddenFields.style.display = 'none';
    
            validateButton.addEventListener('click', function () {
                const matricNumber = document.querySelector('#matricNumber').value;

                if(matricNumber === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Matric Number is required',
                        text: 'Fill in your matric number',
                    });

                    return false;
                }
    
                // Send a POST request to the Laravel route
                axios.post('/student/getStudent', { matric_number: matricNumber })
                    .then(function (response) {
                        if (response.data.status === 'record_not_found') {
                            // Show a SweetAlert for record not found
                            Swal.fire({
                                icon: 'error',
                                title: 'Record Not Found',
                                text: 'The student record was not found.',
                            });
                        } else {
                            // Set the student ID and show the hidden fields
                            studentIdInput.value = response.data.student.id;
                            hiddenFields.style.display = 'flex';
                            validateButton.style.display = 'none';
                            matricNumberInput.setAttribute('disabled', 'disabled');
                        }
                    })
                    .catch(function (error) {
                        console.error(error);
                    });
            });
        });
    </script>
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
    <script>
        // Select all textarea elements and initialize CKEditor on each
        document.querySelectorAll('ckeditor').forEach((textarea) => {
            CKEDITOR.replace(textarea);
        });
    </script>

<script>

    document.getElementById('category').addEventListener('change', function() {
        var selectedCategory = this.value;
        var facultyContainer = document.getElementById('faculty-container');
        var departmentContainer = document.getElementById('department-container');
        var unitContainer = document.getElementById('unit-container');
        
        if (selectedCategory === 'Academic') {
            facultyContainer.style.display = 'block';
            departmentContainer.style.display = 'block';
            unitContainer.style.display = 'none';
        } else {
            facultyContainer.style.display = 'none';
            departmentContainer.style.display = 'none';
            unitContainer.style.display = 'block';
        }
    });

    function handleFacultyChange(event) {
        const selectedFaculty = event.target.value;
        const departmentSelect = $('#department');

        if(selectedFaculty != ''){
            axios.get("{{ url('/getDepartments') }}/"+selectedFaculty)
            .then(function (response) {
                departmentSelect.empty().append($('<option>', {
                    value: '',
                    text: '--Select--'
                }));
                $.each(response.data, function (index, department) {
                    departmentSelect.append($('<option>', {
                        value: department.id,
                        text: department.name
                    }));
                });
            })
            .catch(function (error) {
                console.error("Error fetching departments:", error);
            });
        }else{
            
        }
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#hofTable').DataTable({
            dom: 'Bfrtip',
            pageLength: 20,
            lengthMenu: [ [10, 20, 50, -1], [10, 20, 50, "All"] ],  
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        // Filter functionality
        $('#filter button').on('click', function () {
            var filterValue = $(this).data('filter');
            $('#filter button').removeClass('btn-primary').addClass('btn-secondary');
            $(this).removeClass('btn-secondary').addClass('btn-primary');

            if (filterValue === 'all') {
                table.search('').draw();
            } else {
                table.search(filterValue + ' Level').draw();
            }
        });
    });
</script>
 <script>
    $(document).ready(function() {
        $('.selectWithSearch').select2();
    });
    $(document).ready(function() {
        $('#selectWithSearch').select2();
    });
</script>
</body>

</html>