@extends('student.layout.dashboard')
@php
    use \App\Models\ProgrammeCategory;



    $student = Auth::guard('student')->user();
    $applicant = $student->applicant;
    $nok = $applicant->nok;
    $guardian = $applicant->guardian;
    $name = $applicant->lastname.' '.$applicant->othernames;
@endphp
@section('content')
<!-- Toast Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
    <div id="requiredFieldsToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Please fill all required fields before proceeding to the next step.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@if(empty($student->signature) && ($student->programme_category_id == ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::UNDERGRADUATE)))
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="mb-sm-0">Upload signature</h4>
                <div class="flex-shrink-0">
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 offset-md-2 ">
                        <div>
                            <p>
                                Please complete the form below to update your profile and upload your digital signature. 
                                A clear and properly formatted signature is required for various official university documents and processes. 
                            </p>
                        </div>
                        
                        <form class="text-start" action="{{ url('student/profile/saveBioData') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="alert alert-info">
                                <h5 class="alert-heading text-black">Steps to Upload a Signature Without Background</h5>
                                <ol>
                                    <li><strong>Sign on a White Paper</strong> – Use a clean white sheet and sign with a black or dark-colored pen.</li>
                                    <li><strong>Take a Picture</strong> – Use your phone camera to snap a clear and well-lit photo of your signature. Ensure there are no shadows or extra marks.</li>
                                    <li><strong>Remove Background</strong> –  
                                        <ul>
                                            <li>Visit <a href="https://www.remove.bg" target="_blank">remove.bg</a>.</li>
                                            <li>Upload the signature image.</li>
                                            <li>The website will automatically remove the background.</li>
                                        </ul>
                                    </li>
                                    <li><strong>Download the Transparent Image</strong> – Click the download button to save the processed image with a transparent background.</li>
                                    <li><strong>Upload the Signature</strong> – Use the downloaded transparent signature file below (e.g., documents, forms, or digital signing).</li>
                                </ol>
                                <p>This method ensures a clean and professional-looking signature without any unwanted background. 🚀</p>
                            </div>

                            <input type="hidden" name="student_id" value="{{ $student->id }}">

                            <div class="col-lg-12">
                                <div class="form-floating">
                                    <input type="file" class="form-control" id="signature" name="signature">
                                    <label for="signature"></label>
                                </div>
                            </div>
                            <br>
                            <button type="submit" id="submit-button" class="btn btn-block btn-fill btn-primary"> Submit</button>
                        </form>
                    </div>
                </div>

            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
