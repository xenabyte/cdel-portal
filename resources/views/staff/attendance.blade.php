@extends('staff.layout.dashboard')

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
    <div class="col-xl-6 col-md-6">
        <div class="card card-animate bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-white mb-0">Total Staff</p>
                        <hr class="text-white">
                        <h2 class="mt-4 ff-secondary text-white fw-semibold"><span class="counter-value" data-target="{{ count($staff) }}">{{ count($staff) }}</span> Staff</h2>
                    </div>
                </div>
            </div><!-- end card body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-xl-6 col-md-6">
        <div class="card card-animate bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-white-50 mb-0">Captured Working Days - Today's date is {{date('D, d M, Y') }}</p>
                        <hr class="text-white">
                        <h2 class="mt-4 ff-secondary fw-semibold text-white"><span class="counter-value" data-target="{{$capturedWorkingDays }}">{{$capturedWorkingDays }}</span> Days</h2>
                    </div>
                </div>
            </div><!-- end card body -->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Upload Daily Attendance</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="live-preview">

                    <div class="mt-4">
                        <h5 class="fs-15 mb-3">Upload Attendance</h5>
                        <div class="row g-3 mb-4">
                            <form action="{{ url('/staff/uploadAttendance') }}"  method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="col-lg-12">
                                    <div class="input-group">
                                        <input type="file" class="form-control" name="file" aria-label="Upload" required>
                                        <button class="btn btn-outline-primary shadow-none" type="submit" id="">Upload Attendance</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>

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
                            <th scope="col">Staff Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Attendance -  {{ empty($year)? date('M Y') : $month .' '. $year }}</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $singleStaff)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <th scope="row">{{ $singleStaff->staffId }}</th>
                            <td>{{ $singleStaff->title.' '.$singleStaff->lastname .' '. $singleStaff->othernames }}</td>
                            <td>{{ $singleStaff->attendance->count() }} / {{$capturedWorkingDays }} Days </td>
                            <td>
                                <a href="{{ url('/staff/monthlyAttendance/'.$singleStaff->slug) }}" class="btn btn-primary"> <i class= "mdi mdi-monitor-eye"></i></a>
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