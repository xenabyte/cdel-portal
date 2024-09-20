@extends('staff.layout.dashboard')
@php
    $staff = Auth::guard('staff')->user();
    $staffRoleGSTCordinator = false; 
    $deptCount = 1;  
    
    foreach ($staff->staffRoles as $staffRole) {
        if (strtolower($staffRole->role->role) == 'gst coordinator') {
            $staffRoleGSTCordinator = true;
        }
        
    }
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Departments</h4>

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
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Course Number(s)</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $department)
                            {{-- @if (($staffRoleGSTCordinator && $department->faculty_id == 0) || $department->faculty_id != 0) --}}
                                <tr>
                                    <th scope="row">{{ $deptCount++ }}</th>
                                    <td>{{ $department->name }}</td>
                                    <td>{{ $department->courses->count() }} </td>
                                    <td>
                                        <a href="{{ url('staff/departmentCourse/'.$department->slug) }}" class="btn btn-primary m-1"><i class= "mdi mdi-database-eye"></i> View department</a>
                                    </td>
                                </tr>
                            {{-- @endif --}}
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
