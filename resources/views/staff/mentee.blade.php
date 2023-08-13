@extends('staff.layout.dashboard')
@php
    $mentees = Auth::guard('staff')->user()->mentees; 

@endphp

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Mentee</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Mentee</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Mentee(s)</h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Academic Session</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mentees as $mentee)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $mentee->applicant->lastname .' '. $mentee->applicant->othernames }}</td>
                            <td>{{ $mentee->programme->name }}</td>
                            <td>{{ $mentee->email }} </td>
                            <td>{{ $mentee->applicant->phone_number }} </td>
                            <td>{{ $mentee->academic_session }} </td>
                            <td>
                                <a href="{{ url('staff/studentProfile/'.$mentee->slug)}}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Student</a>
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

@endsection
