@extends('guardian.layout.dashboard')
@php
$guardian = Auth::guard('guardian')->user();
$name = $guardian->name;
$applicants = $guardian->applicants->where('status', 'Admitted');
@endphp
@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Students</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Students</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Students </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Matric Number</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Email</th>
                            <th scope="col">Level</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applicants as $applicant)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $applicant->lastname .' '. $applicant->othernames }}</td>
                            <td>{{ $applicant->student->matric_number }}</td>
                            <td>{{ $applicant->student->programme->name }}</td>
                            <td>{{ $applicant->student->email }} </td>
                            <td>{{ $applicant->student->academicLevel->level }} </td>
                            <td>{{ $applicant->phone_number }} </td>
                            <td>
                                <a href="{{ url('guardian/studentProfile/'.$applicant->student->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Student</a>
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
<!-- end row -

@endsection