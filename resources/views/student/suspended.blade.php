@extends('student.layout.dashboard')

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
                            <h4 class="mt-4 text-danger fw-semibold">Student Suspended</h4>
                            <p class="text-muted mt-3">
                                {{ $message }}
                                <p>
                                <strong>Reason:</strong>{!! $reason !!}
                                </p>
                                <br>
                                <strong>Effective from:</strong> {{ Carbon\Carbon::parse($suspension_start)->format('d, M Y') }}
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