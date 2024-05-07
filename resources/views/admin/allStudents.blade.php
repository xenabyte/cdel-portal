@extends('admin.layout.dashboard')

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
                            <th scope="col">Support Code</th>
                            <th scope="col">CGPA</th>
                            {{-- <th scope="col">Image</th> --}}
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
                        @foreach($students as $student)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td><span class="text-danger">#{{ $student->id }}</span></td>
                            <td><span class="text-primary">{{ $student->cgpa }}</span></td>
                            {{-- <td>
                                <img class="img-thumbnail rounded-circle avatar-md"  src="{{ !empty($student->image) ? asset($student->image) : asset('assets/images/users/user-dummy-img.jpg') }}">
                            </td> --}}
                            <td>{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</td>
                            <td>{{ $student->academicLevel->level }} </td>
                            <td>{{ $student->passcode }} </td>
                            <td>{{ $student->matric_number }}</td>
                            <td>{{ $student->applicant->application_number }}</td>
                            <td>{{ $student->programme->name }}</td>
                            <td>{{ $student->email }} </td>
                            <td>
                                <a href="{{ url('admin/studentProfile/'.$student->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Student</a>
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
