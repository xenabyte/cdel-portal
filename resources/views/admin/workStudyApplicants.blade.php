@extends('admin.layout.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Work Study Applicant</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Work Study Applicant</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Work Study Applicant </h4>
                <div class="flex-shrink-0">
                    {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaff"><i class="mdi mdi-account-multiple-plus"></i> Add Staff</button> --}}
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Work Study</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wordStudyApplications as $wordStudyApplication)
                            @if($wordStudyApplication && $wordStudyApplication->vacancy && $wordStudyApplication->workStudyApplicant && $wordStudyApplication->workStudyApplicant->applicant))
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $wordStudyApplication->workStudyApplicant->applicant->lastname .' '. $wordStudyApplication->workStudyApplicant->applicant->othernames }}</td>
                                <td>{{ $wordStudyApplication->workStudyApplicant->email }} </td>
                                <td>{{ $wordStudyApplication->workStudyApplicant->applicant->phone_number }} </td>
                                <td>{{ $wordStudyApplication->vacancy->title }} </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-2-fill"></i>
                                        </a>
                                    
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <li><a class="dropdown-item link-primary" href="{{ url('admin/studentProfile/'.$wordStudyApplication->workStudyApplicant->slug) }}"><i class="ri-folder-open-fill"></i> Student Profile</a></li>
                                            <li><a class="dropdown-item link-secondary" href="{{ url('admin/viewJobVacancy/'.$wordStudyApplication->Vacancy->slug) }}"><i class="ri-eye-fill"></i> Job Vacancy</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endif
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