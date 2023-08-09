@extends('admin.layout.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Staff</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Staff-</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Staff </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Image</th>
                            <th scope="col">Name</th>
                            <th scope="col">Staff ID</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Category</th>
                            <th scope="col">Faculty</th>
                            <th scope="col">Department</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $singleStaff)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>
                                <img class="img-thumbnail rounded-circle avatar-md"  src="{{ !empty($singleStaff->image) ? $singleStaff->image : asset('assets/images/users/user-dummy-img.jpg') }}">
                            </td>
                            <td>{{ $singleStaff->title.' '.$singleStaff->lastname .' '. $singleStaff->othernames }}</td>
                            <td>{{ $singleStaff->staffId }}</td>
                            <td>{{ $singleStaff->email }} </td>
                            <td>{{ $singleStaff->phone_number }} </td>
                            <td>{{ $singleStaff->category }} </td>
                            <td>{{ !empty($singleStaff->faculty)?$singleStaff->faculty->name:null }} </td>
                            <td>{{ !empty($singleStaff->acad_department)?$singleStaff->acad_department->name:null }} </td>
                            <td>
                                <a href="{{ url('admin/staff/'.$singleStaff->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Staff</a>
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