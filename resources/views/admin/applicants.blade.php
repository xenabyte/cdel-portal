@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $programmeCategory->category }} Programme Applicants for {{ empty($session)?$pageGlobalData->sessionSetting->application_session : $session }} Application Session</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Applicants</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Applicants for {{ empty($session)?$pageGlobalData->sessionSetting->application_session : $session }} Application Session </h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#createApplicant">Create Applicant</button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#searchApplicant">Filter Applicants</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Application Number</th>
                            <th scope="col">Name</th>
                            <th scope="col">Age</th>
                            <th scope="col">Email</th>
                            <th scope="col">Access Code</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Application Type</th>
                            <th scope="col">Academic Session</th>
                            <th scope="col">Application Status</th>
                            <th scope="col">Applied Date</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applicants->where('status', '!=', 'Admitted') as $applicant)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $applicant->application_number }}</td>
                            <td>{{ $applicant->lastname .' '. $applicant->othernames }}</td>
                            <td>{{ \Carbon\Carbon::parse($applicant->dob)->age }} years</td>
                            <td>{{ $applicant->email }} </td>
                            <td>{{ $applicant->passcode }} </td>
                            <td>{{ $applicant->phone_number }} </td>
                            <td>{{ $applicant->gender }}</td>
                            <td>{{ !empty($applicant->programme)?$applicant->programme->name:null }}</td>
                            <td>{{ $applicant->application_type }}</td>
                            <td>{{ $applicant->academic_session }} </td>
                            <td>{{ ucwords($applicant->status) }} </td>
                            <td>{{ $applicant->created_at }} </td>
                            <td>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit{{$applicant->id}}" class="btn btn-info m-1"><i class= "ri-edit-box-fill"></i> Edit</a>
                                @if(!empty($applicant->programme_id))<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#view{{$applicant->id}}" class="btn btn-secondary m-1"><i class= "ri-eye-fill"></i> View</a>@endif
                                @if(!empty($applicant->programme_id))<a href="{{ url('admin/applicant/'.$applicant->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Applicant</a>@endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@foreach($applicants->where('status', '!=', 'Admitted') as $applicant)
