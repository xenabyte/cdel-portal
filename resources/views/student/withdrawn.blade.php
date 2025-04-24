@extends('student.layout.dashboard')
@php
    $student = Auth::guard('student')->user();
@endphp
@section('content')

<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center mt-5 mb-2">
                        <div class="col-sm-7 col-8">
                            <img src="{{asset('tau_cancel.png')}}" alt="" width="50%" class="img-fluid">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 text-danger fw-semibold">Student Withdrawn</h4>
                            <p class="text-muted mt-3">
                                {{ $message }}
                                <p>
                                <strong>Reason:</strong>{!! $reason !!}
                                </p>
                                <br>
                                
                                <div class="col-lg-12 mt-2 border-top border-top-dashed">
                                    <div class="d-flex mt-3 justify-content-center">
                                        <a href="{{ url('student/viewWithdrawal/'.$student->slug) }}" class="btn btn-primary btn-label nexttab right" data-nexttab="pills-bill-address-tab">
                                            <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Apply for re-admission
                                        </a>
                                    </div>
                                </div>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>


@endsection