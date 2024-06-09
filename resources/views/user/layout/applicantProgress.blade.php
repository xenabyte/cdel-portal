@php
    $imageStatus = !empty($applicant->image)?1:0;
    $programmeStatus = !empty($applicant->programme_id)?1:0;
    $guardianStatus = !empty($applicant->guardian_id)?1:0;
    $nokStatus = !empty($applicant->next_of_kin_id)?1:0;
    $utmeStatus = count($applicant->utmes) > 3 ? 1 : 0;
    $utmeResultStatus = !empty($applicant->utme)?1:0;
    $deResultStatus = !empty($applicant->de_result)?1:0;  
    $olevelStatus = count($applicant->olevels) > 4?1:0;
    $olevelResultStatus = !empty($applicant->olevel_1)?1:0;  

@endphp
<div class="col-xxl-4">
    @if($percent != 100 && empty($applicant->status))
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center mb-5">
                <div class="flex-grow-1">
                    <h5 class="card-title mb-0">Complete Your Application</h5>
                </div>
                <div class="flex-shrink-0">
                </div>
            </div>
            <div class="progress animated-progress custom-progress progress-label">
                <div class="progress-bar bg-danger" role="progressbar" style="width: {{$percent}}%" aria-valuenow="{{$percent}}" aria-valuemin="0" aria-valuemax="100">
                    <div class="label">{{$percent}}%</div>
                </div>
            </div>
        </div>
    </div>
    @elseif(strtolower($applicant->status) == 'admitted')
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center mb-5">
                <div class="flex-grow-1">
                    <h5 class="card-title mb-0">Congratulations</h5>
                </div>
                <div class="flex-shrink-0">
                </div>
            </div>
            <div class="d-flex align-items-center text-center mb-5">
                <div class="flex-grow-1">
                    <i class="fa fa-check fa-5x text-success"></i><br>
                    <p class="muted">You have been granted admission, download Admission Letter from the button below and proceed to student dashboard.</p>
                </div>
            </div>
        </div>
    </div>
    @elseif(strtolower($applicant->status) == 'submitted')
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center mb-5">
                <div class="flex-grow-1">
                    <h5 class="card-title mb-0">Application form completed submitted, Pending Admission</h5>
                </div>
                <div class="flex-shrink-0">
                </div>
            </div>
            <div class="d-flex align-items-center text-center mb-5">
                <div class="flex-grow-1">
                    <i class="fa fa-spinner fa-spin fa-5x text-danger"></i>
                </div>
            </div>
            
        </div>
    </div>
    @endif

    @if($percent >= 100 && empty($applicant->status))
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center mb-1">
                <div class="flex-grow-1">
                    <h5 class="card-title mb-0">Submit Application</h5>
                    <hr>
                </div>
            </div>
            <div class="row">
                <!-- Warning Alert -->
                <div class="alert alert-warning alert-dismissible alert-additional shadow fade show mb-0" role="alert">
                    <div class="alert-body">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <i class="ri-alert-line fs-16 align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading">Ensure you have added all information</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert-content">
                        <p class="mb-0">You will not be able to update the information after clicking "Submit Application"</p>
                    </div>
                </div>
                <div class="col-md-12">
                    <br>
                    <form action="{{ url('applicant/submitApplication') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <button type="submit" id="submit-button" class="btn btn-block btn-primary">Submit Applicattion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif


    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Info</h5>
            <hr>
            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <th class="ps-0" scope="row">Full Name: </th>
                            <td class="text-muted">{{ $applicant->lastname.' '. $applicant->othernames}}</td>
                        </tr>
                        <tr>
                            <th class="ps-0" scope="row">Phone Number: </th>
                            <td class="text-muted">{{ $applicant->phone_number }}</td>
                        </tr>
                        <tr>
                            <th class="ps-0" scope="row">E-mail: </th>
                            <td class="text-muted">{{ $applicant->email }}</td>
                        </tr>
                        <tr>
                            <th class="ps-0" scope="row">Academic session: </th>
                            <td class="text-muted">{{ $applicant->academic_session }}</td>
                        </tr>
                        <tr>
                            <th class="ps-0" scope="row">Programmme: </th>
                            <td class="text-muted">@if(!empty($applicant->programme)){{ $applicant->programme->name }}@endif</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div><!-- end card body -->
    </div><!-- end card -->

    <div class="card card-h-100">
        <div class="card-body">
            <a href="#overview-tab">
                <div class="alert alert-{{ $imageStatus?'success':'danger' }} shadow" role="alert">
                    <strong> {{ $imageStatus?'Image Upload Successful':'Pending Image Upload' }}</strong>
                </div>
            </a>

            <a href="#programme">
                <div class="alert alert-{{ $programmeStatus?'success':'danger' }} shadow" role="alert">
                    <strong>{{ $programmeStatus?'Good Job! Programme of study selected':'You are yet to select a programme of choice' }}</strong>
                </div>
            </a>
            
            @if(strtolower($applicant->application_type) == 'utme')
            <a href="#jamb">
                <div class="alert alert-{{ $utmeStatus?'success':'danger' }} shadow" role="alert">
                    <strong>{{ $utmeStatus?'Good Job! UTME Result added successfully':'You are yet to add your Jamb Result' }}</strong>
                </div>
            </a>

            <a href="#jamb">
                <div class="alert alert-{{ $utmeResultStatus?'success':'danger' }} shadow" role="alert">
                    <strong>{{ $utmeResultStatus?'Good Job! UTME Result uploaded successfully':'You are yet to upload your Jamb Result Printout' }}</strong>
                </div>
            </a>
            @endif

            @if(strtolower($applicant->application_type) == 'de')
            <a href="#jamb">
                <div class="alert alert-{{ $deResultStatus?'success':'danger' }} shadow" role="alert">
                    <strong>{{ $deResultStatus?'Good Job! DE/A-Level Result uploaded successfully':'You are yet to upload your DE/A-Level Result' }}</strong>
                </div>
            </a>
            @endif

            <a href="#olevel">
                <div class="alert alert-{{ $olevelStatus?'success':'danger' }} shadow" role="alert">
                    <strong>{{ $olevelStatus?'Good Job! OLevel Result added successfully':'You are yet to fill your OLevel Result' }}</strong>
                </div>
            </a>

            <a data-bs-toggle="tab" href="#olevel">
                <div class="alert alert-{{ $olevelResultStatus?'success':'danger' }} shadow" role="alert">
                    <strong>{{ $olevelResultStatus?'Good Job! OLevel Result uploaded successfully':'You are yet to upload your Olevel Result printout (optional for awaiting result)' }}</strong>
                </div>
            </a>

            <a href="#guardian">
                <div class="alert alert-{{ $guardianStatus?'success':'danger' }} shadow" role="alert">
                    <strong>{{ $guardianStatus?'Good Job! Guardian details added successfully':'You are yet to add Guardian details' }}</strong>
                </div>
            </a>

            <a href="#nok">
                <div class="alert alert-{{ $nokStatus?'success':'danger' }} shadow" role="alert">
                    <strong>{{ $nokStatus?'Good Job! Next of kin details added successfully':'You are yet to add Next of Kin details' }}</strong>
                </div>
            </a>
        </div>
    </div>
</div>