@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Session Setup for {{ $programmeCategory->category }} Programme</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Session Setup</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Academic Sessions</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add">Add Session Year</button>
                </div>
            </div><!-- end card header -->

            @if(!empty($sessions) && $sessions->count() > 0)
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        
                        <table id="fixed-header" class="table table-borderedless table-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Academic Session</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $session)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $session->year }} </td>
                                    <td>
                                        <div class="hstack gap-3 fs-15">
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$session->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit{{$session->id}}" class="link-primary"><i class="ri-edit-circle-fill"></i></a>

                                            <div id="delete{{$session->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-body text-center p-5">
                                                            <div class="text-end">
                                                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="mt-2">
                                                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                                </lord-icon>
                                                                <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $session->year }}?</h4>
                                                                <form action="{{ url('/admin/deleteSession') }}" method="POST">
                                                                    @csrf
                                                                    <input name="session_id" type="hidden" value="{{$session->id}}">
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

                                            <div id="edit{{$session->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 overflow-hidden">
                                                        <div class="modal-header p-3">
                                                            <h4 class="card-title mb-0">Edit Session</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                
                                                        <div class="modal-body">
                                                            <form action="{{ url('/admin/updateSession') }}" method="post" enctype="multipart/form-data">
                                                                @csrf

                                                                <input name="level_id" type="hidden" value="{{$session->id}}">
                                
                                                                <div class="mb-3">
                                                                    <label for="year" class="form-label">Session</label>
                                                                    <input type="text" class="form-control" name="year"  minlength="9" maxlength="9" id="year" value="{{ $session->year }}">
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
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div><!-- end col -->
                </div>
            </div>
            @endif
        </div><!-- end card -->
    </div>
</div>
<!-- end row -->

