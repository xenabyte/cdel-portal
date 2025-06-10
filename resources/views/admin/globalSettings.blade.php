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
            <div class="col-lg-4">
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

             <div class="col-lg-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Registrar Settings</h4>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <form action="{{ url('/admin/setRegistrarSetting') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="sessionSetting_id" value="{{ $pageGlobalData->appSetting->id ?? '' }}">
                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <h4 class="card-title mb-2">Registrar Name</h4>
                                            <p><strong>Current:</strong> {{ $pageGlobalData->appSetting->registrar_name ?? 'Not Set' }}</p>
                                        </div>

                                        <div class="col-lg-6">
                                            <h4 class="card-title mb-2">Registrar Signature</h4>
                                            @if(!empty($pageGlobalData->appSetting) && !empty($pageGlobalData->appSetting->registrar_signature))
                                                <img class="img-thumbnail" alt="Registrar Signature" width="200" src="{{ asset($pageGlobalData->appSetting->registrar_signature) }}">
                                            @else
                                                <p class="text-muted">No signature set</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-5 border-top border-top-dashed">

                                        {{-- Registrar Name --}}
                                        <div class="col-lg-6">
                                            <h4 class="card-title mb-2">Registrar Name</h4>
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="registrar_name" name="registrar_name" placeholder="Enter registrar name">
                                                <label for="registrar_name">Registrar Name</label>
                                            </div>
                                        </div>

                                        {{-- Registrar Signature --}}
                                        <div class="col-lg-6">
                                            <h4 class="card-title mb-2">Registrar Signature</h4>
                                            <div class="form-floating mb-2">
                                                <input type="file" class="form-control" id="registrar_signature" name="registrar_signature">
                                                <label for="registrar_signature">Upload Signature</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mt-3">
                                            <div class="text-end">
                                                <button type="submit" id="submit-button" class="btn btn-primary">Update Settings</button>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div><!-- end card -->
            </div>
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