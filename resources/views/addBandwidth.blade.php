@extends('layouts.dashboard')
@section('content')


<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="mb-sm-0">Bandwidth Topup</h4>
                <div class="flex-shrink-0">
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 offset-md-2 ">
                        
                        <form action="{{ url('/bandwidthTopUp') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="file" class="form-label">Usernames File</label>
                                <input type="file" class="form-control" name='file' id="file">
                            </div>
        
                            <div class="row mt-3 g-3">
        
                                <div class="col-lg-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter Bandwidth">
                                        <label for="username">Bandwidth Username</label>
                                    </div>
                                </div>

                                <div class="form-floating mb-3">
                                    <select class="form-select" id="plan_id" name="plan_id" required>
                                        <option value="" disabled selected>Select Amount</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">
                                                ₦{{ number_format($plan->amount / 100, 2) }} - {{ $plan->title }}
                                                @if(env('BANDWIDTH_BONUS')) + {{ \App\Models\Plan::formatBytes($plan->bonus) }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="plan_id">Payment Amount <span class="text-danger">*</span></label>
                                </div>
        
                                <span class="text-muted"> Authentication</span><br>
                                <div class="col-lg-12">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your Passowrd" required>
                                        <label for="password">Password</label>
                                    </div>
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