<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Session Settings for {{ $programmeCategory->category }}</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        <form action="{{ url('/admin/setSession') }}" method="POST">
                            @csrf
                            <input type="hidden" name="sessionSetting_id" value="{{ $sessionSetting->id }}">
                            <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}">
                            <div class="row g-3">

                                <div class="col-lg-4">
                                    <h4 class="card-title mb-0 flex-grow-1">Active Academic Session: {{ !empty($programmeCategory->academicSessionSetting)?$programmeCategory->academicSessionSettingacademic_session:'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <select class="form-select" id="academic_session" name="academic_session" aria-label="academic session">
                                            <option value="" selected>--Select--</option>
                                            @foreach($sessions as $session)<option value="{{$session->year}}">{{ $session->year}}</option>@endforeach
                                        </select>
                                        <label for="academic_session">Academic Session</label>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <h4 class="card-title mb-0 flex-grow-1">Active Admission Session: {{ !empty($pageGlobalData->sessionSetting)?$programmeCategory->academicSessionSettingadmission_session:'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <select class="form-select" id="admission_session" name="admission_session" aria-label="admission session">
                                            <option value="" selected>--Select--</option>
                                            @foreach($sessions as $session)<option value="{{$session->year}}">{{ $session->year}}</option>@endforeach
                                        </select>
                                        <label for="admission_session">Admission Session</label>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <h4 class="card-title mb-0 flex-grow-1">Active Application Session: {{ !empty($pageGlobalData->sessionSetting)?$programmeCategory->academicSessionSettingapplication_session:'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <select class="form-select" id="application_session" name="application_session" aria-label="application session">
                                            <option value="" selected>--Select--</option>
                                            @foreach($sessions as $session)<option value="{{$session->year}}">{{ $session->year}}</option>@endforeach
                                        </select>
                                        <label for="application_session">Application Session</label>
                                    </div>
                                </div>
                                <hr>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" id="submit-button" class="btn btn-primary">Update Settings</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div><!-- end col -->
                </div>
            </div>
        </div><!-- end card -->
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Registrar Settings</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        <form action="{{ url('/admin/setRegistrarSetting') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="sessionSetting_id" value="{{ !empty($pageGlobalData->sessionSetting)?$pageGlobalData->sessionSetting->id:null }}">
                            <div class="row g-3">

                                <div class="col-lg-4">
                                    <h4 class="card-title mb-0 flex-grow-1">Registrar Name: {{ !empty($pageGlobalData->sessionSetting)?$pageGlobalData->sessionSetting->registrar_name:'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="registrar_name" name="registrar_name" placeholder="Enter your registrar name">
                                        <label for="registrar_name">Registrar Name</label>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <h4 class="card-title mb-0 flex-grow-1">Registrar Signature:
                                    <div class="form-floating">
                                        <input type="file" class="form-control" id="registrar_signature" name="registrar_signature">
                                        <label for="registrar_signature"></label>
                                    </div>
                                    <br>
                                    <img class="img-thumbnail" alt="Registrar Signature" width="200" src="{{ !empty($pageGlobalData->sessionSetting)? asset($pageGlobalData->sessionSetting->registrar_signature):'Not Set' }}"></h4>
                                </div>

                                <div class="col-lg-4">
                                    <h4 class="card-title mb-0 flex-grow-1">Resumption Date: {{ !empty($pageGlobalData->sessionSetting) ? date('l, jS F, Y', strtotime($pageGlobalData->sessionSetting->resumption_date)) :'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <input type="date" class="form-control" id="resumption_date" name="resumption_date">
                                        <label for="resumption_date">Resumption Date</label>
                                    </div>
                                </div>
                                <hr>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" id="submit-button" class="btn btn-primary">Update Settings</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div><!-- end col -->
                </div>
            </div>
        </div><!-- end card -->
    </div>


    <div class="col-lg-7">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Fees Settings</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        <form action="{{ url('/admin/setFeeStatus') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="sessionSetting_id" value="{{ !empty($pageGlobalData->sessionSetting)?$pageGlobalData->sessionSetting->id:null }}">
                            <div class="row g-3">

                                <div class="col-lg-6">
                                    <h4 class="card-title mb-0 flex-grow-1">School Fee Payment Status: {{ !empty($pageGlobalData->sessionSetting)?$pageGlobalData->sessionSetting->school_fee_status:'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <select class="form-select" id="school_fee_status" name="school_fee_status" aria-label="school_fee_status">
                                            <option value="" selected>--Select--</option>
                                            <option value="start">Start</option>
                                            <option value="stop">Stop</option>
                                        </select>
                                        <label for="school_fee_status">School Fee Payment Status</label>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <h4 class="card-title mb-0 flex-grow-1">Accomondation Booking Status: {{ !empty($pageGlobalData->sessionSetting)?$pageGlobalData->sessionSetting->accomondation_booking_status:'Not Set' }} </h4>
                                        <br>
                                        <div class="form-floating">
                                            <select class="form-select" id="accomondation_booking_status" name="accomondation_booking_status" aria-label="accomondation_booking_status">
                                                <option value="" selected>--Select--</option>
                                                <option value="start">Start</option>
                                                <option value="stop">Stop</option>
                                            </select>
                                            <label for="accomondation_booking_status">Accomondation Booking Status</label>
                                        </div>
                                </div>

                                <hr>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" id="submit-button" class="btn btn-primary">Update Settings</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div><!-- end col -->
                </div>
            </div>
        </div><!-- end card -->
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Campus Wide Message</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        <form action="{{ url('/admin/setCampusWideMessage') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="sessionSetting_id" value="{{ !empty($pageGlobalData->sessionSetting)?$pageGlobalData->sessionSetting->id:null }}">
                            <div class="row g-3">

                                <div class="col-lg-12">
                                    <br>
                                    <div class="col-lg-12">
                                        <label for="campus_wide_message">Campus Wide Message</label>
                                        <textarea class="ckeditor" id="campus_wide_message" name="campus_wide_message" >{!! !empty($pageGlobalData->sessionSetting)?$pageGlobalData->sessionSetting->campus_wide_message:null !!}</textarea>
                                    </div><!--end col-->
                                </div>
                                <hr>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" id="submit-button" class="btn btn-primary">Update Message</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div><!-- end col -->
                </div>
            </div>
        </div><!-- end card -->
    </div>
</div>
<!-- end row -->

<div id="add" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Session</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/addSession') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="year" class="form-label">Session</label>
                        <input type="text" class="form-control" minlength="9" maxlength="9" name="year" id="year">
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Create a Session</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection