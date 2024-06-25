@extends('admin.layout.dashboard')

@section('content')
<!-- start page title -->
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<script>
    // Select all textarea elements and initialize CKEditor on each
    document.querySelectorAll('ckeditor').forEach((textarea) => {
        CKEDITOR.replace(textarea);
    });
</script>
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
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaff"><i class="mdi mdi-account-multiple-plus"></i> Add Staff</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Attendance -  {{ empty($year)? date('M Y') : $month .' '. $year }}</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $singleStaff)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $singleStaff->title.' '.$singleStaff->lastname .' '. $singleStaff->othernames }}</td>
                            <td>{{ $staff->attendance->count() }} Days </td>
                            <td>
                                <a href="{{ url('admin/staff/'.$singleStaff->slug) }}" class="btn btn-primary"> <i class= "mdi mdi-monitor-eye"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editStaff{{$singleStaff->id}}" class="btn btn-info"><i class= "mdi mdi-application-edit"></i></a>
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

@endsection