<div id="view{{$applicant->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">View {{ $programmeCategory->category }} Programme Applicant </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3 border-end">
                        <div class="card-body text-center">
                            <div class="avatar-md mb-3 mx-auto">
                                <img src="{{empty($applicant->image)?asset('assets/images/users/user-dummy-img.jpg'): asset($applicant->image) }}" alt="" id="candidate-img" class="img-thumbnail rounded-circle shadow-none">
                            </div>
    
                            <h5 id="candidate-name" class="mb-0">{{ $applicant->lastname .' '. $applicant->othernames }}</h5>
                            <p id="candidate-position" class="text-muted">{{ $applicant->programmeCategory?$applicant->programmeCategory->category.' Programme':null }}</p>
                            <p id="candidate-position" class="text-muted">{{ $applicant->programme?$applicant->programme->name:null }}</p>
                            <p id="candidate-position" class="text-muted">Phone Number: {{ $applicant->phone_number }}</p>
                            <div class="vr"></div>
                            <div class="text-muted">Application ID : <span class="text-body fw-medium"> {{ $applicant->application_number }}</span></div>
                            @if($applicant->application_type == 'UTME')
                            <div class="vr"></div>
                            <div class="text-muted">UTME Scores : <span class="text-body fw-medium"> {{ $applicant->utmes->sum('score') }}</span></div>
                            @endif
                            <div class="vr"></div>
                            <div class="text-muted">Application Date : <span class="text-body fw-medium">{{ $applicant->updated_at }}</span></div>
                            <hr>
                            <h4 class="mt-3 alert alert-info">Admission Status: {{ empty($applicant->status)? 'Processing' : ucwords($applicant->status) }}</h4>
                        </div>
                    </div>
    
                    <div class="col-md-3 border-end">
                        <div class="card-body">
                            @if(!empty($applicant->olevel_1))
                            <h5 class="fs-14 mb-3"> Schools Attended</h5>
                            {!! $applicant->schools_attended !!}
                            <hr>
                            <div class="row mb-2">
                                <div class="col-sm-6 col-xl-12">
                                    <!-- Simple card -->
                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 25px"></i><span class="fs-14">Olevel Result</span>
                                    <div class="text-end">
                                        <a href="{{ asset($applicant->olevel_1) }}" target="blank" class="btn btn-success">View</a>
                                    </div>
                                    @if($applicant->sitting_no > 1)
                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 25px"></i><span class="fs-14">Olevel Result (Second Sitting)</span>
                                    <div class="text-end">
                                        <a href="{{ asset($applicant->olevel_2) }}" target="blank"  class="btn btn-success">View</a>
                                    </div>
                                    @endif
                                </div><!-- end col -->
                            </div>
                            <hr>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-borderedless table-nowrap">
                                    <thead>
                                        <tr>
                                            <th scope="col">Id</th>
                                            <th scope="col">Subject</th>
                                            <th scope="col">Grade</th>
                                            <th scope="col">Registration Number</th>
                                            <th scope="col">Year</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($applicant->olevels as $olevel)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $olevel->subject }}</td>
                                            <td>{{ $olevel->grade }}</td>
                                            <td>{{ $olevel->reg_no }}</td>
                                            <td>{{ $olevel->year }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-md-3 border-end">
                        <div class="card-body">
                            @if($applicant->application_type == 'UTME')
                            <div>
                                @if(!empty($applicant->utme))
                                <div class="row mb-2">
                                    <div class="col-sm-6 col-xl-12">
                                        <!-- Simple card -->
                                        <i class="bx bxs-file-jpg text-danger" style="font-size: 25px"></i><span class="fs-14">UTME Result Printout</span>
                                        <div class="text-end">
                                            <a href="{{ asset($applicant->utme) }}"  target="blank" class="btn btn-success">View</a>
                                        </div>
                                    </div><!-- end col -->
                                </div>
                                <hr>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-borderedless table-nowrap">
                                        <thead>
                                            <tr>
                                                <th scope="col">Id</th>
                                                <th scope="col">Subject</th>
                                                <th scope="col">Score</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($applicant->utmes as $utme)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $utme->subject }}</td>
                                                <td>{{ $utme->score }}</td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <th scope="row"></th>
                                                <td>Total</td>
                                                <td><strong>{{$applicant->utmes->sum('score')}}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @elseif($applicant->application_type != 'UTME')
                            <div>
                                <h5 class="fs-14 mb-3"> Institution Attended</h5>
                                {!! $applicant->de_school_attended !!}
                                <hr>
                                @if(!empty($applicant->de_result))
                                <div class="row mb-2">
                                    <div class="col-sm-6 col-xl-12">
                                        <!-- Simple card -->
                                        <i class="bx bxs-file-jpg text-danger" style="font-size: 25px"></i><span class="fs-14">Direct Entry/Prev Institution/Prev Institution Result</span>
                                        <div class="text-end">
                                            <a href="{{ asset($applicant->de_result) }}"  target="blank" class="btn btn-success">View</a>
                                        </div>
                                    </div><!-- end col -->
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card-body">
                            <h5 class="fs-14 mb-3 border-bottom"> Manage Admission</h5>
                            @if($applicant->status == 'submitted')
                            <form action="{{ url('admin/manageAdmission') }}" method="POST">
                                @csrf
                                <input type="hidden" name="applicant_id" value="{{ $applicant->id }}">
                                <div class="mb-3">
                                    <label for="programme" class="form-label">Programmes</label>
                                    <select class="form-select" name="programme_id" id="programme" data-choices data-choices-search-false required>
                                        @foreach($programmes as $programme)<option @if($programme->id == $applicant->programme_id) selected  @endif value="{{ $programme->id }}">{{ $programme->name }}</option>@endforeach
                                    </select>
                                </div>
    
                                <div class="mb-3">
                                    <label for="level" class="form-label">Level</label>
                                    <select class="form-select" name="level_id" id="level" data-choices data-choices-search-false required>
                                        <option value="" selected>Choose...</option>
                                        @foreach($levels as $academicLevel)
                                            @if(strtolower($programmeCategory->category) === 'topup' && (int) $academicLevel->level < 300)
                                                @continue
                                            @endif
                                            <option value="{{ $academicLevel->id }}">{{ $academicLevel->level }} Level</option>
                                        @endforeach
                                    </select>
                                </div>
    
                                <div class="mb-3">
                                    <label for="choices-batch-input" class="form-label">Batch</label>
                                    <select class="form-select" name="batch" id="choices-batch-input" data-choices data-choices-search-false required>
                                        <option value="" selected>Choose...</option>
                                        <option value="A">Batch A</option>
                                        <option value="B">Batch B</option>
                                        <option value="C">Batch C</option>
                                    </select>
                                </div>
    
                                <div class="mb-3">
                                    <label for="choices-publish-status-input" class="form-label">Manage Application</label>
                                    <select class="form-select" name="status" id="choices-publish-status-input" data-choices data-choices-search-false required>
                                        <option value="" selected>Choose...</option>
                                        <option value="Admitted">Admitted</option>
                                        <option value="Declined">Declined</option>
                                    </select>
                                </div>
    
                                <hr>
                                <button type="submit" id="submit-button" class="btn btn-lg btn-primary"> Submit</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div id="edit{{$applicant->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Update Applicant</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <hr>
                <form action="{{ url('/admin/updateApplicant') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input name="user_id" type="hidden" value="{{ $applicant->id }}">
                    
                    <span class="text-muted"> Bio Data</span><br>
                    
                    <div class="row mt-3 g-3">
                        <!-- Lastname -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="lastname" name="lastname" value="{{ $applicant->lastname }}">
                                <label for="lastname">Lastname (Surname)</label>
                            </div>
                        </div>
                
                        <!-- Othernames -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="othernames" name="othernames" value="{{ $applicant->othernames }}">
                                <label for="othernames">Othernames</label>
                            </div>
                        </div>
                
                        <!-- Email -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" value="{{ $applicant->email }}">
                                <label for="email">Email</label>
                            </div>
                        </div>
                
                
                        <!-- Phone Number -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $applicant->phone_number }}">
                                <label for="phone_number">Mobile Number</label>
                            </div>
                        </div>
                
                
                        <!-- Date of Birth -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="dob" name="dob" 
                                                               value="{{ isset($applicant->dob) ? substr($applicant->dob, 0, 10) : '' }}" 
                                                               required max="{{ date('Y-m-d', strtotime('-15 years')) }}" />
                                <label for="dob">Date of Birth</label>
                            </div>
                        </div>
                
                        <!-- Gender -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <select class="form-control" id="gender" name="gender">
                                    <option value="Male" @if($applicant->gender == 'Male') selected @endif>Male</option>
                                    <option value="Female" @if($applicant->gender == 'Female') selected @endif>Female</option>
                                </select>
                                <label for="gender">Gender</label>
                            </div>
                        </div>
                
                        <!-- Address -->
                        <div class="col-lg-12">
                            <div class="form-floating">
                                <input type="text" class="form-control ckeditor" id="address" name="address" value="{!! $applicant->address !!}">
                                <label for="address">Address</label>
                            </div>
                        </div>
                
                        <!-- Academic Information -->
                        <span class="text-muted"> Academic Information</span><br>
                
                        <!-- Faculty -->
                        <div class="mb-3">
                            <label for="programme" class="form-label">Select Applicant Programme</label>
                            <select class="form-select" aria-label="programme" name="programme_id">
                                @foreach($programmes as $programme)
                                <option @if($applicant->programe_id == $programme->id) selected @endif value="{{ $programme->id }}">{{ $programme->name }}</option>
                                @endforeach
                            </select>
                        </div>

                         <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="sitting_no" name="sitting_no" value="{{ $applicant->sitting_no }}">
                                <label for="sitting_no">Number of Olevel Sitting</label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="jamb_reg_no" class="form-control" id="jamb_reg_no" name="jamb_reg_no" value="{{ $applicant->jamb_reg_no }}">
                                <label for="jamb_reg_no">Jamb Registration Number</label>
                            </div>
                        </div>
            
                        
                
                        <!-- Submit Button -->
                        <div class="col-lg-12 border-top border-top-dashed">
                            <div class="d-flex align-items-start gap-3 mt-3">
                                <button type="submit" id="submit-button" class="btn btn-primary btn-label right ms-auto nexttab">
                                    <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endforeach

