@extends('staff.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">My Leave Application(s)</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">My Leave Application(s)</li>
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
                <h4 class="card-title mb-0 flex-grow-1">My Leaves</h4>
                <div class="flex-shrink-0">
                    <a href="{{ url('/staff/leaveApplication') }}" class="btn btn-primary">Apply For Leave</a>
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-stripped" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Leave Purpose</th>
                            <th scope="col">Destination</th>
                            <th scope="col">Start Date</th>
                            <th scope="col">Resumption Date</th>
                            <th scope="col">Total Days</th>
                            <th scope="col">Status</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaveApplications as $leave)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{  $leave->staff->title.' '.$leave->staff->lastname.' '. $leave->staff->othernames }}
                            <td>{!!  $leave->purpose !!}</td>
                            <td>{!!  $leave->destination_address !!}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('jS \o\f F, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('jS \o\f F, Y') }}</td>
                            <td>{{ $leave->days }} Days</td>
                            <td>
                                <button type="button" class="btn btn-{{ ($leave->status == null) ? 'warning' : ($leave->status == 1 ? 'success' : 'primary')}} btn-sm btn-rounded">
                                    {{ ($leave->status == null) ? 'Pending' : ($leave->status == 'approved' ? 'Approved' : 'Ended') }}
                                </button>
                            </td>
                            <td>
                               
                                @if(empty($leave->status))
                                <form method="post" action="{{ url('/staff/deleteLeave') }}">
                                    @csrf
                                    <input type="hidden" name="leaveId" value="{{ $leave->id }}">
                                    <a href="{{ url('/staff/leave/'.$leave->slug) }}" class="btn btn-primary waves-effect waves-light">
                                        <i class="mdi mdi-timer-settings"></i> View Leave Process
                                    </a>
                                    <button type="submit" class="btn btn-danger waves-effect waves-light">
                                      <i class="mdi mdi-trash-can"></i>
                                    </button>
                                </form>
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