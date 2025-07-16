@extends('staff.layout.dashboard')
@php
    $programmeCategoryModel = new \App\Models\ProgrammeCategory;
    $applicationSession = $programmeCategory->academicSessionSetting->application_session;


    $staff = Auth::guard('staff')->user();
    $staffId = $staff->id;

    $staffRegistrarRole = false;
    $staffAdmissionOfficerRole = false;
    

    foreach ($staff->staffRoles as $staffRole) {
        if (strtolower($staffRole->role->role) == 'registrar') {
            $staffRegistrarRole = true;
        }
        if(strtolower($staffRole->role->role) == 'admission'){
            $staffAdmissionOfficerRole = true;
        }
    }

    $canGiveAdmission = $staffRegistrarRole || $staffAdmissionOfficerRole;

@endphp
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $programmeCategory->category }} Programme Applicants for {{ empty($session)?$applicationSession : $session }} Application Session</h4>

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
                <h4 class="card-title mb-0 flex-grow-1">Applicants for {{ empty($session)?$applicationSession : $session }} Application Session </h4>
                <div class="flex-shrink-0">
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
                            <th scope="col">DOB</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Application Type</th>
                            <th scope="col">Email</th>
                            <th scope="col">Access Code</th>
                            <th scope="col">Phone Number</th>
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
                            <td>{{ date('F j, Y', strtotime($applicant->dob)) }} </td>
                            <td>{{ $applicant->gender }} </td>
                            <td>{{ !empty($applicant->programme)?$applicant->programme->name:null }}</td>
                            <td>{{ $applicant->application_type }}</td>
                            <td>{{ $applicant->email }} </td>
                            <td>{{ $applicant->passcode }} </td>
                            <td>{{ $applicant->phone_number }} </td>
                            <td>{{ $applicant->academic_session }} </td>
                            <td>{{ ucwords($applicant->status) }} </td>
                            <td>{{ $applicant->created_at }} </td>
                            <td>
                                @if(!empty($applicant->programme_id))<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#view{{$applicant->id}}" class="btn btn-secondary m-1"><i class= "ri-eye-fill"></i> View</a>@endif
                                @if(!empty($applicant->programme_id))<a href="{{ url('staff/applicant/'.$applicant->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Applicant</a>@endif
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
    
                    @if($programmeCategory->id == $programmeCategoryModel::getProgrammeCategory('Undergraduate') || $programmeCategory->id == $programmeCategoryModel::getProgrammeCategory('Topup'))
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
                    @elseif($programmeCategory->id != $programmeCategoryModel::getProgrammeCategory('Topup'))
                    <div class="col-md-5 border-end">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                Uploaded Documents
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @php
                                        $documents = [
                                            'O-Level Certificate' => $applicant->olevel_certificate ?? null,
                                            'Degree Certificate' => $applicant->degree_certificate ?? null,
                                            'NYSC Certificate' => $applicant->nysc_certificate ?? null,
                                            'Academic Transcript' => $applicant->academic_transcript ?? null,
                                            'Masters Certificate' => $applicant->masters_certificate ?? null,
                                            'Research Proposal' => $applicant->research_proposal ?? null,
                                        ];
                                    @endphp

                                    @foreach ($documents as $label => $file)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $label }}
                                            @if ($file)
                                                <a href="{{ asset($file) }}" target="_blank" class="btn btn-sm btn-outline-success">View</a>
                                            @else
                                                <span class="badge bg-secondary">Not Uploaded</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <div class="card-body">
                            <h5 class="fs-14 mb-3 border-bottom"> Manage Admission</h5>
                            @if($applicant->status == 'submitted' && $canGiveAdmission)
                            <form action="{{ url('staff/manageAdmission') }}" method="POST">
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
@endforeach


<div id="searchApplicant" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Filter Applicants</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/staff/applicantWithSession') }}" method="post" enctype="multipart/form-data">
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

@endsection