<div id="searchApplicant" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Filter Applicants</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/applicantWithSession') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="session" class="form-label">Session</label>
                        <input type="text" class="form-control" placeholder="2022/2023" name="session" id="session">
                    </div>

                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="createApplicant" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Create Applicant</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/createNewApplicant') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row mt-2 g-3">
                        <span class="text-muted border-top border-top-dashed pt-3"> Bio Data</span>

                        <!-- Lastname -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="lastname" name="lastname">
                                <label for="lastname">Lastname (Surname)</label>
                            </div>
                        </div>
                
                        <!-- Othernames -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="othernames" name="othernames">
                                <label for="othernames">Othernames</label>
                            </div>
                        </div>
                
                        <!-- Email -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email">
                                <label for="email">Email</label>
                            </div>
                        </div>
                
                
                        <!-- Phone Number -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="phone_number" name="phone_number">
                                <label for="phone_number">Mobile Number</label>
                            </div>
                        </div>

                        <span class="text-muted border-top border-top-dashed pt-3"> Authentication</span>

                        <!-- Email -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password" name="password">
                                <label for="password">Password</label>
                            </div>
                        </div>
                
                
                        <!-- Phone Number -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password" name="password_confirmation">
                                <label for="password">Confirm Password</label>
                            </div>
                        </div>

                        <span class="text-muted">Application Type</span>

                        <div class="mb-3 border-top border-top-dashed pt-3">
                            <label for="applicationType" class="form-label">Select Application Type<span class="text-danger">*</span></label>
                            <select class="form-select" aria-label="applicationType" name="applicationType" required>
                                <option value= "" selected>Select Application Type</option>
                                <option value="General Application">General Application(UTME & DE)</option>
                                <option value="Inter Transfer Application">Inter Transfer Application</option>
                            </select>
                        </div>

                
                        <!-- Submit Button -->
                        <div class="col-lg-12 border-top border-top-dashed">
                            <div class="d-flex align-items-start gap-3 mt-3">
                                <button type="submit" id="submit-button" class="btn btn-primary btn-label right ms-auto nexttab">
                                    <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection
