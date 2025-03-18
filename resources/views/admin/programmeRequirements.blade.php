@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Programme Requirements</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Programme Requirements</li>
                </ol>
            </div>

        </div>
    </div>

    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Programme Requirements</h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add">Add Programme Requirement</button>
                    </div>
                </div><!-- end card header -->

                @if(!empty($programmes))
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-6 col-xl-12">
                            
                            <div class="table-responsive">
                                <table id="fixed-header" class="table table-bordered table-responsive nowrap table-striped align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Programme Name</th>
                                            <th scope="col">Programme Probation CGPA</th>
                                            <th scope="col">Programme Requirements</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($programmes as $programme)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $programme->name }} - {{ $programme->programmeCategory->category }} Programme</td>
                                            <td>{{ $programme->minimum_cgpa }}</td>
                                            <td>
                                                @foreach($programme->programmeRequirement as $requirement)
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <p class="mb-1"><strong>Level:</strong> {{ $requirement->level_id * 100 }} Level</p>
                                                        <p class="mb-1"><strong>Minimum CGPA:</strong> {{ $requirement->min_cgpa }}</p>
                                                        <p class="mb-1"><strong>Additional:</strong> {{ $requirement->additional_criteria }}</p>
                                                    </div>
                                                    
                                                    <!-- Actions for Each Requirement -->
                                                    <div class="hstack gap-2">
                                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editRequirement{{$requirement->id}}" class="link-primary">
                                                            <i class="ri-edit-circle-fill"></i>
                                                        </a>
                                                        
                                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteRequirement{{$requirement->id}}" class="link-danger">
                                                            <i class="ri-delete-bin-5-line"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <hr>
                                                @endforeach
                                            </td>
                                            
                                            <td>
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addRequirement{{$programme->id}}" class="link-success">
                                                    <i class="ri-add-circle-line"></i>
                                                </a>

                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addProbationCGPA{{$programme->id}}" class="link-primary">
                                                    <i class="ri-edit-circle-fill"></i>
                                                </a>
                                            </td>
                                        </tr>


                                        <div id="addProbationCGPA{{$programme->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 overflow-hidden">
                                                    <div class="modal-header p-3">
                                                        <h4 class="card-title mb-0">Update Programme Probation CGPA</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                
                                                    <div class="modal-body">
                                                        <form action="{{ url('/admin/updateProgramme') }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name='programme_id' value="{{ $programme->id }}">
                                        
                                                            <div class="mb-3">
                                                                <label for="mininum_cgpa" class="form-label">Programme Probation CGPA</label>
                                                                <input type="text" class="form-control" name="mininum_cgpa" id="mininum_cgpa" value="{{$programme->mininum_cgpa}}">
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
                                
                                        <!-- Add Requirement Modal -->
                                        <div id="addRequirement{{$programme->id}}" class="modal fade" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 overflow-hidden">
                                                    <div class="modal-header p-3">
                                                        <h4 class="card-title mb-0">Add Programme Requirement</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ url('admin/addProgrammeRequirement') }}" method="post">
                                                            @csrf
                                                            <input name="programme_id" type="hidden" value="{{$programme->id}}">
                                                            <input name="programme_category_id" type="hidden" value="{{$programme->category_id}}">

                                                            <div class="mb-3">
                                                                <label for="level" class="form-label">Level</label>
                                                                <select class="form-select" name="level_id" id="level" data-choices data-choices-search-false required>
                                                                    <option value="" selected>Choose...</option>
                                                                    @foreach($levels as $academicLevel)<option value="{{ $academicLevel->id }}">{{ $academicLevel->level }}</option>@endforeach
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="min_cgpa" class="form-label">Minimum CGPA</label>
                                                                <input type="text" class="form-control" name="min_cgpa" required>
                                                            </div>
                                
                                                            <div class="mb-3">
                                                                <label for="additional_criteria" class="form-label">Additional Criteria</label>
                                                                <textarea class="form-control" name="additional_criteria"></textarea>
                                                            </div>
                                
                                                            <hr>
                                                            <div class="text-end">
                                                                <button type="submit" class="btn btn-success">Add Requirement</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                
                                        @foreach($programme->programmeRequirement as $requirement)
                                        <!-- Edit Requirement Modal -->
                                        <div id="editRequirement{{$requirement->id}}" class="modal fade" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 overflow-hidden">
                                                    <div class="modal-header p-3">
                                                        <h4 class="card-title mb-0">Edit Programme Requirement</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ url('admin/updateProgrammeRequirement') }}" method="post">
                                                            @csrf
                                                            <input name="requirement_id" type="hidden" value="{{$requirement->id}}">
                                                            <input name="programme_id" type="hidden" value="{{$requirement->programme_id}}">
                                                            <input name="programme_category_id" type="hidden" value="{{$requirement->programme->category_id}}">

                                                            <div class="mb-3">
                                                                <label for="level" class="form-label">Level</label>
                                                                <select class="form-select" name="level_id" id="level" data-choices data-choices-search-false required>
                                                                    <option value="" selected>Choose...</option>
                                                                    @foreach($levels as $academicLevel)<option value="{{ $academicLevel->id }}" @if($requirement->level_id == $academicLevel->id) selected @endif >{{ $academicLevel->level }}</option>@endforeach
                                                                </select>
                                                            </div>
                                
                                                            <div class="mb-3">
                                                                <label for="min_cgpa" class="form-label">Minimum CGPA</label>
                                                                <input type="text" class="form-control" name="min_cgpa" value="{{ $requirement->min_cgpa }}" required>
                                                            </div>
                                
                                                            <div class="mb-3">
                                                                <label for="additional_criteria" class="form-label">Additional Criteria</label>
                                                                <textarea class="form-control" name="additional_criteria">{{ $requirement->additional_criteria }}</textarea>
                                                            </div>
                                
                                                            <hr>
                                                            <div class="text-end">
                                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                
                                        <!-- Delete Requirement Modal -->
                                        <div id="deleteRequirement{{$requirement->id}}" class="modal fade" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-body text-center p-5">
                                                        <div class="text-end">
                                                            <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="mt-2">
                                                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                            </lord-icon>
                                                            <h4 class="mb-3 mt-4">Are you sure you want to delete this requirement?</h4>
                                                            <form action="{{ url('admin/deleteProgrammeRequirement') }}" method="POST">
                                                                @csrf
                                                                <input name="requirement_id" type="hidden" value="{{$requirement->id}}">
                                                                <hr>
                                                                <button type="submit" class="btn btn-danger w-100">Yes, Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
@endsection