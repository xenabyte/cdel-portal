@extends('admin.layout.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Admitted Students for {{ $pageGlobalData->sessionSetting->admission_session}} Admission Session</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">students-</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Admitted Students for {{ $pageGlobalData->sessionSetting->admission_session}} Admission Session </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Application Number</th>
                            <th scope="col">Matric Number</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Email</th>
                            <th scope="col">Access Code</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Academic Session</th>
                            <th scope="col">Admitted Date</th>
                            <th scope="col">Clearance Status</th>
                            <th scope="col">Acceptance Fee Status</th>
                            <th scope="col">Admission Letter</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</td>
                            <td>{{ $student->applicant->application_number }}</td>
                            <td>{{ $student->applicant->matric_number }}</td>
                            <td>{{ $student->applicant->gender }}</td>
                            <td>{{ $student->programme->name }}</td>
                            <td>{{ $student->email }} </td>
                            <td> 
                                {{ $student->passcode }} 
                                <form action="{{ url('admin/refreshPasscode') }}" style="display:inline" method="POST">
                                    @csrf
                                    <input name="student_id" type="hidden" value="{{ $student->id }}">
                                    <button type="submit" id="submit-button" class="btn btn-danger">
                                        <i class="ri-refresh-line"></i>
                                    </button>
                                </form>
                            </td>
                            <td>{{ $student->applicant->phone_number }} </td>
                            <td>{{ $student->academic_session }} </td>
                            <td>{{ $student->created_at }} </td>
                            <td>
                                @if($student->clearance_status == 1)Cleared @else Not Cleared @endif
                            </td>
                            <td>
                                @if($student->acceptanceFeeStatus)
                                    <span class="badge bg-success p-2 rounded-pill">Paid</span>
                                @else
                                    <span class="badge bg-danger p-2 rounded-pill">Not Yet Paid</span>
                                @endif

                            </td>
                            <td>
                                <a href="{{ asset($student->admission_letter) }}" class="btn btn-danger m-1"> Download Admission Letter</a>
                                <form action="{{ url('admin/generateAdmissionLetter') }}" method="POST">
                                    @csrf
                                    <input name="applicant_id" type="hidden" value="{{$student->user_id}}">
                                    <hr>
                                    <button type="submit" id="submit-button" class="btn btn-primary w-100">Regenerate Admission Letter</button>
                                </form>
                            </td>
                            <td>
                                @if(!empty($student->clearance_status))<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#view{{$student->applicant->id}}" class="btn btn-secondary m-1"><i class= "ri-eye-fill"></i> View Clearance</a>@endif
                                <a href="{{ url('admin/student/'.$student->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Student</a>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$student->id}}" class="btn btn-danger m-1"><i class="ri-delete-bin-5-line"></i> Reverse Admission</a>
                            </td>
                        </tr>

                        <div id="delete{{$student->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-body text-center p-5">
                                        <div class="text-end">
                                            <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="mt-2">
                                            <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                                            </lord-icon>
                                            <h4 class="mb-3 mt-4">Are you sure you want to reverse admission for <br/> {{ $student->applicant->lastname .' '. $student->applicant->othernames }}?</h4>
                                            <form action="{{ url('admin/manageAdmission') }}" method="POST">
                                                @csrf
                                                <input name="applicant_id" type="hidden" value="{{$student->user_id}}">
                                                <input name="status" type="hidden" value="reverse_admission" />
                                                <hr>
                                                <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Reverse Admission</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light p-3 justify-content-center">

                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@foreach($students as $student)
<div id="view{{$student->applicant->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">View Clearance Application</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3 border-end">
                        <div class="card-body text-center">
                            <div class="avatar-md mb-3 mx-auto">
                                <img src="{{empty($student->applicant->image)?asset('assets/images/users/user-dummy-img.jpg'): asset($student->applicant->image) }}" alt="" id="candidate-img" class="img-thumbnail rounded-circle shadow-none">
                            </div>
    
                            <h5 id="candidate-name" class="mb-0">{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</h5>
                            <p id="candidate-position" class="text-muted">{{ $student->applicant->programme?$student->applicant->programme->name:null }}</p>
                            <p id="candidate-position" class="text-muted">Phone Number: {{ $student->applicant->phone_number }}</p>
                            <div class="vr"></div>
                            <div class="text-muted">Application ID : <span class="text-body fw-medium"> {{ $student->applicant->application_number }}</span></div>
                            @if($student->applicant->application_type == 'UTME')
                            <div class="vr"></div>
                            <div class="text-muted">UTME Scores : <span class="text-body fw-medium"> {{ $student->applicant->utmes->sum('score') }}</span></div>
                            @endif
                            <div class="vr"></div>
                            <div class="text-muted">Application Date : <span class="text-body fw-medium">{{ $student->applicant->updated_at }}</span></div>
                            <hr>
                            <h4 class="mt-3 alert alert-info">Admission Status: {{ empty($student->applicant->status)? 'Processing' : ucwords($student->applicant->status) }}</h4>
                        </div>
                    </div>
    
                    <div class="col-md-3 border-end">
                        <div class="card-body">
                            @if(!empty($student->applicant->olevel_1))
                            <h5 class="fs-14 mb-3"> Schools Attended</h5>
                            {!! $student->applicant->schools_attended !!}
                            <hr>
                            <div class="row mb-2">
                                <div class="col-sm-6 col-xl-12">
                                    <!-- Simple card -->
                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 25px"></i><span class="fs-14">Olevel Result</span>
                                    <div class="text-end">
                                        <a href="{{ asset($student->applicant->olevel_1) }}" target="blank" class="btn btn-success">View</a>
                                    </div>
                                    @if($student->applicant->sitting_no > 1)
                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 25px"></i><span class="fs-14">Olevel Result (Second Sitting)</span>
                                    <div class="text-end">
                                        <a href="{{ asset($student->applicant->olevel_2) }}" target="blank"  class="btn btn-success">View</a>
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
                                        @foreach($student->applicant->olevels as $olevel)
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
                            @if($student->applicant->application_type == 'UTME')
                            <div>
                                @if(!empty($student->applicant->utme))
                                <div class="row mb-2">
                                    <div class="col-sm-6 col-xl-12">
                                        <!-- Simple card -->
                                        <i class="bx bxs-file-jpg text-danger" style="font-size: 25px"></i><span class="fs-14">UTME Result Printout</span>
                                        <div class="text-end">
                                            <a href="{{ asset($student->applicant->utme) }}"  target="blank" class="btn btn-success">View</a>
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
                                            @foreach($student->applicant->utmes as $utme)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $utme->subject }}</td>
                                                <td>{{ $utme->score }}</td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <th scope="row"></th>
                                                <td>Total</td>
                                                <td><strong>{{$student->applicant->utmes->sum('score')}}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @elseif($student->applicant->application_type != 'UTME')
                            <div>
                                <h5 class="fs-14 mb-3"> Institution Attended</h5>
                                {!! $student->applicant->de_school_attended !!}
                                <hr>
                                @if(!empty($student->applicant->de_result))
                                <div class="row mb-2">
                                    <div class="col-sm-6 col-xl-12">
                                        <!-- Simple card -->
                                        <i class="bx bxs-file-jpg text-danger" style="font-size: 25px"></i><span class="fs-14">Direct Entry/Prev Institution/Prev Institution Result</span>
                                        <div class="text-end">
                                            <a href="{{ asset($student->applicant->de_result) }}"  target="blank" class="btn btn-success">View</a>
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
                            <h5 class="fs-14 mb-3 border-bottom"> Manage Clearance Application</h5>
                            @if($student->clearance_status == 2)
                            <form action="{{ url('admin/manageClearanceApplication') }}" method="POST">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
    
                                <div class="mb-3">
                                    <label for="choices-publish-status-input" class="form-label">Manage Application</label>
                                    <select class="form-select" name="status" id="choices-publish-status-input" data-choices data-choices-search-false required>
                                        <option value="" selected>Choose...</option>
                                        <option value="1">Cleared</option>
                                        <option value="3">Not Cleared/Reverse Clearance Application </option>
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

@endsection