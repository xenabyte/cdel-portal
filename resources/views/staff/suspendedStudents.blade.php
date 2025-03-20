@extends('staff.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Suspended Students</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Suspended Students</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Suspended Students </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Support Code</th>
                            <th scope="col">CGPA</th>
                            <th scope="col">Image</th>
                            <th scope="col">Name</th>
                            <th scope="col">Level</th>
                            <th scope="col">Passcode</th>
                            <th scope="col">Matric Number</th>
                            <th scope="col">Application Number</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Email</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suspensions as $suspension)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td><span class="text-danger">#{{ $suspension->student->id }}</span></td>
                            <td><span class="text-primary">{{ $suspension->student->cgpa }}</span></td>
                            <td>
                                <img class="img-thumbnail rounded-circle avatar-md"  src="{{ !empty($suspension->student->image) ? asset($suspension->student->image) : asset('assets/images/users/user-dummy-img.jpg') }}">
                            </td>
                            <td>{{ $suspension->student->applicant? $suspension->student->applicant->lastname .' '. $suspension->student->applicant->othernames : null }}</td>
                            <td>{{ $suspension->student->academicLevel->level }} </td>
                            <td>{{ $suspension->student->passcode }} </td>
                            <td>{{ $suspension->student->matric_number }}</td>
                            <td>{{ $suspension->student->applicant? $suspension->student->applicant->application_number:null }}</td>
                            <td>{{ $suspension->student->programme->name }}</td>
                            <td>{{ $suspension->student->email }} </td>
                            <td>
                                <a href="{{ url('admin/viewSuspension/'.$suspension->slug) }}" class="btn btn-sm btn-primary"><i class= "ri-eye-fill"></i> View Suspension</a>
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
