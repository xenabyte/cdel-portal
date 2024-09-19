@extends('staff.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Leave Application</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Leave Application</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="mb-sm-0">Leave Application</h4>
                <div class="flex-shrink-0">
                </div>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 offset-md-2 ">
                        
                        <form action="{{ url('/staff/applyForLeave') }}" method="post" enctype="multipart/form-data">
                            @csrf
            
                            <div class="row mt-3 g-3">

                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" required name="start_date" id="startdate">
                                        <label for="startdate">Start Date</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <input type="date" name="end_date" required class="form-control" id="enddate">
                                        <label for="enddate">Resumption Date</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="selectWithSearch" class="form-label">Name of Officer to act on your Duty while Away</label>
                                    <select class="form-select select2 selectWithSearch" id="selectWithSearch" name="assisting_staff_id" aria-label="cstatus">
                                            <option value="" selected>--Select--</option>
                                            @foreach($staff as $staffMember)@if($staffMember->id != Auth::guard('staff')->user()->id)<option value="{{$staffMember->id}}">{{ $staffMember->title.' '.$staffMember->lastname.' '.$staffMember->othernames}}</option>@endif @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Leave Purpose</label>
                                    <textarea type="text" class="form-control ckeditor" name="purpose" id="purpose"></textarea>
                                </div>
            
                                <div class="mb-3">
                                    <label for="destination_address" class="form-label">Destination</label>
                                    <textarea type="text" class="form-control ckeditor" name="destination_address" id="destiantion_address"></textarea>
                                </div>

                                <!--end col-->
                                <div class="col-lg-12 border-top border-top-dashed">
                                    <div class="d-flex align-items-start gap-3 mt-3">
                                        <button type="submit" id="submit-button" class="btn btn-primary btn-label right ms-auto nexttab" data-nexttab="pills-bill-address-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Submit</button>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>    
                        </form>
                    </div>
                </div>
            
            </div>
         
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@endsection
