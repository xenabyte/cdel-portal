@extends('admin.layout.dashboard')

@section('content')
<!-- start page title -->
<script src="https://cdn.tiny.cloud/1/b9d45cy4rlld8ypwkzb6yfzdza63fznxtcoc3iyit61r4rv9/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
          selector: 'textarea',
          plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
          toolbar_mode: 'floating',
        });
    </script>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Staff</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Staff-</li>
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaff"><i class="mdi mdi-account-multiple-plus"></i> Add Staff</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Image</th>
                            <th scope="col">Name</th>
                            <th scope="col">Staff ID</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $singleStaff)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>
                                <img class="img-thumbnail rounded-circle avatar-md"  src="{{ !empty($singleStaff->image) ? $singleStaff->image : asset('assets/images/users/user-dummy-img.jpg') }}">
                            </td>
                            <td>{{ $singleStaff->title.' '.$singleStaff->lastname .' '. $singleStaff->othernames }}</td>
                            <td>{{ $singleStaff->staffId }}</td>
                            <td>{{ $singleStaff->email }} </td>
                            <td>{{ $singleStaff->phone_number }} </td>
                            {{-- <td>{{ $singleStaff->category }} </td> --}}
                            {{-- <td>{{ !empty($singleStaff->faculty)?$singleStaff->faculty->name:null }} </td>
                            <td>{{ !empty($singleStaff->acad_department)?$singleStaff->acad_department->name:null }} </td> --}}
                            <td>
                                <a href="{{ url('admin/staff/'.$singleStaff->slug) }}" class="btn btn-primary"> <i class= "mdi mdi-monitor-eye"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editStaff{{$singleStaff->id}}" class="btn btn-info"><i class= "mdi mdi-application-edit"></i></a>
                            </td>

                            <div id="editStaff{{$singleStaff->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 overflow-hidden">
                                        <div class="modal-header p-3">
                                            <h4 class="card-title mb-0">Add Staff</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                            
                                        <div class="modal-body">
                                            <form action="{{ url('/admin/updateStaff') }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="staff_id" value="{{ $singleStaff->id }}">
                                                <div class="mb-3">
                                                    <label for="image" class="form-label">Image</label>
                                                    <input type="file" class="form-control" name='image' id="emailInput">
                                                </div>
                            
                                                <div class="row mt-3 g-3">
                                                    <span class="text-muted"> Bio Data</span><br>
                                                    <div class="col-lg-3">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control" id="title" name="title" value="{{ $singleStaff->title }}">
                                                            <label for="title">Title(Mr/Miss/Mrs/Dr/Prof)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control" id="lastname" name="lastname" value="{{ $singleStaff->lastname }}">
                                                            <label for="lastname">Lastname(Surname)</label>
                                                        </div>
                                                    </div>
                            
                                                    <div class="col-lg-5">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control" id="othernames" name="othernames" value="{{ $singleStaff->othernames }}">
                                                            <label for="othernames">Othernames</label>
                                                        </div>
                                                    </div>
                            
                                                    <div class="col-lg-6">
                                                        <div class="form-floating">
                                                            <input type="email" class="form-control" id="email" name="email" value="{{ $singleStaff->email }}">
                                                            <label for="email">Staff Email</label>
                                                        </div>
                                                    </div>
                            
                                                    <div class="col-lg-6">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control" id="staffId" name="staffId" value="{{ $singleStaff->staffId }}">
                                                            <label for="staffId">Staff ID</label>
                                                        </div>
                                                    </div>
                            
                                                    <div class="col-lg-12">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $singleStaff->phone_number }}">
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
                                                            <option @if($singleStaff->category == 'Academic') selected @endif value="Academic">Academic</option>
                                                            <option @if($singleStaff->category == 'Non Academic') selected @endif value="Non Academic">Non Academic</option>
                                                        </select>
                                                    </div>
                            
                                                    <div class="mb-3">
                                                        <label for="faculty" class="form-label">Select Staff Faculty</label>
                                                        <select class="form-select" aria-label="faculty" name="faculty_id">
                                                            <option value= "" selected>Select Staff Faculty </option>
                                                            @foreach($faculties as $faculty)
                                                            <option @if($singleStaff->faculty_id == $faculty->id) selected @endif value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                
                                                    <div class="mb-3">
                                                        <label for="department" class="form-label">Select Staff Department</label>
                                                        <select class="form-select" aria-label="department" name="department_id">
                                                            @foreach($departments as $department)
                                                            <option @if($singleStaff->department_id == $department->id) selected @endif value="{{ $department->id }}">{{ $department->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                            
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Staff Qualifications</label>
                                                        <textarea type="text" class="form-control" name="description" id="description">{{ $singleStaff->description }}</textarea>
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
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
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

<div id="addStaff" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Staff</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/addStaff') }}" method="post" enctype="multipart/form-data">
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
                            <label for="description" class="form-label">Staff Qualifications</label>
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
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
    

@endsection