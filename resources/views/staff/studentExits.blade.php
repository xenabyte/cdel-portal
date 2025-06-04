@extends('staff.layout.dashboard')
@php
    $staff = Auth::guard('staff')->user();
    $isHod = \App\Models\Department::where('hod_id', $staff->id)->exists();
    $role = $isHod ? 'HOD' : 'student care';
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Exit Application(s)</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Exit Application(s)</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Validate Application</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#getApplication">Get Application</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            @if(!empty($exitApplications) && $exitApplications->count() > 0)
            <div class="card-body">
                <h6 class="mb-3 fw-semibold text-uppercase">Pending Student Exit Application(s)</h6>
                <form action="{{ url('/staff/bulkManageExitApplications') }}" method="POST">
                    @csrf
                    
                    <input type="hidden" name="role" value="{{ $role }}">
                    <div class="table-responsive">
                        <table id="fixed-header" class="table table-bordered table-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col"><input type="checkbox" id="select-all"></th>
                                    <th scope="col">Application ID</th>
                                    <th scope="col">Student Name</th>
                                    <th scope="col">Purpose</th>
                                    <th scope="col">Destination</th>
                                    <th scope="col">Outing Date</th>
                                    <th scope="col">Returning Date</th>
                                    <th scope="col">Application Date
                                    {{-- <th scope="col">File</th> --}}
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exitApplications as $exitApplication)
                                    <tr>
                                        <td><input type="checkbox" name="exit_ids[]" value="{{ $exitApplication->id }}"></td>
                                        <td>#{{ sprintf("%06d", $exitApplication->id) }}</td>
                                        <td>{{ $exitApplication->student->applicant->lastname ?? 'N/A' }} {{ $exitApplication->student->applicant->othernames ?? '' }}</td>
                                        <td>{{ $exitApplication->purpose }}</td>
                                        <td>{{ $exitApplication->destination }}</td>
                                        <td>{{ empty($exitApplication->exit_date) ? null : date('F j, Y', strtotime($exitApplication->exit_date)) }}</td>
                                        <td>{{ empty($exitApplication->return_date) ? null : date('F j, Y \a\t g:i A', strtotime($exitApplication->return_date)) }}</td>
                                        <td>{{ empty($exitApplication->created_at) ? null : date('F j, Y \a\t g:i A', strtotime($exitApplication->created_at)) }}</td>
                                        {{-- <td><a href="{{ asset($exitApplication->file) }}" class="btn btn-outline-primary" target="_blank">View Document</a></td> --}}
                                        <td>{{ ucwords($exitApplication->status) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" name="action" value="approved" class="btn btn-success me-2">Approve Selected</button>
                        <button type="submit" name="action" value="declined" class="btn btn-danger">Decline Selected</button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>


@foreach($exitApplications as $exitApplication)
<div id="decline{{$exitApplication->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to decline <br/> {{ isset($exitApplication->student->applicant)?$exitApplication->student->applicant->lastname .' ' . $exitApplication->student->applicant->othernames:null}} exit application?</h4>
                    <form action="{{ url('/staff/manageExitApplication') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="{{ $role }}">
                        <input name="exit_id" type="hidden" value="{{$exitApplication->id}}">
                        <input name="action" type="hidden" value="declined">
                        <hr>
                        <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Decline</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="approve{{$exitApplication->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to approve <br/> {{ isset($exitApplication->student->applicant)? $exitApplication->student->applicant->lastname .' ' . $exitApplication->student->applicant->othernames: null }} exit application?</h4>
                    <form action="{{ url('/staff/manageExitApplication') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="{{ $role }}">
                        <input name="exit_id" type="hidden" value="{{$exitApplication->id}}">
                        <input name="action" type="hidden" value="approved">
                        <hr>
                        <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Approve</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endforeach

<div id="getApplication" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Get Application</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/staff/getExitApplication') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="reg" class="form-label">Exit Application Number</label>
                        <input type="text" class="form-control" name="exit_id" id="reg">
                    </div>
                    <div class="text-end border-top border-top-dashed p-3">
                        <br>
                        <button type="submit" id="submit-button" class="btn btn-primary">Get Application</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    document.getElementById("select-all").addEventListener("change", function() {
        let checkboxes = document.querySelectorAll("input[name='exit_ids[]']");
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });
</script>
@endsection