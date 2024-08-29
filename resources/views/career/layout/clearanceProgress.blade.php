@php
    $imageStatus = !empty($career->image)?1:0;
    $educationHistory = !empty($career->profile->education_history)?1:0;
    $professionalInformation = !empty($career->professional_information)?1:0;
    $publications = !empty($career->profile->publications)?1:0;
    $cvPath = !empty($career->profile->cv_path)?1:0;  

@endphp
<div class="col-xxl-4">
    @if($percent != 100)
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center mb-5">
                <div class="flex-grow-1">
                    <h5 class="card-title mb-0">Complete Your profile</h5>
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
    @else
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center mb-5">
                <div class="flex-grow-1">
                    <h5 class="card-title mb-0">Profile Complete</h5>
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


    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Info</h5>
            <hr>
            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <th class="ps-0" scope="row">Full Name: </th>
                            <td class="text-muted">{{ $career->lastname.' '. $career->othernames}}</td>
                        </tr>
                        <tr>
                            <th class="ps-0" scope="row">Phone Number: </th>
                            <td class="text-muted">{{ $career->phone_number }}</td>
                        </tr>
                        <tr>
                            <th class="ps-0" scope="row">E-mail: </th>
                            <td class="text-muted">{{ $career->email }}</td>
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

            <a href="#educationHistory">
                <div class="alert alert-{{ $educationHistory?'success':'danger' }} shadow" role="alert">
                    <strong>{{ $educationHistory?'Good Job! Education History Added Successfully':'You are yet to add your education history' }}</strong>
                </div>
            </a>
            
            <a href="#professionalInformation">
                <div class="alert alert-{{ $professionalInformation?'success':'danger' }} shadow" role="alert">
                    <strong>{{ $professionalInformation?'Good Job! Professional Infomation Added Successfully':'You are yet to add your Professional Information' }}</strong>
                </div>
            </a>

            <a href="#publications">
                <div class="alert alert-{{ $publications?'success':'danger' }} shadow" role="alert">
                    <strong>{{ $publications?'Good Job! Publication Added Successfully':'You are yet to add your publications' }}</strong>
                </div>
            </a>

            <a href="#cvPath">
                <div class="alert alert-{{ $cvPath?'success':'danger' }} shadow" role="alert">
                    <strong>{{ $cvPath?'Good Job! CV uploaded successfully':'You are yet to upload your CV' }}</strong>
                </div>
            </a>
        </div>
    </div>
</div>