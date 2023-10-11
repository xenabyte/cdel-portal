@extends('layouts.dashboard')
@section('content')


<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="mb-sm-0">Staff Registration</h4>
                <div class="flex-shrink-0">
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 offset-md-2 ">
                        
                        <form action="{{ url('/addStaffRecord') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" class="form-control" name='image' id="emailInput">
                            </div>
        
                            <div class="row mt-3 g-3">
                                <span class="text-muted"> Bio Data</span><br>
                                <div class="col-lg-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter Title">
                                        <label for="title">Title(Mr/Miss/Mrs/Dr/Prof)</label>
                                    </div>
                                </div>
        
                                <div class="col-lg-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Enter lastname">
                                        <label for="lastname">Lastname(Surname)</label>
                                    </div>
                                </div>
        
                                <div class="col-lg-5">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="othernames" name="othernames" placeholder="Enter othernames">
                                        <label for="othernames">Othernames</label>
                                    </div>
                                </div>
        
                                <div class="col-lg-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                                        <label for="email">Staff Email</label>
                                    </div>
                                </div>
        
                                <div class="col-lg-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="staffId" name="staffId" placeholder="Enter Staff Id">
                                        <label for="staffId">Staff ID</label>
                                    </div>
                                </div>
        
                                <div class="col-lg-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter Staff Phone Number">
                                        <label for="phone_number">Staff Mobile Number</label>
                                    </div>
                                </div>
        
                                <span class="text-muted"> Authentication</span><br>
                                <div class="col-lg-6">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your Passowrd">
                                        <label for="password">Password</label>
                                    </div>
                                </div>
        
                                <div class="col-lg-6">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="confirm-password" name="confirm_password" placeholder="Enter your email">
                                        <label for="confirm-password">Confirm Password</label>
                                    </div>
                                </div>
        
                                <span class="text-muted"> Academic Information</span><br>
                                <div class="mb-3">
                                    <label for="category" class="form-label">Select Staff Category</label>
                                    <select class="form-select" aria-label="category" name="category">
                                        <option value= "" selected>Select Staff Category </option>
                                        <option value="Academic">Academic</option>
                                        <option value="Non Academic">Non Academic</option>
                                    </select>
                                </div>
        
                                <div class="mb-3">
                                    <label for="faculty" class="form-label">Select Staff Faculty</label>
                                    <select class="form-select" aria-label="faculty" name="faculty_id">
                                        <option value= "" selected>Select Staff Faculty </option>
                                        @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
            
                                <div class="mb-3">
                                    <label for="department" class="form-label">Select Staff Department</label>
                                    <select class="form-select" aria-label="department" name="department_id">
                                        <option value= "" selected>Select Staff Department </option>
                                        @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
        
                                <div class="mb-3">
                                    <label for="description" class="form-label">Write a short descriptive Note about yourself</label>
                                    <textarea type="text" class="form-control" name="description" id="description"></textarea>
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
