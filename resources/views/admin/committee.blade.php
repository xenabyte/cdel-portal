@extends('admin.layout.dashboard')

@section('content')
@php
    use Carbon\Carbon;
@endphp
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Committee</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Committee</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row project-wrapper">
    <div class="col-xxl-8">
        <div class="row">
            <div class="col-xl-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary rounded-2 fs-2">
                                    <i data-feather="briefcase"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden ms-3">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-3">Members</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{$committee->members? $committee->members->count():0 }}">0</span></h4>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div><!-- end col -->

            <div class="col-xl-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning rounded-2 fs-2">
                                    <i data-feather="award"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-3">Meetings</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $committee->meetings?$committee->meetings->count():0 }}">0</span></h4>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div><!-- end col -->
        </div><!-- end row -->

        <div class="row">
            <div class="col-xl-7">
                <div class="card">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Committee Overview - {{ $committee->name }}</h4>
                    </div><!-- end card header -->

                    <div class="card-header p-0 border-0 bg-soft-light">
                        <div class="row g-0 text-center">
                            <div class="col-6 col-sm-12">
                                <div class="p-3 border border-dashed border-start-0">
                                </div>
                            </div>
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <div class="align-items-center d-flex border-top border-top-dashed mt-3 pt-3">
                            <p class="mb-0 flex-grow-1">Edit Committee</p>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editCommittee" class="btn btn-primary">Edit Committee</a>
                            </div>
                        </div>

                        <div class="align-items-center d-flex border-top border-top-dashed mt-3 pt-3">
                            <p class="mb-0 flex-grow-1">Kindly Appoint Secretary </p>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#assignSec" class="btn btn-success">Assign Secretary</a>
                            </div>
                        </div>
                       
                        <div class="align-items-center d-flex border-top border-top-dashed mt-3 mb-3 pt-3">
                            <p class="mb-0 flex-grow-1">Kindly Appoint Chairman</p>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#assignChairman" class="btn btn-primary">Assign Chairman</a>
                            </div>
                        </div>

                        <br>

                        <hr>

                        <h4 class="mb-3">Add Members</h4>
                        
                        <form action="{{ url('/admin/addMember') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name='committee_id' value="{{ $committee->id }}">
        
                            <div class="mb-3">
                                <label for="staff_id" class="form-label">Select Staff</label>
                                <select class="form-select select2 selectWithSearch" id="selectWithSearch" name="staff_id" aria-label="cstatus" required>
                                    <option value="" selected>--Select--</option>
                                    @foreach($staffs as $staff)<option value="{{$staff->id}}">{{ $staff->title.' '. $staff->lastname.' '.$staff->othernames }}</option>@endforeach
                                </select>
                            </div>
        
                            <div class="text-end border-top border-top-dashed p-3 p-3">
                                <button type="submit" id="submit-button" class="btn btn-primary">Add Members</button>
                            </div>
                        </form>

                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
            <div class="col-xxl-5">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="card-title mb-0">Secretary's Profile</h4>
                    </div><!-- end cardheader -->
                    @if(!empty($committee->secretary))
                    <div class="card-body pt-0">
                        <img class="card-img-top img-fluid" src="{{ $committee->secretary->image }}" alt="Card image cap">
                        <div class="card-body">
                            <p class="card-text text-center"><strong>{{ $committee->secretary->title.' '. $committee->secretary->lastname.' '.$committee->secretary->othernames }}</strong> <br> Secretary, {{ $committee->name }}</p>
                        </div>
        
        
                        <div class="card-header p-0 border-0 bg-soft-light">
                            <div class="row g-0 text-center">
                                <div class="col-12 col-sm-12">
                                    <div class="p-3 border border-dashed border-start-0">
        
                                    </div>
                                </div>
                            </div>
                        </div><!-- end card header -->
                    </div><!-- end cardbody -->
                    @endif
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
    </div><!-- end col -->
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-header border-0">
                <h4 class="card-title mb-0">Chairman's Profile</h4>
            </div><!-- end cardheader -->
            @if(!empty($committee->chairman))
            <div class="card-body pt-0">
                <img class="card-img-top img-fluid" src="{{ $committee->chairman->image }}" alt="Card image cap">
                <div class="card-body">
                    <p class="card-text text-center"><strong>{{$committee->chairman->title.' '.$committee->chairman->lastname.' '.$committee->chairman->othernames }}</strong> <br> Chairman, {{ $committee->name }}</p>
                </div>


                <div class="card-header p-0 border-0 bg-soft-light">
                    <div class="row g-0 text-center">
                        <div class="col-12 col-sm-12">
                            <div class="p-3 border border-dashed border-start-0">

                            </div>
                        </div>
                    </div>
                </div><!-- end card header -->
            </div><!-- end cardbody -->
            @endif
        </div><!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->

