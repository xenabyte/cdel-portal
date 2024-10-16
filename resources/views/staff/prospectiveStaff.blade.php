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
                    <li class="breadcrumb-item active">Staff Applicant</li>
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
                    {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaff"><i class="mdi mdi-account-multiple-plus"></i> Add Staff</button> --}}
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($careerApplicants as $singleStaff)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $singleStaff->lastname .' '. $singleStaff->othernames }}</td>
                            <td>{{ $singleStaff->email }} </td>
                            <td>{{ $singleStaff->phone_number }} </td>
                            <td>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#viewApplicant{{$singleStaff->id}}" class="link-primary"><i class="ri-eye-fill"></i></a>
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
@foreach($careerApplicants as $singleStaff)
<div id="viewApplicant{{ $singleStaff->id }}" class="modal fade zoomIn" tabindex="-1" aria-labelledby="zoomInModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered  modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="zoomInModalLabel">Applicant Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <hr>
            </div>
            <div class="modal-body border-top border-top-dashed">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm me-3 flex-shrink-0">
                        <div class="avatar-title bg-info-subtle rounded">
                            <img src="{{ asset($singleStaff->image) }}" alt="" class="avatar-xs">
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fs-15 fw-semibold mb-0">{{ $singleStaff->lastname .' '. $singleStaff->othernames }}</h5>
                        <p class="text-muted mb-2">{{ $singleStaff->email.' | '.$singleStaff->phone_number }}</p>
                    </div>
                </div>
                @if(!empty($singleStaff->profile))
                <p class="text-muted pb-1 border-top border-top-dashed">{!! $singleStaff->profile->biodata !!}</p>
                <p class="text-muted pb-1 border-top border-top-dashed">{!! $singleStaff->profile->education_history !!}</p>
                <p class="text-muted pb-1 border-top border-top-dashed">{!! $singleStaff->profile->professional_information !!}</p>
                <p class="text-muted pb-1 border-top border-top-dashed">{!! $singleStaff->profile->publications !!}</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endforeach
@endsection