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


@if(empty($students))
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Fetch Examination result</h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                                <form action="{{ url('/admin/generateStudentMissingResults') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="programme_category" name="programme_category_id" aria-label="Programme Category">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($programmeCategories as $programmeCategory)<option value="{{ $programmeCategory->id }}">{{ $programmeCategory->category }} Programme</option>@endforeach
                                                </select>
                                                <label for="session">Programme Category</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="faculty" name="faculty_id" aria-label="faculty" onchange="handleFacultyChange(event)">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($faculties as $faculty)
                                                        <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                                    @endforeach
                                                </select>
                                                <label for="faculty">Faculty</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="department" name="department_id" aria-label="department" onchange="handleDepartmentChange(event)">
                                                    <option value="" selected>--Select--</option>
                                                </select>
                                                <label for="department">Department</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="programme" name="programme_id" aria-label="programme">
                                                    <option value="" selected>--Select--</option>
                                                </select>
                                                <label for="department">Programme</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="level" name="level_id" aria-label="level">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($academicLevels as $academicLevel)
                                                        <option value="{{ $academicLevel->id }}">{{ $academicLevel->level }} Level</option>
                                                    @endforeach
                                                </select>
                                                <label for="level">Academic Level</label>
                                            </div>
                                        </div>
        
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="semester" name="semester" aria-label="semester">
                                                    <option value="" selected>--Select--</option>
                                                    <option value="1">Harmattan Semester</option>
                                                    <option value="2">Rain Semester</option>
                                                </select>
                                                <label for="semester">Semester</label>
                                            </div>
                                        </div>
        
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="session" name="session" aria-label="Academic Session">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($academicSessions as $session)<option value="{{ $session->year }}">{{ $session->year }}</option>@endforeach
                                                </select>
                                                <label for="session">Academic Session</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="batch" name="batch" aria-label="Batch">
                                                    <option value="" selected>--Select--</option>
                                                    <option value="A">Batch A</option>
                                                    <option value="B">Batch B</option>
                                                    <option value="C">Batch C</option>
                                                </select>
                                                <label for="batch">Batch</label>
                                            </div>
                                        </div>

                                        <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Get Missing Results</button>
                                    </div>
                                </form>
                            </div>
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

@if(!empty($students))
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Students with missing results </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Level</th>
                            <th scope="col">Matric Number</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Courses with Missing Grades</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</td>
                            <td>{{ $student->email }} </td>
                            <td>{{ $student->academicLevel->level }} </td>
                            <td>{{ $student->matric_number }}</td>
                            <td>{{ $student->programme->name }}</td>
                            <td>
                                @foreach($student->courses_with_missing_grades as $course)
                                {{ $loop->iteration }}. {{ $course->code }}({{ ucwords(strtolower($course->name)) }})<br>
                                @endforeach
                            </td>
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
<!-- end row -->
@endif
@endsection