<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Committee Members</h4>
            </div><!-- end card header -->

            <div class="card-body">

                <div class="table-responsive p-3">
                    <table id="buttons-datatables1" class="table table-borderless table-nowrap align-middle mb-3">
                        <thead class="table-light text-muted">
                            <tr>
                                <th scope="col">Members</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($committee->members as $member)
                            <tr>
                                <td class="d-flex">
                                    @if($member->staff)
                                        <img src="{{ $member->staff->image }}" alt="" class="avatar-xs rounded-3 shadow me-2">
                                        <div>
                                            <h5 class="fs-13 mb-0">
                                                {{ $member->staff->title.' '.$member->staff->lastname.' '.$member->staff->othernames }}
                                            </h5>
                                            <p class="fs-12 mb-0 text-muted">{{ $member->staff->qualification }}</p>
                                        </div>
                                    @else
                                        <span class="text-muted">No staff information available</span>
                                    @endif
                                </td>

                                <td style="width:5%;">
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteMember{{$member->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>
                                </td>
                            </tr><!-- end tr -->

                            <div id="deleteMember{{$member->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body text-center p-5">
                                            <div class="text-end">
                                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="mt-2">
                                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                </lord-icon>
                                                <h4 class="mb-3 mt-4">Are you sure you want to delete?</h4>
                                                <form action="{{ url('/admin/deleteMember') }}" method="POST">
                                                    @csrf
                                                    <input name="member_id" type="hidden" value="{{$member->id}}">
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
                            @endforeach
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>
            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-lg-9">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Meetings</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add">Schedule a Meeting</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="table-responsive p-3">
                    <table id="buttons-datatables2" class="table table-borderless table-nowrap align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th scope="col">S/N</th>
                                <th scope="col">Title</th>
                                <th scope="col">Venue</th>
                                <th scope="col">Date</th>
                                <th scope="col">Time</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($committee->meetings as $meeting)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="">{{ $meeting->title }}</td>
                                <td class="responsive" style="width:50%;">{{ $meeting->venue }} </td>
                                <td class="">{{ \Carbon\Carbon::parse($meeting->date)->toFormattedDateString() }}</td>
                                <td class="">{{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</td>

                                <td style="width:5%;">
                                    <div class="hstack gap-3 fs-15">
                                        @if(!empty($meeting->agenda))<a target="blank" href="{{ asset($meeting->agenda) }}" class="btn btn-sm btn-primary">View Agenda</a>@endif
                                        @if(!empty($meeting->minute))<a target="blank" href="{{ asset($meeting->minute) }}" class="btn btn-sm btn-primary">View Minute</a>@endif
                                        @if(!empty($meeting->excerpt))<a target="blank" href="{{ asset($meeting->excerpt) }}" class="btn btn-sm btn-primary">View Excerpt</a>@endif
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editMeeting{{$meeting->id}}" class="link-primary"><i class="ri-edit-circle-fill"></i></a>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteMeeting{{$meeting->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>
                                    </div>
                                </td>
                            </tr><!-- end tr -->

                            <div id="editMeeting{{ $meeting->id }}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 overflow-hidden">
                                        <div class="modal-header p-3">
                                            <h4 class="card-title mb-0">Update Meeting</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                            
                                        <div class="modal-body">
                                            <form action="{{ url('/admin/updateMeeting') }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="committee_id" value="{{ $committee->id }}">
                                                <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">

                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Meeting Title</label>
                                                    <input type="text" class="form-control" name="title" id="title" value="{{ $meeting->title }}" required>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="date" class="form-label">Date</label>
                                                    <input type="date" class="form-control" name="date" id="date" value="{{ $meeting->date }}" required>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="time" class="form-label">Time</label>
                                                    <input type="time" class="form-control" name="time" id="time" value="{{ $meeting->time }}" required>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="venue" class="form-label">Venue</label>
                                                    <textarea class="form-control ckeditor" name="venue" id="venue" required>{!! $meeting->venue !!}</textarea>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="agenda" class="form-label">Agenda (Optional)</label>
                                                    <input type="file" class="form-control" name="agenda" id="agenda">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="minute" class="form-label">Minute (Optional)</label>
                                                    <input type="file" class="form-control" name="minute" id="minute">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="excerpt" class="form-label">Excerpt (Optional)</label>
                                                    <input type="file" class="form-control" name="excerpt" id="excerpt">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Status</label>
                                                    <select class="form-select" aria-label="status" name="status">
                                                        <option value="pending">Pending</option>
                                                        <option value="confirmed">Confirmed</option>
                                                        <option value="cancelled">Cancelled</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="text-end">
                                                    <button type="submit" id="submit-button" class="btn btn-primary">Update Meeting</button>
                                                </div>
                                            </form>                
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->

                            <div id="deleteMeeting{{$meeting->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body text-center p-5">
                                            <div class="text-end">
                                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="mt-2">
                                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                </lord-icon>
                                                <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $meeting->title }}?</h4>
                                                <form action="{{ url('/admin/deleteMeeting') }}" method="POST">
                                                    @csrf
                                                    <input name="meeting_id" type="hidden" value="{{$meeting->id}}">
                                                    <input name="committee_id" type="hidden" value="{{$committee->id}}">
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
                            @endforeach
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>

            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->


<div id="editCommittee" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Edit Committee</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/updateCommittee') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="committee_id" value="{{ $committee->id }}">
                    <div class="mb-3">
                        <label for="name" class="form-label">Committee Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ $committee->name }}" disabled readonly>
                    </div>

                    <div class="mb-3">
                        <label for="duties" class="form-label">Committee Duties</label>
                        <textarea class="form-control ckeditor" name="duties" id="duties" placeholder="Enter Committee Duties"></textarea>
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

<div id="assignChairman" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Assign Chairman</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">

                <form action="{{ url('/admin/assignCommitteePosition') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name='committee_id' value="{{ $committee->id }}">
                    <input type="hidden" name='role' value="chairman">


                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Select Staff</label>
                        <select class="form-select" aria-label="staff_id" name="staff_id">
                            <option selected value= "">Select Staff </option>
                            @foreach($committee->members as $member)
                            @if(!empty($member->staff))<option value="{{ $member->staff->id }}">{{ $member->staff->lastname.' '.$member->staff->othernames }}</option>@endif
                            @endforeach
                        </select>
                    </div>

                    <div class="text-end border-top border-top-dashed p-3 p-3">
                        <button type="submit" id="submit-button" class="btn btn-primary">Assign Chairman</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="assignSec" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Assign Chairman</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">

                <form action="{{ url('/admin/assignCommitteePosition') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name='committee_id' value="{{ $committee->id }}">
                    <input type="hidden" name='role' value="secretary">

                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Select Staff</label>
                        <select class="form-select" aria-label="staff_id" name="staff_id">
                            <option selected value= "">Select Staff </option>
                            @foreach($committee->members as $member)
                            <option value="{{ $member->staff->id }}">{{ $member->staff->lastname.' '.$member->staff->othernames }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-end border-top border-top-dashed p-3 p-3">
                        <button type="submit" id="submit-button" class="btn btn-primary">Assign Secretary</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div id="add" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Create Meeting</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/createMeeting') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="committee_id" value="{{ $committee->id }}">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Meeting Title</label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Enter Meeting Title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" id="date" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="time" class="form-label">Time</label>
                        <input type="time" class="form-control" name="time" id="time" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="venue" class="form-label">Venue</label>
                        <textarea class="form-control ckeditor" name="venue" id="venue" placeholder="Enter Venue" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="agenda" class="form-label">Agenda (Optional)</label>
                        <input type="file" class="form-control" name="agenda" id="agenda">
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" aria-label="status" name="status">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Create Meeting</button>
                    </div>
                </form>                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
