@extends('student.layout.dashboard')
<?php 
    $student = Auth::guard('student')->user();
?>
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Anti Drug Declaration</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Anti Drug Declaration</li>
                </ol>
            </div>

        </div>
    </div>
</div>


@if(empty($student->signature))
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Student Signature</h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                                Please upload your signature to complete the <strong> Anti-Drug Declaration </strong> form.
                            </div>
                            <div class="mt-4">
                                <a href="{{ url('student/profile') }}" class="btn btn-primary">Upload Signature</a>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5 mb-2">
                        <div class="col-sm-7 col-8">
                            <img src="{{asset('assets/images/terms.png')}}" alt="" class="img-fluid" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
@endif

@if(!empty($student->anti_drug_status))
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Anti Drug Declaration </h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                                Your <strong>Anti-Drug Declaration</strong> Form has been duly signed! Click the button below to download, print, and submit the form to the Admission Office.
                            </div>
                            <div class="mt-4">
                                <a href="{{ asset($student->anti_drug_status) }}" target="_blank" class="btn btn-primary">Click here to download</a>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5 mb-2">
                        <div class="col-sm-7 col-8">
                            <img src="{{asset('assets/images/terms.png')}}" alt="" class="img-fluid" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
@endif

@if(empty($student->anti_drug_status))
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center mt-5 mb-2">
                        <h4 class="mt-4 fw-semibold">Anti Drug Declaration Form</h4>
                        <hr>
                        <div class="col-sm-7 col-8">
                            <img src="{{asset('assets/images/terms.png')}}" width="50%" alt="" class="img-fluid" />
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <p class="text-muted mt-3"></p>
                            <div class="mt-2 text-start">
                                I, <strong>{{ $student->applicant->lastname.', '.$student->applicant->othernames}}</strong> a student admitted to the <strong>{{ $student->programme->name }}</strong> of {{env('SCHOOL_NAME')}}, hereby declare that I have been made aware that possessing, consuming, or dealing in narcotic and intoxicating drugs is an offense punishable by expulsion under Section 10.3, VII of the Student’s Handbook of Information and Regulations. <br><br> In the event of such indulgence or suspicion, I am willing to undergo medical examination, including blood tests and urine analysis, as required by the institution.
                                <br><br> I also commit to reporting any irregular behavior related to the possession, use, sale, or distribution of alcohol, tobacco, or any psychoactive/psychotropic substances that may occur within the institution or during activities involving its students.
                            </div>
                            <div class="mt-4">
                                <form action="{{ url('/student/antiDrugDeclaration') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="agreeCheckbox" name="anti_drug_status" required>
                                        <label class="form-check-label" for="agreeCheckbox">
                                            I agree to the Anti-Drug Declaration.
                                        </label>
                                    </div>
                                    <hr>
                                    <button type="submit" id="submit-button" class="btn btn-primary mt-2" disabled>
                                        Submit
                                    </button>
                                </form>
                            </div>
                            
                            <script>
                                document.getElementById('agreeCheckbox').addEventListener('change', function() {
                                    document.getElementById('submit-button').disabled = !this.checked;
                                });
                            </script>
                        </div>
                    </div>

                   
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
@endif



@endsection