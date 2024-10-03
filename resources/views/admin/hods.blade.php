@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Head of Departments</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Departments</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Departments </h4>
                <div class="flex-shrink-0">
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Hod</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $department)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $department->name }}</td>
                            <td>{{ !empty($department->hod)?$department->hod->title.' '.$department->hod->lastname. ' '.$department->hod->othernames : null }}</td>
                            <td>
                                {{-- <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#assignDean{{$department->id}}" class="btn btn-info">Assign Dean</a> --}}
                                <form action="{{ url('/admin/assignHodToDepartment') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name='department_id' value="{{ $department->id }}">
                                    
                                    <div class="input-group" style="display: flex; flex-wrap: nowrap;">
                                        <select class="form-select select2 selectWithSearch" aria-label="staff" name="staff_id" required>
                                            <option value= "" selected>Select Staff</option>
                                            @foreach($department->staffs as $staffMember)<option value="{{ $staffMember->id }}">{{ $staffMember->title.' '.$staffMember->lastname.' '.$staffMember->othernames }}</option>@endforeach
                                        </select>
                                        <button type="submit" class="btn btn-outline-secondary shadow-none" type="button">Assign</button>
                                    </div>
                                </form>
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

{{-- <!-- Modal for Assign Dean -->
@foreach($departments as $department)
    <div id="assignDean{{$department->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 overflow-hidden">
                <div class="modal-header p-3">
                    <h4 class="card-title mb-0">Assign Department Dean</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ url('/admin/assignDeanToDepartment') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name='department_id' value="{{ $department->id }}">
                        
                        <div class="input-group">
                            <select class="form-select select2 selectWithSearch" aria-label="staff" name="staff_id" required>
                                <option value= "" selected>Select Staff</option>
                                @foreach($department->staffs as $staffMember)<option value="{{ $staffMember->id }}">{{ $staffMember->title.' '.$staffMember->lastname.' '.$staffMember->othernames }}</option>@endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-secondary shadow-none" type="button">Assign</button>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endforeach --}}

@endsection
