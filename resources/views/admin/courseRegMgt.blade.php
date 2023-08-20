@extends('admin.layout.dashboard')

@section('content')

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Course Registration Setting</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                            <li class="breadcrumb-item active">Course Registration Settings</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-10 offset-md-1">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Course Registration Settings for {{ $pageGlobalData->sessionSetting->academic_session }} academic session</h4>
                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editSiteInfo">{{ empty($courseRegMgt) ? 'Add Course Registration Settings' : 'Update Course Registration Settings' }}</button>
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#resetCourses">Reset Courses</button>
                        </div>
                    </div><!-- end card header -->
                    @if(!empty($courseRegMgt))
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-6 col-xl-12">
                               <h4 class="text-dark"> Course Registration <span class="{{ ($courseRegMgt->status == 'stop') ? 'text-danger' : 'text-success' }}"><strong>{{ ($courseRegMgt->status == 'stop') ? 'is not activated' : 'activated' }}</strong></span> for  <strong>{{ $pageGlobalData->sessionSetting->academic_session }} </strong> academic session </h4>
                            </div><!-- end col -->
                        </div>
                    </div>
                    @endif
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->

        <div id="resetCourses" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-5">
                        <div class="text-end">
                            <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="mt-2">
                            <lord-icon src="https://cdn.lordicon.com/sxobuwft.json" trigger="hover" style="width:150px;height:150px">
                            </lord-icon>
                            <h4 class="mb-3 mt-4">Are you sure you want to reset courses?</h4>
                            <form action="#" method="POST">
                                @csrf
                                <hr>
                                <button type="submit" class="btn btn-danger w-100">Yes, Delete</button>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3 justify-content-center">

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <div id="editSiteInfo" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 overflow-hidden">
                    <div class="modal-header p-3">
                        <h4 class="card-title mb-0">Course Registration Setting for {{ $pageGlobalData->sessionSetting->academic_session }}  academic session </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <hr class="text-primary">
                    <div class="modal-body">
                        <form action="{{ url('/admin/setCourseRegStatus') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name='courseRegMgt_id' value="{{ !empty($courseRegMgt) ? $courseRegMgt->id : '' }}">
                            <input type="hidden" name='academic_session' value="{{ $pageGlobalData->sessionSetting->academic_session }}">
        
                            <div class="mb-3">
                                <label for="choices-publish-status-input" class="form-label">Manage Course Registration</label>
                                <select class="form-select" name="status" id="choices-publish-status-input" data-choices data-choices-search-false required>
                                    <option value="" selected>Choose...</option>
                                    <option value="start">Start Course Registration</option>
                                    <option value="stop">Stop Course Registration</option>
                                </select>
                            </div>
        
                            <br>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    </div>
</div>

@endsection