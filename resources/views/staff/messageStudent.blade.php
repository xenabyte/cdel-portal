@extends('staff.layout.dashboard')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Message student</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Message student</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Get Student Information</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#getStudent">Get Student</button>
                </div>
            </div><!-- end card header -->
        </div><!-- end card -->
    </div>
</div>
<!-- end row -->
@if(!empty($student))
<div class="row">
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-body p-4">
                <div>
                    <div class="flex-shrink-0 avatar-md mx-auto">
                        <div class="avatar-title bg-light rounded">
                            <img src="{{empty($student->image)?asset('assets/images/users/user-dummy-img.jpg'):asset($student->image)}}" alt="" height="50" />
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <h5 class="mb-1">{{$student->applicant->lastname.' '.$student->applicant->othernames}}</h5>
                        <p class="text-muted">{{ $student->programme->name }} <br>
                            <strong>Programme Category:</strong> {{ $student->programmeCategory->category }} Programme<br>
                            <strong>Matric Number:</strong> {{ $student->matric_number }}<br>
                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }}<br>
                            <strong>Support Code:</strong> <span class="text-danger">{{ $student->applicant->id }}-ST{{ sprintf("%03d", $student->id) }}</span> 
                            <hr>
                            @if(env('WALLET_STATUS'))<a class="dropdown-item" href="#"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>₦{{ number_format($student->amount_balance/100, 2) }}</b></span></a>@endif
                        </p>
                    </div>
                    <div class="table-responsive border-top border-top-dashed">
                        <table class="table mb-0 table-borderless">
                            <tbody>
                                <tr>
                                    <th><span class="fw-medium">Department:</span></th>
                                    <td>{{ $student->department->name }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Faculty:</span></th>
                                    <td>{{ $student->faculty->name }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Email:</span></th>
                                    <td>{{ $student->email }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Contact No.:</span></th>
                                    <td>{{ $student->applicant->phone_number }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Address:</span></th>
                                    <td>{!! $student->applicant->address !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if(!empty($student->applicant->guardian))
            <div class="card-body border-top border-top-dashed p-4">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold mb-4">Guardian Info</h6>
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <tbody>
                                <tr>
                                    <th><span class="fw-medium">SN</span></th>
                                    <td class="text-danger">#{{ $student->applicant->guardian->id }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Name</span></th>
                                    <td>{{ $student->applicant->guardian->name }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Email</span></th>
                                    <td>{{ $student->applicant->guardian->email }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Contact No.</span></th>
                                    <td>{{ $student->applicant->guardian->phone_number }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Address</span></th>
                                    <td>{!! $student->applicant->guardian->address !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <!--end card-->
    
    </div>
    <!--end col-->

    <div class="col-xxl-8">
        {{-- Student Message --}}
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Send Student Direct Message</h4>
                <div class="text-end mb-5">
                    
                </div>
            </div><!-- end card header -->

            <div class="card-body pb-2 border-top border-top-dashed">
                <form action="{{ url('/staff/sendStudentMessage') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="url" value="staff.messageStudent">
                    <input type="hidden" name="studentId" value="{{ $student->id }}">
                    
                    <div class="mb-3">
                        <label for="type" class="form-label">Select message Type</label>
                        <select class="form-select" aria-label="type" name="type" id="messageType" required>
                            <option selected value="">Select type</option>
                            <option value="sms">SMS</option>
                            <option value="email">Email</option>
                            <option value="both">Both</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input type="checkbox" class="form-check-input" name="copy_guardian" id="copyGuardian">
                            <label class="form-check-label" for="copyGuardian">Copy Student's Guardian</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input type="checkbox" class="form-check-input" id="attachFileCheckbox">
                            <label class="form-check-label" for="attachFileCheckbox">Attach File</label>
                        </div>
                    </div>                    

                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <input type="text" class="form-control" name="message" id="message">
                    </div>

                    <div class="mb-3" id="attachmentInput" style="display: none;">
                        <label for="Attachment" class="form-label">Attachment</label>
                        <input type="file" class="form-control" name="attachment" id="Attachment">
                    </div>

                    <div class="text-end border-top border-top-dashed p-3">
                        <br>
                        <button type="submit" id="submit-button" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div><!-- end card body -->
        </div><!-- end card -->

        @if(!empty($student->applicant->guardian))
        {{-- Parent Direct Message  --}}
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Send Parent Direct Message</h4>
                <div class="text-end mb-5">
                    
                </div>
            </div><!-- end card header -->

            <div class="card-body pb-2 border-top border-top-dashed">
                <form action="{{ url('/staff/sendParentMessage') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="url" value="staff.messageStudent">
                    <input type="hidden" name="guardianId" value="{{ $student->applicant->guardian->id }}">
                    <input type="hidden" name="studentId" value="{{ $student->id }}">
                    
                    <div class="mb-3">
                        <label for="type" class="form-label">Select message Type</label>
                        <select class="form-select" aria-label="type" name="type" id="messageType" required>
                            <option selected value="">Select type</option>
                            <option value="sms">SMS</option>
                            <option value="email">Email</option>
                            <option value="both">Both</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input type="checkbox" class="form-check-input" id="attachParentFileCheckbox">
                            <label class="form-check-label" for="attachParentFileCheckbox">Attach File</label>
                        </div>
                    </div>                    

                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <input type="text" class="form-control" name="message" id="message">
                    </div>

                    <div class="mb-3" id="attachmentParentInput" style="display: none;">
                        <label for="Attachment" class="form-label">Attachment</label>
                        <input type="file" class="form-control" name="attachment" id="Attachment">
                    </div>

                    <div class="text-end border-top border-top-dashed p-3">
                        <br>
                        <button type="submit" id="submit-button" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div><!-- end card body -->
        </div><!-- end card -->
        @endif
    </div>
    <!--end col-->
    
</div>
<!--end row-->
@endif


<div id="getStudent" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Get Student</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/staff/communication/getStudent') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="url" value="staff.messageStudent">
                    <div class="mb-3">
                        <label for="reg" class="form-label">Application/Matric Number</label>
                        <input type="text" class="form-control" name="reg_number" id="reg">
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Select  Type</label>
                        <select class="form-select" aria-label="type" name="type" required>
                            <option selected value= "">Select type </option>
                            <option value="Student">Student</option>
                        </select>
                    </div>
                    <div class="text-end border-top border-top-dashed p-3">
                        <br>
                        <button type="submit" id="submit-button" class="btn btn-primary">Get student</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    $(document).ready(function() {
        $('#messageType').change(function() {
            var selectedType = $(this).val();
            if (selectedType === 'email' || selectedType === 'both') {
                $('#attachFileCheckbox').prop('disabled', false);
            } else {
                $('#attachFileCheckbox').prop('disabled', true);
                $('#attachFileCheckbox').prop('checked', false);
                $('#attachmentInput').hide();
            }
        });

        $('#attachFileCheckbox').change(function() {
            if ($(this).prop('checked')) {
                $('#attachmentInput').show();
            } else {
                $('#attachmentInput').hide();
            }
        });

        $('#attachParentFileCheckbox').change(function() {
            if ($(this).prop('checked')) {
                $('#attachmentParentInput').show();
            } else {
                $('#attachmentParentInput').hide();
            }
        });
    });
</script>
@endsection