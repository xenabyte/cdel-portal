@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Job Vacancy</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Job Vacancy</li>
                </ol>
            </div>

        </div>
    </div>

    <div class="row">
        <style type="text/css">
        .wrap-text {
            white-space: normal;
            word-wrap: break-word;
            max-width: 100px; /* Adjust as needed */
        }
        </style>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Job Vacancy</h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add">Post Vacancy</button>
                    </div>
                </div><!-- end card header -->

                @if(!empty($jobVacancies) && $jobVacancies->count() > 0)
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-6 col-xl-12">
                            
                            <table id="fixed-header" class="table table-borderedless dt-responsive nowrap table-striped align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Id</th>
                                        <th scope="col">Flyer</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Application Type</th>
                                        <th scope="col">Job Level</th>
                                        <th scope="col">Status</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jobVacancies as $jobVacancy)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td><img class="img-thumbnail" alt="200x200" width="200" src="{{ asset($jobVacancy->image) }}"></td>
                                        <td>{{ $jobVacancy->title }} </>
                                        <td>{{ $jobVacancy->type }} </td>
                                        <td>{{ $jobVacancy->jobLevel?$jobVacancy->jobLevel->name .' @ ₦'. number_format($jobVacancy->jobLevel->hourly_rate/100, 2) .'/hour':null }}</td>
                                        <td>{{ ucwords($jobVacancy->status) }} </td>
                                        <td>
                                            <div class="hstack gap-3 fs-15">
                                                <a href="{{ url('admin/viewJobVacancy/'.$jobVacancy->slug) }}" class="link-secondary m-1"><i class= "ri-eye-fill"></i></a>
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit{{$jobVacancy->id}}" class="link-primary"><i class="ri-edit-circle-fill"></i></a>
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$jobVacancy->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div><!-- end col -->
                    </div>
                </div>
                @endif
            </div><!-- end card -->
        </div>
    </div>
    <!-- end row -->
</div>
<!-- end page title -->

@foreach($jobVacancies as $jobVacancy)

<div id="delete{{$jobVacancy->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $jobVacancy->title }}?</h4>
                    <form action="{{ url('/admin/deleteJobVacancy') }}" method="POST">
                        @csrf
                        <input name="job_id" type="hidden" value="{{$jobVacancy->id}}">
                        <hr>
                        <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Delete</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div id="edit{{$jobVacancy->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Edit Job Vacancy</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/updateJobVacancy') }}" method="post" enctype="multipart/form-data">
                    @csrf

                        <input name="job_id" type="hidden" value="{{$jobVacancy->id}}">

                        <div class="mb-3">
                            <label for="role" class="form-label">Vacancy Type</label>
                            <select class="form-select" aria-label="role" name="type" required>
                                <option @if($jobVacancy->type == 'Job Vacancy') selected  @endif value="Job Vacancy">Job Vacancy</option>
                                <option @if($jobVacancy->type == 'Work Study') selected @endif value="Work Study">Work Study</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="flyer" class="form-label">Application Flyer</label>
                            <input type="file" class="form-control" name="flyer" id="flyer">
                        </div>
                    
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" value="{{ $jobVacancy->title }}" id="title">
                        </div>
                    
                        <div class="mb-3">
                            <label for="description">Description</label>
                            <textarea class="ckeditor" id="description" name="description">{!! $jobVacancy->description !!}</textarea>
                        </div>
                    
                        <div class="mb-3">
                            <label for="requirements">Requirements</label>
                            <textarea class="ckeditor" id="requirements" name="requirements">{!! $jobVacancy->requirements !!}</textarea>
                        </div>
                    
                        <div class="mb-3">
                            <label for="applicationDeadline" class="form-label">Application Deadline</label>
                            <input type="date" class="form-control" name="application_deadline" value="{{ $jobVacancy->application_deadline }}" id="applicationDeadline">
                        </div>
                    
                        @if($jobVacancy->type == 'Work Study')
                        <!-- CGPA Field Wrapper -->
                        <div class="mb-3">
                            <label for="cgpa" class="form-label">Minimum Student CGPA</label>
                            <input type="text" class="form-control" name="cgpa" value="{{ $jobVacancy->cgpa }}" id="cgpa">
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Job Level</label>
                            <select class="form-select" aria-label="role" name="level_id" id="level_id">
                                @foreach($jobLevels as $jobLevel)<option @if($jobLevel->id == $jobVacancy->level_id) selected @endif value="{{ $jobLevel->id }}">{{ $jobLevel->name .' @ ₦'. number_format($jobLevel->hourly_rate/100, 2) .'/hour' }}</option>@endforeach
                            </select>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="role" class="form-label">Status</label>
                            <select class="form-select" aria-label="role" name="status">
                                <option selected value="">Select Option </option>
                                <option value="active">Active</option>
                                <option value="closed">Closed</option>
                                <option value="reset">Reset (This will remove all applicant)</option>
                            </select>
                        </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endforeach

<div id="add" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Post Job Vacancy</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/postJobVacancy') }}" method="post" enctype="multipart/form-data">
                    @csrf
                
                    <div class="mb-3">
                        <label for="role" class="form-label">Vacancy Type</label>
                        <select class="form-select" aria-label="role" name="type" id="vacancy-type" required>
                            <option selected value="">Select Option </option>
                            <option value="Job Vacancy">Job Vacancy</option>
                            <option value="Work Study">Work Study</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="flyer" class="form-label">Application Flyer</label>
                        <input type="file" class="form-control" name="flyer" id="flyer">
                    </div>
                
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="title">
                    </div>
                
                    <div class="mb-3">
                        <label for="description">Description</label>
                        <textarea class="ckeditor" id="description" name="description"></textarea>
                    </div>
                
                    <div class="mb-3">
                        <label for="requirements">Requirements</label>
                        <textarea class="ckeditor" id="requirements" name="requirements"></textarea>
                    </div>
                
                    <div class="mb-3">
                        <label for="applicationDeadline" class="form-label">Application Deadline</label>
                        <input type="date" class="form-control" name="application_deadline" id="applicationDeadline">
                    </div>
                
                    <!-- CGPA Field Wrapper -->
                    <div class="mb-3" id="cgpa-field" style="display: none;">
                        <label for="cgpa" class="form-label">Minimum Student CGPA</label>
                        <input type="text" class="form-control" name="cgpa" id="cgpa">
                    </div>

                    <div class="mb-3" id="job-level" style="display: none;">
                        <label for="role" class="form-label">Job Level</label>
                        <select class="form-select" aria-label="role" name="level_id">
                            <option selected value="">Select Option </option>
                            @foreach($jobLevels as $jobLevel)<option value="{{ $jobLevel->id }}">{{ $jobLevel->name .' @ ₦'. number_format($jobLevel->hourly_rate/100, 2) .'/hour' }}</option>@endforeach
                        </select>
                    </div>
                
                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Post Job Vacancy</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    document.getElementById('vacancy-type').addEventListener('change', function() {
        var cgpaField = document.getElementById('cgpa-field');
        var jobLevel = document.getElementById('job-level');

        if (this.value === 'Work Study') {
            cgpaField.style.display = 'block';
            jobLevel.style.display = 'block';
        } else {
            cgpaField.style.display = 'none';
            jobLevel.style.display = 'none';

        }
    });
</script>
@endsection