@extends('student.layout.dashboard')
<?php 
    $student = Auth::guard('student')->user();
?>
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Examination Cards</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Examination Cards</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Examination Cards </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table class="display table table-stripped" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Academic Session</th>
                            <th scope="col">Semester</th>
                            {{-- <th scope="col">Level Adviser Status</th>
                            <th scope="col">HOD Status</th> --}}
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentExamCards as $studentExamCard)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $studentExamCard->academic_session }}</td>
                            <td>{{ $studentExamCard->semester == '1' ? 'First' : 'Second' }} Semester</td>
                            {{-- <td><span class="badge badge-soft-{{ $studentExamCard->level_adviser_status == 1 ? 'success' : 'warning' }}">{{ $studentExamCard->level_adviser_status == 1 ? 'Approved' : 'Pending' }}</span></td>
                            <td><span class="badge badge-soft-{{ $studentExamCard->status == 1 ? 'success' : 'warning' }}">{{ $studentExamCard->hod_status == 1 ? 'Approved' : 'Pending' }}</span></td> --}}
                            <td>
                                <a href="{{ asset($studentExamCard->file) }}" target="_blank" style="margin: 5px" class="btn btn-warning">Download Form</a>
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

@endsection