@extends('admin.layout.dashboard')

@section('content')

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">General Setting</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                            <li class="breadcrumb-item active">Site General Settings</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Site Information</h4>
                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editSiteInfo">{{ empty($siteInfo) ? 'Add Site Information' : 'Update Site Information' }}</button>
                        </div>
                    </div><!-- end card header -->
                    @if(!empty($siteInfo))
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-6 col-xl-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <img class="rounded shadow" width="100%" src="{{asset($siteInfo->logo)}}" alt="Card image cap"> 
                                        <p>Logo</p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editSiteInfo" class="btn btn-primary">Edit Site Information</a>
                                </div>
                            </div><!-- end col -->
                        </div>
                    </div>
                    @endif
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
        
        <div id="editSiteInfo" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 overflow-hidden">
                    <div class="modal-header p-3">
                        <h4 class="card-title mb-0">Update Site Informations</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
        
                    <div class="modal-body">
                        <form action="{{ url('/admin/updateSiteInfo') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name='siteinfo_id' value="{{ !empty($siteInfo) ? $siteInfo->id : '' }}">
        
                            <div class="mb-3">
                                <label for="logo" class="form-label">Institution Logo <code>Dimension: 168px by 41px</code></label>
                                <input type="file" class="form-control" name='logo' id="logo">
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
</div>
@endsection