@elseif(empty($student->facebook) && empty($guardian->father_name && empty($applicant->family_position)))
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="mb-sm-0">Update Biodata</h4>
                <div class="flex-shrink-0">
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <form action="{{ url('student/profile/saveBioData') }}" method="POST" class="form-steps" autocomplete="on">
                    @csrf
                    <!-- Header Logo -->
                    <div class="text-center bg-primary mb-4 pt-3 pb-4 mb-1 d-flex justify-content-center">
                        <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" class="card-logo" alt="logo dark" height="100">
                    </div>

                    <!-- Step Navigation -->
                    <div style="display: none" class="step-arrow-nav mb-4">
                        <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="tab-personal-tab" data-bs-toggle="pill" data-bs-target="#tab-personal" type="button" role="tab" aria-selected="true">Personal Info</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="tab-parent-tab" data-bs-toggle="pill" data-bs-target="#tab-parent" type="button" role="tab" aria-selected="false">Parent Info</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="tab-academic-tab" data-bs-toggle="pill" data-bs-target="#tab-academic" type="button" role="tab" aria-selected="false">Academic Info</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="tab-finish-tab" data-bs-toggle="pill" data-bs-target="#tab-finish" type="button" role="tab" aria-selected="false">Finish</button>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content">

                        <!-- Step 1: Personal Info -->
                        <div class="tab-pane fade show active" id="tab-personal" role="tabpanel">
                            <div class="alert alert-warning" role="alert">
                                <strong>Note:</strong> All fields in this section are required. Please fill them out before proceeding.
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{$student->email}}" readonly disabled>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="lastname">Lastname</label>
                                    <input type="lastname" class="form-control" id="lastname" name="lastname"  value="{{$student->applicant->lastname}}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="othernames">Othernames</label>
                                    <input type="text" class="form-control" id="othernames" name="othernames" value="{{$student->applicant->othernames}}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="dob">Date of Birth </label>
                                   <input type="date" class="form-control" id="dob" name="dob" value="{{ \Carbon\Carbon::parse($student->applicant->dob)->format('Y-m-d') }}" required>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-control" name="gender" id="gender" required>
                                            <option @if($applicant->gender == '') selected  @endif value="" selected>Select Gender</option>
                                            <option @if($applicant->gender == 'Male') selected  @endif value="Male">Male</option>
                                            <option @if($applicant->gender == 'Female') selected  @endif value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="phone_number">Phone Number</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{$student->applicant->phone_number}}" required>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="marital_status" class="form-label">Marital Status</label>
                                        <select class="form-control" name="marital_status" id="marital_status" required>
                                            <option @if($applicant->marital_status == '') selected  @endif value="" selected>Select Marital Status</option>
                                            <option @if($applicant->marital_status == 'Single') selected  @endif value="Single">Single</option>
                                            <option @if($applicant->marital_status == 'Married') selected  @endif value="Married">Married</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="nationality">Nationality</label>
                                    <input type="text" class="form-control" id="nationality" name="nationality" value="{{$student->applicant->nationality}}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="state">State of Origin</label>
                                    <input type="text" class="form-control" id="state" name="state" value="{{$student->applicant->state}}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="lga">Local Government Area</label>
                                    <input type="text" class="form-control" id="lga" name="lga" value="{{$student->applicant->lga}}" required>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="religion" class="form-label">Religion</label>
                                        <select class="form-control" name="religion" id="religion" required>
                                            <option value="" @if($applicant->religion == '') selected  @endif >Select Religion</option>
                                            <option @if($applicant->religion == 'Christianity') selected  @endif value="Christianity">Christianity</option>
                                            <option @if($applicant->religion == 'Islamic') selected  @endif value="Islamic">Islamic</option>
                                            <option @if($applicant->religion == 'Others') selected  @endif value="Others">Others</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="family_position">Position in the Family (1st, 2nd, or 3rd)</label>
                                    <input type="text" class="form-control" id="family_position" name="family_position" value="{{$student->applicant->family_position}}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="number_of_siblings">Number of Siblings</label>
                                    <input type="text" class="form-control" id="number_of_siblings" name="number_of_siblings" value="{{$student->applicant->number_of_siblings}}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="facebook">Facebook Username</label>
                                    <input type="text" class="form-control" id="facebook" name="facebook" value="{{$student->facebook}}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="linkedIn">Linkedin Username</label>
                                    <input type="text" class="form-control" id="linkedIn" name="linkedIn" value="{{$student->linkedIn}}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="tiktok">Tiktok Username</label>
                                    <input type="text" class="form-control" id="tiktok" name="tiktok" value="{{$student->tiktok}}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="instagram">Instagram Username</label>
                                    <input type="text" class="form-control" id="instagram" name="instagram" value="{{$student->instagram}}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="whatsapp">Whatsapp Phone number</label>
                                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{$student->whatsapp}}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="twitter">X(Twitter) Username</label>
                                    <input type="text" class="form-control" id="twitter" name="twitter" value="{{$student->twitter}}" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <label for="hobbies">Hobbies</label>
                                <textarea class="ckeditor" id="hobbies" name="hobbies" >{!! $student->hobbies !!}</textarea>
                            </div><!--end col-->
                           
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-success nexttab" data-nexttab="tab-parent-tab" disabled>Next</button>
                            </div>
                        </div>

                        <!-- Step 2: Parent Info -->
                        <div class="tab-pane fade" id="tab-parent" role="tabpanel">
                            <div class="alert alert-warning" role="alert">
                                 <strong>Note:</strong> All fields in this section are required. Please fill them out before proceeding.
                            </div>

                            <div class="row">
                                <!-- Father's Information -->
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="father-name">Father’s Name</label>
                                    <input type="text" class="form-control" id="father-name" name="father_name" value="{{$guardian->father_name}}" placeholder="Enter father's name">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="father-occupation">Father’s Occupation</label>
                                    <input type="text" class="form-control" id="father-occupation" name="father_occupation" value="{{$guardian->father_occupation}}" placeholder="Enter father's occupation">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="father-phone">Father’s Phone Number</label>
                                    <input type="text" class="form-control" id="father-phone" name="father_phone" value="{{$guardian->father_phone}}" placeholder="Enter father's phone number">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="father-email">Father’s Email</label>
                                    <input type="email" class="form-control" id="father-email" name="father_email" value="{{$guardian->father_email}}" placeholder="Enter father's email">
                                </div>

                                <!-- Mother's Information -->
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="mother-name">Mother’s Name</label>
                                    <input type="text" class="form-control" id="mother-name" name="mother_name" value="{{$guardian->mother_name}}" placeholder="Enter mother's name">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="mother-occupation">Mother’s Occupation</label>
                                    <input type="text" class="form-control" id="mother-occupation" name="mother_occupation" value="{{$guardian->mother_occupation}}" placeholder="Enter mother's occupation">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="mother-phone">Mother’s Phone Number</label>
                                    <input type="text" class="form-control" id="mother-phone" name="mother_phone" value="{{$guardian->mother_phone}}" placeholder="Enter mother's phone number">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="mother-email">Mother’s Email</label>
                                    <input type="email" class="form-control" id="mother-email" name="mother_email" value="{{$guardian->mother_email}}" placeholder="Enter mother's email">
                                </div>

                                <!-- Parent Residential Address -->
                                <div class="col-lg-12 mb-3">
                                    <label for="parent_address">Parent’s Residential Address</label>
                                    <textarea class="ckeditor" id="parent_address" name="parent_address" ></textarea>
                                </div><!--end col-->

                                <!-- Guardian Info (if applicable) -->
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="guardian-name">Guardian’s Name</label>
                                    <input type="text" class="form-control" id="guardian-name" name="name" value="{{$guardian->name}}" placeholder="Enter guardian's name">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="guardian-phone">Guardian’s Phone Number</label>
                                    <input type="text" class="form-control" id="guardian-phone" name="phone_number" value="{{$guardian->phone_number}}" placeholder="Enter guardian's phone number">
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="guardian-email">Guardian’s Email</label>
                                    <input type="email" class="form-control" id="guardian-email" name="email" value="{{$guardian->email}}" placeholder="Enter guardian's email">
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label for="address">Guardian Residential Address</label>
                                    <textarea class="ckeditor" id="parent_address" name="address" >{!! $guardian->address !!}</textarea>
                                </div><!--end col-->

                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-light previestab" data-previous="tab-personal-tab">Back</button>
                                <button type="button" class="btn btn-success nexttab" data-nexttab="tab-academic-tab disabled">Next</button>
                            </div>
                        </div>

                        <!-- Step 3: Academic Info -->
                        <div class="tab-pane fade" id="tab-academic" role="tabpanel">
                            <div class="alert alert-warning" role="alert">
                                <strong>Note:</strong> All fields in this section are required. Please fill them out before proceeding.
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="matric_number">Matriculation Number</label>
                                    <input type="text" class="form-control" id="matric_number" name="matric_number" value="{{$student->matric_number}}" readonly disabled>
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="entry_year">Admission Year</label>
                                    <input type="text" class="form-control" id="entry_year" name="entry_year" value="{{$student->entry_year}}" readonly disabled>
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="faculty">Faculty</label>
                                    <input type="text" class="form-control" id="faculty" name="faculty" value="{{$student->faculty->name}}" readonly disabled>
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="department">Department</label>
                                    <input type="text" class="form-control" id="department" name="department" value="{{$student->department->name}}" readonly disabled>
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="programme">Course of Study</label>
                                    <input type="text" class="form-control" id="programme" name="programme" value="{{$student->programme->name}}" readonly disabled>
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="level">Level</label>
                                    <input type="text" class="form-control" id="level" name="level" value="{{$student->academicLevel->level}}" readonly disabled>
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="application_type">Mode of Admission</label>
                                    <input type="text" class="form-control" id="application_type" name="application_type" value="{{$applicant->application_type}}" readonly disabled>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-light previestab" data-previous="tab-parent-tab">Back</button>
                                <button type="button" class="btn btn-success nexttab" data-nexttab="tab-finish-tab" disabled>Next</button>
                            </div>
                        </div>

                        <!-- Step 4: Finish -->
                        <div class="tab-pane fade" id="tab-finish" role="tabpanel">
                            <div class="text-center">
                                <div class="avatar-md mt-5 mb-4 mx-auto">
                                    <div class="avatar-title bg-light text-success display-4 rounded-circle">
                                        <i class="ri-checkbox-circle-fill"></i>
                                    </div>
                                </div>
                                <h5>Well Done!</h5>
                                <p class="text-muted">You have successfully signed up.</p>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
@else

<div class="position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg profile-setting-img">
        <img src="{{asset('assets/images/profile-bg.jpg')}}" class="profile-wid-img" alt="">
        <div class="overlay-content">
            <div class="text-end p-3">
                
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xxl-4">
        <div class="card mt-n5">
            <div class="card-body p-4">
                <div class="text-center">
                    <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                        <img src="{{empty($student->image)?asset('assets/images/users/user-dummy-img.jpg'):asset($student->image)}}" class="rounded-circle avatar-xl img-thumbnail user-profile-image  shadow" alt="user-profile-image"> 
                    </div>
                    <h5 class="fs-16 mb-1">{{ $name }}</h5>
                    <p class="text-muted mb-0">Programme: {{ $student->programme->name }}</p>
                    <p class="text-muted mb-0 text-bold">Matric Number: {{  $student->matric_number }}</p>
                    <p class="text-muted mb-0 text-bold">Level: {{ $student->academicLevel->level }} Level</p>

                    @if(empty($student->signature))
                    <hr>
                    <form class="text-start" action="{{ url('student/profile/saveBioData') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="alert alert-info">
                            <h5 class="alert-heading text-black">Steps to Upload a Signature Without Background</h5>
                            <ol>
                                <li><strong>Sign on a White Paper</strong> – Use a clean white sheet and sign with a black or dark-colored pen.</li>
                                <li><strong>Take a Picture</strong> – Use your phone camera to snap a clear and well-lit photo of your signature. Ensure there are no shadows or extra marks.</li>
                                <li><strong>Remove Background</strong> –  
                                    <ul>
                                        <li>Visit <a href="https://www.remove.bg" target="_blank">remove.bg</a>.</li>
                                        <li>Upload the signature image.</li>
                                        <li>The website will automatically remove the background.</li>
                                    </ul>
                                </li>
                                <li><strong>Download the Transparent Image</strong> – Click the download button to save the processed image with a transparent background.</li>
                                <li><strong>Upload the Signature</strong> – Use the downloaded transparent signature file below (e.g., documents, forms, or digital signing).</li>
                            </ol>
                            <p>This method ensures a clean and professional-looking signature without any unwanted background. 🚀</p>
                        </div>

                        <input type="hidden" name="student_id" value="{{ $student->id }}">

                        <div class="col-lg-12">
                            <div class="form-floating">
                                <input type="file" class="form-control" id="signature" name="signature">
                                <label for="signature"></label>
                            </div>
                        </div>
                        <br>
                        <button type="submit" id="submit-button" class="btn btn-block btn-fill btn-primary"> Submit</button>
                    </form>
                    @else
                    <hr>
                    <img src="{{asset($student->signature)}}" class="avatar-xl img-thumbnail shadow" alt="user-profile-image"> 
                    @endif

                    <hr>
                    <form class="text-start" action="{{ url('student/setMode') }}" method="POST">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student->id }}">

                        <div class="mb-3">
                            <label for="choices-publish-status-input" class="form-label">Set Dashboard Theme</label>
                            <select class="form-select" name="dashboard_mode" id="choices-publish-status-input" data-choices data-choices-search-false required>
                                <option value="" selected>Choose...</option>
                                <option value="dark">Dark</option>
                                <option value="light">Light</option>
                            </select>
                        </div>

                        <br>
                        <button type="submit" id="submit-button" class="btn btn-block btn-fill btn-primary"> Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <!--end card-->
    
    </div>
    <!--end col-->
    <div class="col-xxl-8">
        <div class="card mt-xxl-n5">
            <div class="card-header">
                <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                            <i class="fas fa-home"></i> Personal Details
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#updatePassword" role="tab">
                            <i class="far fa-user"></i> Change Password
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content">
                    <div class="tab-pane active" id="personalDetails" role="tabpanel">
                        <form action="{{ url('student/profile/saveBioData') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="lastname" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastname" value="{{ $applicant->lastname }}" readonly disabled>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="othernames" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="othernames" value="{{$applicant->othernames}}" readonly disabled>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" value="{{$student->email}}" readonly disabled>
                                    </div>
                                </div>

                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="phonenumber" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" name="phone_number" id="phonenumber" value="{{$applicant->phone_number}}">
                                    </div>
                                </div>
                                
                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label for="religion" class="form-label">Religion</label>
                                        <select class="form-control" name="religion" id="religion" required>
                                            <option value="" @if($applicant->religion == '') selected  @endif >Select Religion</option>
                                            <option @if($applicant->religion == 'Christianity') selected  @endif value="Christianity">Christianity</option>
                                            <option @if($applicant->religion == 'Islamic') selected  @endif value="Islamic">Islamic</option>
                                            <option @if($applicant->religion == 'Others') selected  @endif value="Others">Others</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end col-->

                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-control" name="gender" id="gender" required>
                                            <option @if($applicant->gender == '') selected  @endif value="" selected>Select Gender</option>
                                            <option @if($applicant->gender == 'Male') selected  @endif value="Male">Male</option>
                                            <option @if($applicant->gender == 'Female') selected  @endif value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label for="marital_status" class="form-label">Marital Status</label>
                                        <select class="form-control" name="marital_status" id="marital_status" required>
                                            <option @if($applicant->marital_status == '') selected  @endif value="" selected>Select Marital Status</option>
                                            <option @if($applicant->marital_status == 'Single') selected  @endif value="Single">Single</option>
                                            <option @if($applicant->marital_status == 'Married') selected  @endif value="Married">Married</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label for="dob" class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control"  id="dob" name="dob" value="{{ $applicant->dob }}" required />
                                    </div>
                                </div>


                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="nationality" class="form-label">Nationality - {{ $applicant->nationality }}</label>
                                        <select class="form-control" name="nationality" id="nationality" required>
                                            <option value="Nigeria">Nigeria</option>
                                            <option value="Afghanistan">Afghanistan</option>
                                            <option value="Åland Islands">Åland Islands</option>
                                            <option value="Albania">Albania</option>
                                            <option value="Algeria">Algeria</option>
                                            <option value="American Samoa">American Samoa</option>
                                            <option value="Andorra">Andorra</option>
                                            <option value="Angola">Angola</option>
                                            <option value="Anguilla">Anguilla</option>
                                            <option value="Antarctica">Antarctica</option>
                                            <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                            <option value="Argentina">Argentina</option>
                                            <option value="Armenia">Armenia</option>
                                            <option value="Aruba">Aruba</option>
                                            <option value="Australia">Australia</option>
                                            <option value="Austria">Austria</option>
                                            <option value="Azerbaijan">Azerbaijan</option>
                                            <option value="Bahamas">Bahamas</option>
                                            <option value="Bahrain">Bahrain</option>
                                            <option value="Bangladesh">Bangladesh</option>
                                            <option value="Barbados">Barbados</option>
                                            <option value="Belarus">Belarus</option>
                                            <option value="Belgium">Belgium</option>
                                            <option value="Belize">Belize</option>
                                            <option value="Benin">Benin</option>
                                            <option value="Bermuda">Bermuda</option>
                                            <option value="Bhutan">Bhutan</option>
                                            <option value="Bolivia">Bolivia</option>
                                            <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                            <option value="Botswana">Botswana</option>
                                            <option value="Bouvet Island">Bouvet Island</option>
                                            <option value="Brazil">Brazil</option>
                                            <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                                            <option value="Brunei Darussalam">Brunei Darussalam</option>
                                            <option value="Bulgaria">Bulgaria</option>
                                            <option value="Burkina Faso">Burkina Faso</option>
                                            <option value="Burundi">Burundi</option>
                                            <option value="Cambodia">Cambodia</option>
                                            <option value="Cameroon">Cameroon</option>
                                            <option value="Canada">Canada</option>
                                            <option value="Cape Verde">Cape Verde</option>
                                            <option value="Cayman Islands">Cayman Islands</option>
                                            <option value="Central African Republic">Central African Republic</option>
                                            <option value="Chad">Chad</option>
                                            <option value="Chile">Chile</option>
                                            <option value="China">China</option>
                                            <option value="Christmas Island">Christmas Island</option>
                                            <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                                            <option value="Colombia">Colombia</option>
                                            <option value="Comoros">Comoros</option>
                                            <option value="Congo">Congo</option>
                                            <option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option>
                                            <option value="Cook Islands">Cook Islands</option>
                                            <option value="Costa Rica">Costa Rica</option>
                                            <option value="Cote D'ivoire">Cote D'ivoire</option>
                                            <option value="Croatia">Croatia</option>
                                            <option value="Cuba">Cuba</option>
                                            <option value="Cyprus">Cyprus</option>
                                            <option value="Czech Republic">Czech Republic</option>
                                            <option value="Denmark">Denmark</option>
                                            <option value="Djibouti">Djibouti</option>
                                            <option value="Dominica">Dominica</option>
                                            <option value="Dominican Republic">Dominican Republic</option>
                                            <option value="Ecuador">Ecuador</option>
                                            <option value="Egypt">Egypt</option>
                                            <option value="El Salvador">El Salvador</option>
                                            <option value="Equatorial Guinea">Equatorial Guinea</option>
                                            <option value="Eritrea">Eritrea</option>
                                            <option value="Estonia">Estonia</option>
                                            <option value="Ethiopia">Ethiopia</option>
                                            <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
                                            <option value="Faroe Islands">Faroe Islands</option>
                                            <option value="Fiji">Fiji</option>
                                            <option value="Finland">Finland</option>
                                            <option value="France">France</option>
                                            <option value="French Guiana">French Guiana</option>
                                            <option value="French Polynesia">French Polynesia</option>
                                            <option value="French Southern Territories">French Southern Territories</option>
                                            <option value="Gabon">Gabon</option>
                                            <option value="Gambia">Gambia</option>
                                            <option value="Georgia">Georgia</option>
                                            <option value="Germany">Germany</option>
                                            <option value="Ghana">Ghana</option>
                                            <option value="Gibraltar">Gibraltar</option>
                                            <option value="Greece">Greece</option>
                                            <option value="Greenland">Greenland</option>
                                            <option value="Grenada">Grenada</option>
                                            <option value="Guadeloupe">Guadeloupe</option>
                                            <option value="Guam">Guam</option>
                                            <option value="Guatemala">Guatemala</option>
                                            <option value="Guernsey">Guernsey</option>
                                            <option value="Guinea">Guinea</option>
                                            <option value="Guinea-bissau">Guinea-bissau</option>
                                            <option value="Guyana">Guyana</option>
                                            <option value="Haiti">Haiti</option>
                                            <option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option>
                                            <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
                                            <option value="Honduras">Honduras</option>
                                            <option value="Hong Kong">Hong Kong</option>
                                            <option value="Hungary">Hungary</option>
                                            <option value="Iceland">Iceland</option>
                                            <option value="India">India</option>
                                            <option value="Indonesia">Indonesia</option>
                                            <option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option>
                                            <option value="Iraq">Iraq</option>
                                            <option value="Ireland">Ireland</option>
                                            <option value="Isle of Man">Isle of Man</option>
                                            <option value="Israel">Israel</option>
                                            <option value="Italy">Italy</option>
                                            <option value="Jamaica">Jamaica</option>
                                            <option value="Japan">Japan</option>
                                            <option value="Jersey">Jersey</option>
                                            <option value="Jordan">Jordan</option>
                                            <option value="Kazakhstan">Kazakhstan</option>
                                            <option value="Kenya">Kenya</option>
                                            <option value="Kiribati">Kiribati</option>
                                            <option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option>
                                            <option value="Korea, Republic of">Korea, Republic of</option>
                                            <option value="Kuwait">Kuwait</option>
                                            <option value="Kyrgyzstan">Kyrgyzstan</option>
                                            <option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
                                            <option value="Latvia">Latvia</option>
                                            <option value="Lebanon">Lebanon</option>
                                            <option value="Lesotho">Lesotho</option>
                                            <option value="Liberia">Liberia</option>
                                            <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
                                            <option value="Liechtenstein">Liechtenstein</option>
                                            <option value="Lithuania">Lithuania</option>
                                            <option value="Luxembourg">Luxembourg</option>
                                            <option value="Macao">Macao</option>
                                            <option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option>
                                            <option value="Madagascar">Madagascar</option>
                                            <option value="Malawi">Malawi</option>
                                            <option value="Malaysia">Malaysia</option>
                                            <option value="Maldives">Maldives</option>
                                            <option value="Mali">Mali</option>
                                            <option value="Malta">Malta</option>
                                            <option value="Marshall Islands">Marshall Islands</option>
                                            <option value="Martinique">Martinique</option>
                                            <option value="Mauritania">Mauritania</option>
                                            <option value="Mauritius">Mauritius</option>
                                            <option value="Mayotte">Mayotte</option>
                                            <option value="Mexico">Mexico</option>
                                            <option value="Micronesia, Federated States of">Micronesia, Federated States of</option>
                                            <option value="Moldova, Republic of">Moldova, Republic of</option>
                                            <option value="Monaco">Monaco</option>
                                            <option value="Mongolia">Mongolia</option>
                                            <option value="Montenegro">Montenegro</option>
                                            <option value="Montserrat">Montserrat</option>
                                            <option value="Morocco">Morocco</option>
                                            <option value="Mozambique">Mozambique</option>
                                            <option value="Myanmar">Myanmar</option>
                                            <option value="Namibia">Namibia</option>
                                            <option value="Nauru">Nauru</option>
                                            <option value="Nepal">Nepal</option>
                                            <option value="Netherlands">Netherlands</option>
                                            <option value="Netherlands Antilles">Netherlands Antilles</option>
                                            <option value="New Caledonia">New Caledonia</option>
                                            <option value="New Zealand">New Zealand</option>
                                            <option value="Nicaragua">Nicaragua</option>
                                            <option value="Niger">Niger</option>
                                            <option value="Niue">Niue</option>
                                            <option value="Norfolk Island">Norfolk Island</option>
                                            <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                                            <option value="Norway">Norway</option>
                                            <option value="Oman">Oman</option>
                                            <option value="Pakistan">Pakistan</option>
                                            <option value="Palau">Palau</option>
                                            <option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
                                            <option value="Panama">Panama</option>
                                            <option value="Papua New Guinea">Papua New Guinea</option>
                                            <option value="Paraguay">Paraguay</option>
                                            <option value="Peru">Peru</option>
                                            <option value="Philippines">Philippines</option>
                                            <option value="Pitcairn">Pitcairn</option>
                                            <option value="Poland">Poland</option>
                                            <option value="Portugal">Portugal</option>
                                            <option value="Puerto Rico">Puerto Rico</option>
                                            <option value="Qatar">Qatar</option>
                                            <option value="Reunion">Reunion</option>
                                            <option value="Romania">Romania</option>
                                            <option value="Russian Federation">Russian Federation</option>
                                            <option value="Rwanda">Rwanda</option>
                                            <option value="Saint Helena">Saint Helena</option>
                                            <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                            <option value="Saint Lucia">Saint Lucia</option>
                                            <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                                            <option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option>
                                            <option value="Samoa">Samoa</option>
                                            <option value="San Marino">San Marino</option>
                                            <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                            <option value="Senegal">Senegal</option>
                                            <option value="Serbia">Serbia</option>
                                            <option value="Seychelles">Seychelles</option>
                                            <option value="Sierra Leone">Sierra Leone</option>
                                            <option value="Singapore">Singapore</option>
                                            <option value="Slovakia">Slovakia</option>
                                            <option value="Slovenia">Slovenia</option>
                                            <option value="Solomon Islands">Solomon Islands</option>
                                            <option value="Somalia">Somalia</option>
                                            <option value="South Africa">South Africa</option>
                                            <option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option>
                                            <option value="Spain">Spain</option>
                                            <option value="Sri Lanka">Sri Lanka</option>
                                            <option value="Sudan">Sudan</option>
                                            <option value="Suriname">Suriname</option>
                                            <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                                            <option value="Swaziland">Swaziland</option>
                                            <option value="Sweden">Sweden</option>
                                            <option value="Switzerland">Switzerland</option>
                                            <option value="Syrian Arab Republic">Syrian Arab Republic</option>
                                            <option value="Taiwan">Taiwan</option>
                                            <option value="Tajikistan">Tajikistan</option>
                                            <option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
                                            <option value="Thailand">Thailand</option>
                                            <option value="Timor-leste">Timor-leste</option>
                                            <option value="Togo">Togo</option>
                                            <option value="Tokelau">Tokelau</option>
                                            <option value="Tonga">Tonga</option>
                                            <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                            <option value="Tunisia">Tunisia</option>
                                            <option value="Turkey">Turkey</option>
                                            <option value="Turkmenistan">Turkmenistan</option>
                                            <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                                            <option value="Tuvalu">Tuvalu</option>
                                            <option value="Uganda">Uganda</option>
                                            <option value="Ukraine">Ukraine</option>
                                            <option value="United Arab Emirates">United Arab Emirates</option>
                                            <option value="United Kingdom">United Kingdom</option>
                                            <option value="United States">United States</option>
                                            <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
                                            <option value="Uruguay">Uruguay</option>
                                            <option value="Uzbekistan">Uzbekistan</option>
                                            <option value="Vanuatu">Vanuatu</option>
                                            <option value="Venezuela">Venezuela</option>
                                            <option value="Viet Nam">Viet Nam</option>
                                            <option value="Virgin Islands, British">Virgin Islands, British</option>
                                            <option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
                                            <option value="Wallis and Futuna">Wallis and Futuna</option>
                                            <option value="Western Sahara">Western Sahara</option>
                                            <option value="Yemen">Yemen</option>
                                            <option value="Zambia">Zambia</option>
                                            <option value="Zimbabwe">Zimbabwe</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="state_of_origin" class="form-label">State of Origin - {{ $applicant->state }}</label>
                                        <select onchange="toggleLGA(this);" name="state" id="state" class="form-control" required>
                                            <option value="" selected="selected">- Select -</option>
                                            <option value="Abia">Abia</option>
                                            <option value="Adamawa">Adamawa</option>
                                            <option value="AkwaIbom">AkwaIbom</option>
                                            <option value="Anambra">Anambra</option>
                                            <option value="Bauchi">Bauchi</option>
                                            <option value="Bayelsa">Bayelsa</option>
                                            <option value="Benue">Benue</option>
                                            <option value="Borno">Borno</option>
                                            <option value="Cross River">Cross River</option>
                                            <option value="Delta">Delta</option>
                                            <option value="Ebonyi">Ebonyi</option>
                                            <option value="Edo">Edo</option>
                                            <option value="Ekiti">Ekiti</option>
                                            <option value="Enugu">Enugu</option>
                                            <option value="FCT">FCT</option>
                                            <option value="Gombe">Gombe</option>
                                            <option value="Imo">Imo</option>
                                            <option value="Jigawa">Jigawa</option>
                                            <option value="Kaduna">Kaduna</option>
                                            <option value="Kano">Kano</option>
                                            <option value="Katsina">Katsina</option>
                                            <option value="Kebbi">Kebbi</option>
                                            <option value="Kogi">Kogi</option>
                                            <option value="Kwara">Kwara</option>
                                            <option value="Lagos">Lagos</option>
                                            <option value="Nasarawa">Nasarawa</option>
                                            <option value="Niger">Niger</option>
                                            <option value="Ogun">Ogun</option>
                                            <option value="Ondo">Ondo</option>
                                            <option value="Osun">Osun</option>
                                            <option value="Oyo">Oyo</option>
                                            <option value="Plateau">Plateau</option>
                                            <option value="Rivers">Rivers</option>
                                            <option value="Sokoto">Sokoto</option>
                                            <option value="Taraba">Taraba</option>
                                            <option value="Yobe">Yobe</option>
                                            <option value="Zamfara">Zamafara</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="lga" class="form-label">Local Government Area - {{ $applicant->lga }}</label>
                                        <select name="lga" id="lga" class="form-control select-lga" required></select>
                                    </div>
                                </div>

                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" id="submit-button" class="btn btn-primary">Update</button>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </form>
                    </div>
                    <!--end tab-pane-->
                    <div class="tab-pane" id="updatePassword" role="tabpanel">
                        <form action="{{ url('student/updatePassword') }}" method="POST">
                            @csrf
                            <div class="row g-2">
                                <div class="col-lg-4">
                                    <div>
                                        <label for="oldpasswordInput" class="form-label">Old Password<span class="text-danger">*</span></label>
                                        <input type="password" name="old_password" class="form-control" id="oldpasswordInput" placeholder="Enter current password">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-4">
                                    <div>
                                        <label for="newpasswordInput" class="form-label">New Password<span class="text-danger">*</span></label>
                                        <input type="password"  name="password"  class="form-control" id="newpasswordInput" placeholder="Enter new password">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-4">
                                    <div>
                                        <label for="confirmpasswordInput" class="form-label">Confirm Password<span class="text-danger">*</span></label>
                                        <input type="password"  name="confirm_password" class="form-control" id="confirmpasswordInput" placeholder="Confirm password">
                                    </div>
                                </div>

                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" id="submit-button" class="btn btn-success">Change Password</button>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->
@endif
<script>
document.addEventListener('DOMContentLoaded', function () {

    const requiredFieldsToast = document.getElementById('requiredFieldsToast');
    const toast = new bootstrap.Toast(requiredFieldsToast);

    // This function validates all required fields within a given tab pane.
    const isTabValid = (tabPane) => {
        const requiredInputs = tabPane.querySelectorAll('[required]');
        let allFieldsFilled = true;

        requiredInputs.forEach(input => {
            // Check for various input types, including textareas like CKEditor
            if (input.tagName.toLowerCase() === 'textarea') {
                const editorId = input.id;
                // Handle CKEditor specifically
                if (window.CKEDITOR && window.CKEDITOR.instances[editorId]) {
                    if (window.CKEDITOR.instances[editorId].getData().trim() === '') {
                        allFieldsFilled = false;
                    }
                } else if (input.value.trim() === '') {
                    allFieldsFilled = false;
                }
            } else if (input.value.trim() === '') {
                allFieldsFilled = false;
            }
        });
        return allFieldsFilled;
    };

    // Handle clicks on the 'Next' buttons
    const nextButtons = document.querySelectorAll('.nexttab');
    nextButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            const currentTabPane = button.closest('.tab-pane');
            if (!isTabValid(currentTabPane)) {
                event.preventDefault(); // Stop the tab from switching
                toast.show(); // Show toast message
            }
        });
    });

    // Handle clicks on the tab navigation links
    const tabLinks = document.querySelectorAll('button[data-bs-toggle="pill"]');
    tabLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            const currentTabId = document.querySelector('.nav-link.active').getAttribute('data-bs-target');
            const currentTabPane = document.querySelector(currentTabId);
            
            if (currentTabPane && !isTabValid(currentTabPane)) {
                event.preventDefault(); // Prevent tab switching if current tab is invalid
                toast.show(); // Show toast message
            }
        });
    });
});
</script>
@endsection