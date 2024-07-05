@extends('staff.layout.dashboard')

@section('content')
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
                <h4 class="card-title mb-0 flex-grow-1">{{ $staff->title.'. '.$staff->lastname .' '. $staff->othernames }} ({{ date('M Y') }}) Attendance Records </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Date</th>
                            <th scope="col">Clock In Time</th>
                            <th scope="col">Clock Out Time</th>
                            <th scope="col">Leave</th>
                            <th scope="col">Status</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthAttendance as $attendance)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <th scope="row">{{  \Carbon\Carbon::parse($attendance->date)->format('jS \o\f F, Y') }}</th>
                            <td>{{ !empty($attendance->clock_in)? \Carbon\Carbon::parse($attendance->clock_in)->format('h:i A'):null }}</td>
                            <td>{{ !empty($attendance->clock_out)?  \Carbon\Carbon::parse($attendance->clock_out)->format('h:i A'): null }}</td>
                            <td>{{ $attendance->leave? $attendance->leave->purpose : null }}</td>
                            <td>
                                @if($attendance->status == 2)
                                <button type="button" class="btn btn-success btn-sm btn-rounded">
                                    Present
                                </button>
                                @elseif($attendance->status == 1)
                                <button type="button" class="btn btn-warning btn-sm btn-rounded">
                                    Awaiting ClockIn/ClockOut
                                </button>
                                @else
                                <button type="button" class="btn btn-danger btn-sm btn-rounded">
                                  Absent
                                </button>
                                @endif
                            </td>
                            <td>
                                @if($attendance->status != 2)
                                <a href="{{ url('/staff/updateAttendance/'.$attendance->id) }}" class="btn btn-primary"> <i class= "mdi mdi-calendar-check"></i></a>
                                @endif
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