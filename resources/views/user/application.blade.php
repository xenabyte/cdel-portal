@extends('user.layout.dashboard')

@section('content')

<div class="container-fluid">
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img src="assets/images/profile-bg.jpg" alt="" class="profile-wid-img" />
        </div>
    </div>
    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
        <div class="row g-4">
            <div class="col-auto">
                <div class="avatar-lg">
                    <img src="{{asset(empty($applicant->image)?'assets/images/users/user-dummy-img.jpg':$applicant->image)}}" alt="user-img" class="img-thumbnail rounded-circle" />
                </div>
            </div>
            <!--end col-->
            <div class="col">
                <div class="p-2">
                    <h3 class="text-white mb-1">{{ $applicant->lastname.' '. $applicant->othernames}}</h3>
                    <p class="text-white-75">{{ $applicant->application_number }}</p>
                    <div class="hstack text-white-50 gap-1">
                        <div class="me-2"><i class="ri-building-4-fill me-1 text-white-75 fs-16 align-middle"></i>@if(!empty($applicant->programme)){{ $applicant->programme->name }}@endif</div>
                        <div>
                            <i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i> {{ $applicant->jamb_reg_no }}
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
            <div class="col-12 col-lg-auto order-last order-lg-0">
                <div class="row text text-white-50 text-center">
                    <div class="col-lg-6 col-4">
                        <div class="p-2">
                           
                        </div>
                    </div>
                    <div class="col-lg-6 col-4">
                        <div class="p-2">
                            
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->

        </div>
        <!--end row-->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div>
                <div class="d-flex">
                    <!-- Nav tabs -->
                    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#overview-tab" role="tab">
                                <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Overview</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#programme" role="tab">
                                <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Programme</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#olevel" role="tab">
                                <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Olevel Result</span>
                            </a>
                        </li>

                        @if(!empty($applicant->application_type) && $applicant->application_type == 'UTME')
                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#jamb" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Jamb Result</span>
                            </a>
                        </li>
                        @elseif(!empty($applicant->application_type) && $applicant->application_type == 'DE')
                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#deresult" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Direct Result</span>
                            </a>
                        </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#guardian" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Guardian</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#nok" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Next of Kin</span>
                            </a>
                        </li>


                    </ul>
                   
                </div>
                <!-- Tab panes -->
                <div class="tab-content pt-4 text-muted">
                    <div class="tab-pane active" id="overview-tab" role="tabpanel">
                        <div class="row">
                            <div class="col-xxl-4">
                                @if($percent != 100 && empty($applicant->status))
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-5">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-0">Complete Your Application</h5>
                                            </div>
                                            <div class="flex-shrink-0">
                                            </div>
                                        </div>
                                        <div class="progress animated-progress custom-progress progress-label">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{$percent}}%" aria-valuenow="{{$percent}}" aria-valuemin="0" aria-valuemax="100">
                                                <div class="label">{{$percent}}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @elseif(strtolower($applicant->status) == 'admitted')
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-5">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-0">Congratulations</h5>
                                            </div>
                                            <div class="flex-shrink-0">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center text-center mb-5">
                                            <div class="flex-grow-1">
                                                <i class="fa fa-check fa-5x text-success"></i><br>
                                                <p class="muted">You have been granted admission, download Admission Letter from the button below and proceed to student dashboard.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @elseif(strtolower($applicant->status) == 'submitted')
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-5">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-0">Application form completed submitted, Pending Admission</h5>
                                            </div>
                                            <div class="flex-shrink-0">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center text-center mb-5">
                                            <div class="flex-grow-1">
                                                <i class="fa fa-spinner fa-spin fa-5x text-danger"></i>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                @endif

                                @if($percent == 100 && empty($applicant->status))
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-0">Submit Application</h5>
                                                <hr>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- Warning Alert -->
                                            <div class="alert alert-warning alert-dismissible alert-additional shadow fade show mb-0" role="alert">
                                                <div class="alert-body">
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 me-3">
                                                            <i class="ri-alert-line fs-16 align-middle"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h5 class="alert-heading">Ensure you have added all information</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="alert-content">
                                                    <p class="mb-0">You will not be able to update the information after clicking "Submit Application"</p>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <br>
                                                <form action="{{ url('applicant/submitApplication') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <button type="submit" class="btn btn-block btn-primary">Submit Applicattion</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif


                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Info</h5>
                                        <hr>
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Full Name: </th>
                                                        <td class="text-muted">{{ $applicant->lastname.' '. $applicant->othernames}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Phone Number: </th>
                                                        <td class="text-muted">{{ $applicant->phone_number }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">E-mail: </th>
                                                        <td class="text-muted">{{ $applicant->email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Academic session: </th>
                                                        <td class="text-muted">{{ $applicant->academic_session }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Programmme: </th>
                                                        <td class="text-muted">@if(!empty($applicant->programme)){{ $applicant->programme->name }}@endif</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div>


                            <!--end col-->
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Bio-Data</h5>
                                        <form action="{{ url('applicant/saveBioData') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-12 text-center">
                                                    <div class="profile-user position-relative d-inline-block mx-auto mb-2">
                                                        <img src="{{asset(empty($applicant->image)?'assets/images/users/user-dummy-img.jpg':$applicant->image)}}" class="rounded-circle avatar-lg img-thumbnail user-profile-image" alt="user-profile-image">
                                                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                                            <input id="profile-img-file-input" type="file" class="profile-img-file-input" accept="image/png, image/jpeg" name="image" required>
                                                            <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                                                <span class="avatar-title rounded-circle bg-light text-body">
                                                                    <i class="ri-camera-fill"></i>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <h5 class="fs-14">Add Passport Photograph</h5>
                                                </div>
                                                <hr>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <label for="lastname" class="form-label">Last Name(Surname)</label>
                                                        <input type="text" class="form-control" id="lastname" name="lastname" value="{{ $applicant->lastname }}" disabled readonly>
                                                    </div>
                                                </div>
    
                                                <div class="col-lg-8">
                                                    <div class="mb-3">
                                                        <label for="othernames" class="form-label">Other Names</label>
                                                        <input type="text" class="form-control" id="othernames" name="othernames" value="{{ $applicant->othernames }}" disabled readonly>
                                                    </div>
                                                </div>
                                                <!--end col-->
    
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="phone_number" class="form-label">Phone Number</label>
                                                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $applicant->phone_number }}" disabled  readonly>
                                                    </div>
                                                </div>
    
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">Email Address</label>
                                                        <input type="text" class="form-control" id="email" name="email" value="{{ $applicant->email }}" disabled  readonly>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="dob" class="form-label">Date of Birth</label>
                                                        <input type="date" class="form-control"  id="dob" name="dob" value="{{ $applicant->dob }}" required />
                                                    </div>
                                                </div>
                                                <!--end col-->
    
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="religion" class="form-label">Religion</label>
                                                        <select class="form-control" name="religion" id="religion" required>
                                                            <option value="" @if($applicant->religion == '') selected  @endif >Select Religion</option>
                                                            <option @if($applicant->religion == 'Christianity') selected  @endif value="Christianity">Christianity</option>
                                                            <option @if($applicant->religion == 'Islamic') selected  @endif value="Islamic">Islamic</option>
                                                            <option @if($applicant->religion == 'Others') selected  @endif value="Others">Others</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!--end col-->
    
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <label for="gender" class="form-label">Gender</label>
                                                        <select class="form-control" name="gender" id="gender" required>
                                                            <option @if($applicant->gender == '') selected  @endif value="" selected>Select Gender</option>
                                                            <option @if($applicant->gender == 'Male') selected  @endif value="Male">Male</option>
                                                            <option @if($applicant->gender == 'Female') selected  @endif value="Female">Female</option>
                                                        </select>
                                                    </div>
                                                </div>
    
    
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <label for="marital_status" class="form-label">Marital Status</label>
                                                        <select class="form-control" name="marital_status" id="marital_status" required>
                                                            <option @if($applicant->marital_status == '') selected  @endif value="" selected>Select Marital Status</option>
                                                            <option @if($applicant->marital_status == 'Single') selected  @endif value="Single">Single</option>
                                                            <option @if($applicant->marital_status == 'Married') selected  @endif value="Married">Married</option>
                                                        </select>
                                                    </div>
                                                </div>
    
    
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <label for="nationality" class="form-label">Nationality - {{ $applicant->nationality }}</label>
                                                        <select class="form-control" name="nationality" id="nationality" required>
                                                            <option value="Nigeria">Nigeria</option>
                                                            <option value="Afghanistan">Afghanistan</option>
                                                            <option value="Åland Islands">Åland Islands</option>
                                                            <option value="Albania">Albania</option>
                                                            <option value="Algeria">Algeria</option>
                                                            <option value="American Samoa">American Samoa</option>
                                                            <option value="Andorra">Andorra</option>
                                                            <option value="Angola">Angola</option>
                                                            <option value="Anguilla">Anguilla</option>
                                                            <option value="Antarctica">Antarctica</option>
                                                            <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                                            <option value="Argentina">Argentina</option>
                                                            <option value="Armenia">Armenia</option>
                                                            <option value="Aruba">Aruba</option>
                                                            <option value="Australia">Australia</option>
                                                            <option value="Austria">Austria</option>
                                                            <option value="Azerbaijan">Azerbaijan</option>
                                                            <option value="Bahamas">Bahamas</option>
                                                            <option value="Bahrain">Bahrain</option>
                                                            <option value="Bangladesh">Bangladesh</option>
                                                            <option value="Barbados">Barbados</option>
                                                            <option value="Belarus">Belarus</option>
                                                            <option value="Belgium">Belgium</option>
                                                            <option value="Belize">Belize</option>
                                                            <option value="Benin">Benin</option>
                                                            <option value="Bermuda">Bermuda</option>
                                                            <option value="Bhutan">Bhutan</option>
                                                            <option value="Bolivia">Bolivia</option>
                                                            <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                                            <option value="Botswana">Botswana</option>
                                                            <option value="Bouvet Island">Bouvet Island</option>
                                                            <option value="Brazil">Brazil</option>
                                                            <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                                                            <option value="Brunei Darussalam">Brunei Darussalam</option>
                                                            <option value="Bulgaria">Bulgaria</option>
                                                            <option value="Burkina Faso">Burkina Faso</option>
                                                            <option value="Burundi">Burundi</option>
                                                            <option value="Cambodia">Cambodia</option>
                                                            <option value="Cameroon">Cameroon</option>
                                                            <option value="Canada">Canada</option>
                                                            <option value="Cape Verde">Cape Verde</option>
                                                            <option value="Cayman Islands">Cayman Islands</option>
                                                            <option value="Central African Republic">Central African Republic</option>
                                                            <option value="Chad">Chad</option>
                                                            <option value="Chile">Chile</option>
                                                            <option value="China">China</option>
                                                            <option value="Christmas Island">Christmas Island</option>
                                                            <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                                                            <option value="Colombia">Colombia</option>
                                                            <option value="Comoros">Comoros</option>
                                                            <option value="Congo">Congo</option>
                                                            <option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option>
                                                            <option value="Cook Islands">Cook Islands</option>
                                                            <option value="Costa Rica">Costa Rica</option>
                                                            <option value="Cote D'ivoire">Cote D'ivoire</option>
                                                            <option value="Croatia">Croatia</option>
                                                            <option value="Cuba">Cuba</option>
                                                            <option value="Cyprus">Cyprus</option>
                                                            <option value="Czech Republic">Czech Republic</option>
                                                            <option value="Denmark">Denmark</option>
                                                            <option value="Djibouti">Djibouti</option>
                                                            <option value="Dominica">Dominica</option>
                                                            <option value="Dominican Republic">Dominican Republic</option>
                                                            <option value="Ecuador">Ecuador</option>
                                                            <option value="Egypt">Egypt</option>
                                                            <option value="El Salvador">El Salvador</option>
                                                            <option value="Equatorial Guinea">Equatorial Guinea</option>
                                                            <option value="Eritrea">Eritrea</option>
                                                            <option value="Estonia">Estonia</option>
                                                            <option value="Ethiopia">Ethiopia</option>
                                                            <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
                                                            <option value="Faroe Islands">Faroe Islands</option>
                                                            <option value="Fiji">Fiji</option>
                                                            <option value="Finland">Finland</option>
                                                            <option value="France">France</option>
                                                            <option value="French Guiana">French Guiana</option>
                                                            <option value="French Polynesia">French Polynesia</option>
                                                            <option value="French Southern Territories">French Southern Territories</option>
                                                            <option value="Gabon">Gabon</option>
                                                            <option value="Gambia">Gambia</option>
                                                            <option value="Georgia">Georgia</option>
                                                            <option value="Germany">Germany</option>
                                                            <option value="Ghana">Ghana</option>
                                                            <option value="Gibraltar">Gibraltar</option>
                                                            <option value="Greece">Greece</option>
                                                            <option value="Greenland">Greenland</option>
                                                            <option value="Grenada">Grenada</option>
                                                            <option value="Guadeloupe">Guadeloupe</option>
                                                            <option value="Guam">Guam</option>
                                                            <option value="Guatemala">Guatemala</option>
                                                            <option value="Guernsey">Guernsey</option>
                                                            <option value="Guinea">Guinea</option>
                                                            <option value="Guinea-bissau">Guinea-bissau</option>
                                                            <option value="Guyana">Guyana</option>
                                                            <option value="Haiti">Haiti</option>
                                                            <option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option>
                                                            <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
                                                            <option value="Honduras">Honduras</option>
                                                            <option value="Hong Kong">Hong Kong</option>
                                                            <option value="Hungary">Hungary</option>
                                                            <option value="Iceland">Iceland</option>
                                                            <option value="India">India</option>
                                                            <option value="Indonesia">Indonesia</option>
                                                            <option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option>
                                                            <option value="Iraq">Iraq</option>
                                                            <option value="Ireland">Ireland</option>
                                                            <option value="Isle of Man">Isle of Man</option>
                                                            <option value="Israel">Israel</option>
                                                            <option value="Italy">Italy</option>
                                                            <option value="Jamaica">Jamaica</option>
                                                            <option value="Japan">Japan</option>
                                                            <option value="Jersey">Jersey</option>
                                                            <option value="Jordan">Jordan</option>
                                                            <option value="Kazakhstan">Kazakhstan</option>
                                                            <option value="Kenya">Kenya</option>
                                                            <option value="Kiribati">Kiribati</option>
                                                            <option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option>
                                                            <option value="Korea, Republic of">Korea, Republic of</option>
                                                            <option value="Kuwait">Kuwait</option>
                                                            <option value="Kyrgyzstan">Kyrgyzstan</option>
                                                            <option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
                                                            <option value="Latvia">Latvia</option>
                                                            <option value="Lebanon">Lebanon</option>
                                                            <option value="Lesotho">Lesotho</option>
                                                            <option value="Liberia">Liberia</option>
                                                            <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
                                                            <option value="Liechtenstein">Liechtenstein</option>
                                                            <option value="Lithuania">Lithuania</option>
                                                            <option value="Luxembourg">Luxembourg</option>
                                                            <option value="Macao">Macao</option>
                                                            <option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option>
                                                            <option value="Madagascar">Madagascar</option>
                                                            <option value="Malawi">Malawi</option>
                                                            <option value="Malaysia">Malaysia</option>
                                                            <option value="Maldives">Maldives</option>
                                                            <option value="Mali">Mali</option>
                                                            <option value="Malta">Malta</option>
                                                            <option value="Marshall Islands">Marshall Islands</option>
                                                            <option value="Martinique">Martinique</option>
                                                            <option value="Mauritania">Mauritania</option>
                                                            <option value="Mauritius">Mauritius</option>
                                                            <option value="Mayotte">Mayotte</option>
                                                            <option value="Mexico">Mexico</option>
                                                            <option value="Micronesia, Federated States of">Micronesia, Federated States of</option>
                                                            <option value="Moldova, Republic of">Moldova, Republic of</option>
                                                            <option value="Monaco">Monaco</option>
                                                            <option value="Mongolia">Mongolia</option>
                                                            <option value="Montenegro">Montenegro</option>
                                                            <option value="Montserrat">Montserrat</option>
                                                            <option value="Morocco">Morocco</option>
                                                            <option value="Mozambique">Mozambique</option>
                                                            <option value="Myanmar">Myanmar</option>
                                                            <option value="Namibia">Namibia</option>
                                                            <option value="Nauru">Nauru</option>
                                                            <option value="Nepal">Nepal</option>
                                                            <option value="Netherlands">Netherlands</option>
                                                            <option value="Netherlands Antilles">Netherlands Antilles</option>
                                                            <option value="New Caledonia">New Caledonia</option>
                                                            <option value="New Zealand">New Zealand</option>
                                                            <option value="Nicaragua">Nicaragua</option>
                                                            <option value="Niger">Niger</option>
                                                            <option value="Niue">Niue</option>
                                                            <option value="Norfolk Island">Norfolk Island</option>
                                                            <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                                                            <option value="Norway">Norway</option>
                                                            <option value="Oman">Oman</option>
                                                            <option value="Pakistan">Pakistan</option>
                                                            <option value="Palau">Palau</option>
                                                            <option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
                                                            <option value="Panama">Panama</option>
                                                            <option value="Papua New Guinea">Papua New Guinea</option>
                                                            <option value="Paraguay">Paraguay</option>
                                                            <option value="Peru">Peru</option>
                                                            <option value="Philippines">Philippines</option>
                                                            <option value="Pitcairn">Pitcairn</option>
                                                            <option value="Poland">Poland</option>
                                                            <option value="Portugal">Portugal</option>
                                                            <option value="Puerto Rico">Puerto Rico</option>
                                                            <option value="Qatar">Qatar</option>
                                                            <option value="Reunion">Reunion</option>
                                                            <option value="Romania">Romania</option>
                                                            <option value="Russian Federation">Russian Federation</option>
                                                            <option value="Rwanda">Rwanda</option>
                                                            <option value="Saint Helena">Saint Helena</option>
                                                            <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                                            <option value="Saint Lucia">Saint Lucia</option>
                                                            <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                                                            <option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option>
                                                            <option value="Samoa">Samoa</option>
                                                            <option value="San Marino">San Marino</option>
                                                            <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                                            <option value="Senegal">Senegal</option>
                                                            <option value="Serbia">Serbia</option>
                                                            <option value="Seychelles">Seychelles</option>
                                                            <option value="Sierra Leone">Sierra Leone</option>
                                                            <option value="Singapore">Singapore</option>
                                                            <option value="Slovakia">Slovakia</option>
                                                            <option value="Slovenia">Slovenia</option>
                                                            <option value="Solomon Islands">Solomon Islands</option>
                                                            <option value="Somalia">Somalia</option>
                                                            <option value="South Africa">South Africa</option>
                                                            <option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option>
                                                            <option value="Spain">Spain</option>
                                                            <option value="Sri Lanka">Sri Lanka</option>
                                                            <option value="Sudan">Sudan</option>
                                                            <option value="Suriname">Suriname</option>
                                                            <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                                                            <option value="Swaziland">Swaziland</option>
                                                            <option value="Sweden">Sweden</option>
                                                            <option value="Switzerland">Switzerland</option>
                                                            <option value="Syrian Arab Republic">Syrian Arab Republic</option>
                                                            <option value="Taiwan">Taiwan</option>
                                                            <option value="Tajikistan">Tajikistan</option>
                                                            <option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
                                                            <option value="Thailand">Thailand</option>
                                                            <option value="Timor-leste">Timor-leste</option>
                                                            <option value="Togo">Togo</option>
                                                            <option value="Tokelau">Tokelau</option>
                                                            <option value="Tonga">Tonga</option>
                                                            <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                                            <option value="Tunisia">Tunisia</option>
                                                            <option value="Turkey">Turkey</option>
                                                            <option value="Turkmenistan">Turkmenistan</option>
                                                            <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                                                            <option value="Tuvalu">Tuvalu</option>
                                                            <option value="Uganda">Uganda</option>
                                                            <option value="Ukraine">Ukraine</option>
                                                            <option value="United Arab Emirates">United Arab Emirates</option>
                                                            <option value="United Kingdom">United Kingdom</option>
                                                            <option value="United States">United States</option>
                                                            <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
                                                            <option value="Uruguay">Uruguay</option>
                                                            <option value="Uzbekistan">Uzbekistan</option>
                                                            <option value="Vanuatu">Vanuatu</option>
                                                            <option value="Venezuela">Venezuela</option>
                                                            <option value="Viet Nam">Viet Nam</option>
                                                            <option value="Virgin Islands, British">Virgin Islands, British</option>
                                                            <option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
                                                            <option value="Wallis and Futuna">Wallis and Futuna</option>
                                                            <option value="Western Sahara">Western Sahara</option>
                                                            <option value="Yemen">Yemen</option>
                                                            <option value="Zambia">Zambia</option>
                                                            <option value="Zimbabwe">Zimbabwe</option>
                                                        </select>
                                                    </div>
                                                </div>
    
    
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="state_of_origin" class="form-label">State of Origin - {{ $applicant->state }}</label>
                                                        <select onchange="toggleLGA(this);" name="state" id="state" class="form-control" required>
                                                            <option value="" selected="selected">- Select -</option>
                                                            <option value="Abia">Abia</option>
                                                            <option value="Adamawa">Adamawa</option>
                                                            <option value="AkwaIbom">AkwaIbom</option>
                                                            <option value="Anambra">Anambra</option>
                                                            <option value="Bauchi">Bauchi</option>
                                                            <option value="Bayelsa">Bayelsa</option>
                                                            <option value="Benue">Benue</option>
                                                            <option value="Borno">Borno</option>
                                                            <option value="Cross River">Cross River</option>
                                                            <option value="Delta">Delta</option>
                                                            <option value="Ebonyi">Ebonyi</option>
                                                            <option value="Edo">Edo</option>
                                                            <option value="Ekiti">Ekiti</option>
                                                            <option value="Enugu">Enugu</option>
                                                            <option value="FCT">FCT</option>
                                                            <option value="Gombe">Gombe</option>
                                                            <option value="Imo">Imo</option>
                                                            <option value="Jigawa">Jigawa</option>
                                                            <option value="Kaduna">Kaduna</option>
                                                            <option value="Kano">Kano</option>
                                                            <option value="Katsina">Katsina</option>
                                                            <option value="Kebbi">Kebbi</option>
                                                            <option value="Kogi">Kogi</option>
                                                            <option value="Kwara">Kwara</option>
                                                            <option value="Lagos">Lagos</option>
                                                            <option value="Nasarawa">Nasarawa</option>
                                                            <option value="Niger">Niger</option>
                                                            <option value="Ogun">Ogun</option>
                                                            <option value="Ondo">Ondo</option>
                                                            <option value="Osun">Osun</option>
                                                            <option value="Oyo">Oyo</option>
                                                            <option value="Plateau">Plateau</option>
                                                            <option value="Rivers">Rivers</option>
                                                            <option value="Sokoto">Sokoto</option>
                                                            <option value="Taraba">Taraba</option>
                                                            <option value="Yobe">Yobe</option>
                                                            <option value="Zamfara">Zamafara</option>
                                                        </select>
                                                    </div>
                                                </div>
    
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="lga" class="form-label">Local Government Area - {{ $applicant->lga }}</label>
                                                        <select name="lga" id="lga" class="form-control select-lga" required></select>
                                                    </div>
                                                </div>
    
                                                <div class="col-lg-12">
                                                    <label for="address">Address</label>
                                                    <textarea id="address" name="address" >{!! $applicant->address !!}</textarea>
                                                </div><!--end col-->
    
                                                <hr>
                                                @if(empty($applicant->status))
                                                <div class="col-lg-12">
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                                @endif
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </form>
                                    </div>
                                    <!--end card-body-->
                                </div><!-- end card -->

                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>

                    <div class="tab-pane" id="programme" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-4 pb-2">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0 avatar-sm">
                                            <div class="avatar-title bg-light text-primary rounded-3 fs-18 shadow">
                                                <i class="ri-award-fill"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6></h6>
                                            <p class="text-muted mb-0"><strong>Programme:</strong> @if(!empty($applicant->programme)){{ $applicant->programme->name }}@endif</p>
                                        </div>
                                        <div>
                                            
                                        </div>
                                    </div>
            
                                    <hr>
                                    @if(empty($applicant->application_type))
                                    <form action="{{ url('applicant/saveProgramme') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
            
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="application_type">Application Type</label>
                                                <select class="form-select" name="application_type" id="application_type">
                                                    <option selected>Choose...</option>
                                                    <option value="UTME"> UTME</option>
                                                    <option value="DE"> Direct Entry</option>
                                                </select>
                                            </div>
                                        </div><!--end col-->

                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="jamb_reg" class="form-label">Jamb Registration Number</label>
                                                <input type="text" class="form-control" id="jamb_reg" name="jamb_reg_no" value="{{ $applicant->jamb_reg_no }}" required>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row g-2">
                                            @if(empty($applicant->status))
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-success">Save</button>
                                                </div>
                                            </div>
                                            @endif
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--end tab-pane-->
                    <div class="tab-pane fade" id="olevel" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">O'Level Results:</h5>
                                    <hr>
                                    @if(empty($applicant->sitting_no))
                                    <form action="{{ url('applicant/saveSitting') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row gx-3 gy-2 align-items-center">
                                            <div class="col-sm-3">
                                                <label for="subject">Number of sittings</label>
                                                <select class="form-select" name="sitting_no" id="subject" required>
                                                    <option selected>Choose...</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                </select>
                                            </div><!--end col-->

                                            <br>
                                            <div class="col-md-12">
                                                <label for="schools_attended">School(s) Attended</label>
                                                <textarea id="schools_attended" name="schools_attended" >{!! $applicant->schools_attended !!}</textarea>
                                            </div><!--end col-->

                                            <div class="col-auto">
                                                <br>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div><!--end col-->
                                        </div>
                                    </form>
                                    @else
                                        @if(empty($applicant->status))
                                        <form action="{{ url('applicant/addOlevel') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row gx-3 gy-2 align-items-center">
                                                <div class="col-sm-3">
                                                    <label for="subject">Subject</label>
                                                    <select class="form-select" name="subject" id="subject" required>
                                                        <option selected>Choose...</option>
                                                        <option value="Agricultural Science">Agricultural Science</option>
                                                        <option value="Animal Husbandry">Animal Husbandry</option>
                                                        <option value="Automobile Parts Merchandising">Automobile Parts Merchandising</option>
                                                        <option value="Biology">Biology</option>
                                                        <option value="Book Keeping">Book Keeping</option>
                                                        <option value="Catering Craft Practice">Catering Craft Practice</option>
                                                        <option value="Chemistry">Chemistry</option>
                                                        <option value="Christian Studies">Christian Studies</option>
                                                        <option value="Civic Education">Civic Education</option>
                                                        <option value="Clothing & Textile">Clothing & Textile</option>
                                                        <option value="Commerce">Commerce</option>
                                                        <option value="Computer & IT">Computer & IT</option>
                                                        <option value="Cosmetology">Cosmetology</option>
                                                        <option value="Dyeing & Bleaching">Dyeing & Bleaching</option>
                                                        <option value="Economics">Economics</option>
                                                        <option value="English Language">English Language</option>
                                                        <option value="Financial Accounting">Financial Accounting</option>
                                                        <option value="Fisheries">Fisheries</option>
                                                        <option value="Food & Nutrition">Food & Nutrition</option>
                                                        <option value="Further Mathematics">Further Mathematics</option>
                                                        <option value="Garment Making">Garment Making</option>
                                                        <option value="General Mathematics">General Mathematics</option>
                                                        <option value="Geography">Geography</option>
                                                        <option value="Government">Government</option>
                                                        <option value="Hausa">Hausa</option>
                                                        <option value="Health Education">Health Education</option>
                                                        <option value="History">History</option>
                                                        <option value="Home Management">Home Management</option>
                                                        <option value="Igbo">Igbo</option>
                                                        <option value="Insurance">Insurance</option>
                                                        <option value="Islamic Studies">Islamic Studies</option>
                                                        <option value="Literature in English">Literature in English</option>
                                                        <option value="Marketing">Marketing</option>
                                                        <option value="Music">Music</option>
                                                        <option value="Office Practice">Office Practice</option>
                                                        <option value="Painting & Decorating">Painting & Decorating</option>
                                                        <option value="Photography">Photography</option>
                                                        <option value="Physical Education">Physical Education</option>
                                                        <option value="Physics">Physics</option>
                                                        <option value="Salesmanship">Salesmanship</option>
                                                        <option value="Stenography">Stenography</option>
                                                        <option value="Store Keeping">Store Keeping</option>
                                                        <option value="Store Management">Store Management</option>
                                                        <option value="Tourism">Tourism</option>
                                                        <option value="Type Writing">Type Writing</option>
                                                        <option value="Visual Art">Visual Art</option>
                                                        <option value="Yoruba">Yoruba</option>
                                                    </select>
                                                </div><!--end col-->
                                                <div class="col-sm-2">
                                                    <label for="grade">Grade</label>
                                                    <select class="form-select" name="grade" id="grade" required>
                                                        <option selected>Choose...</option>
                                                        <option value="A1">A1</option>
                                                        <option value="B2">B2</option>
                                                        <option value="B3">B3</option>
                                                        <option value="C4">C4</option>
                                                        <option value="C5">C5</option>
                                                        <option value="C6">C6</option>
                                                        <option value="D7">D7</option>
                                                        <option value="E8">E8</option>
                                                        <option value="F9">F9</option>
                                                    </select>
                                                </div><!--end col-->

                                                <div class="col-sm-2">
                                                    <label for="year">Year</label>
                                                    <input type="number" min="2010" max="2099" step="1" name="year" class="form-control" id="year" required>
                                                </div><!--end col-->

                                                <div class="col-sm-3">
                                                    <label for="reg_no">Registration Number</label>
                                                    <input type="text" name="reg_no" class="form-control" id="reg_no" required>
                                                </div><!--end col-->

                                                <div class="col-auto">
                                                    <br>
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div><!--end col-->
                                            </div>
                                        </form>
                                        @endif
                                    @endif
                                    <hr>
                                    @if(!empty($applicant->olevel_1))
                                    <div class="row mb-2">
                                        <div class="col-sm-6 col-xl-12">
                                            <!-- Simple card -->
                                            <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">Olevel Result</span>
                                            <div class="text-end">
                                                <a href="{{ asset($applicant->olevel_1) }}" target="blank" class="btn btn-success">View</a>
                                            </div>
                                            @if($applicant->sitting_no > 1)
                                            <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">Olevel Result (Second Sitting)</span>
                                            <div class="text-end">
                                                <a href="{{ asset($applicant->olevel_2) }}" target="blank"  class="btn btn-success">View</a>
                                            </div>
                                            @endif
                                        </div><!-- end col -->
                                    </div>
                                    @endif
                                    <hr>
                                    <table class="table table-borderedless table-nowrap">
                                        <thead>
                                            <tr>
                                                <th scope="col">Id</th>
                                                <th scope="col">Subject</th>
                                                <th scope="col">Grade</th>
                                                <th scope="col">Registration Number</th>
                                                <th scope="col">Year</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($applicant->olevels as $olevel)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $olevel->subject }}</td>
                                                <td>{{ $olevel->grade }}</td>
                                                <td>{{ $olevel->reg_no }}</td>
                                                <td>{{ $olevel->year }}</td>
                                                <td>
                                                    <div class="hstack gap-3 fs-15">
                                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteGrade{{$olevel->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>
        
                                                        <div id="deleteGrade{{$olevel->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-body text-center p-5">
                                                                        <div class="text-end">
                                                                            <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="mt-2">
                                                                            <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                                                                            </lord-icon>
                                                                            <h4 class="mb-3 mt-4">Are you sure you want to delete <br>{{ $olevel->subject }}?</h4>
                                                                            <form action="{{ url('/applicant/deleteOlevel') }}" method="POST">
                                                                                @csrf
                                                                                <input name="olevel_id" type="hidden" value="{{$olevel->id}}">
        
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
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if($applicant->olevels->count() > 4 && empty($applicant->status))
                                    <hr>
                                    <h5 class="card-title mb-3">Upload O'Level Results Printout:</h5>
                                        <form action="{{ url('applicant/uploadOlevel') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row ">
    
                                                <div class="col-md-12 mt-3">
                                                    <label for="olevel1">Upload Olevel @if($applicant->sitting_no > 1) (first Sitting) @endif</label>
                                                    <input type="file" name="olevel_1" class="form-control" id="olevel1">
                                                </div><!--end col-->
    
                                                @if($applicant->sitting_no > 1)
                                                <div class="col-md-12 mt-3">
                                                    <label for="olevel2">Upload Olevel (Second Sitting)</label>
                                                    <input type="file" name="olevel_2" class="form-control" id="olevel2">
                                                </div><!--end col-->
                                                @endif
    
                                                <div class="col-auto">
                                                    <br>
                                                    <button type="submit" class="btn btn-primary">Upload</button>
                                                </div><!--end col-->
                                            </div>
                                        </form>
                                    @endif
                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>

                    <!--end tab-pane-->
                    <div class="tab-pane fade" id="jamb" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-4 pb-2">
                                    <h5 class="card-title mb-3">UTME Results:</h5>
                                    <hr>
                                    @if(empty($applicant->jamb_reg_no))
                                        <form action="{{ url('applicant/saveUtme') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="jamb_reg" class="form-label">Jamb Registration Number</label>
                                                    <input type="text" class="form-control" id="jamb_reg" name="jamb_reg_no" value="{{ $applicant->jamb_reg_no }}" required>
                                                </div>
                                            </div>

                                            <hr>
                                            <div class="row g-2">
                                                @if(empty($applicant->status))
                                                <div class="col-lg-12">
                                                    <div class="text-end">
                                                        <button type="submit" class="btn btn-success">Save</button>
                                                    </div>
                                                </div>
                                                @endif
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </form>
                                    @else
                                        @if(!empty($applicant->utme))
                                        <div class="row mb-2">
                                            <div class="col-sm-6 col-xl-12">
                                                <!-- Simple card -->
                                                <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">UTME Result Printout</span>
                                                <div class="text-end">
                                                    <a href="{{ asset($applicant->utme) }}"  target="blank" class="btn btn-success">View</a>
                                                </div>
                                            </div><!-- end col -->
                                        </div>
                                        @endif
                                        <hr>
                                        @if(empty($applicant->status))
                                        <form action="{{ url('applicant/addUtme') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row gx-3 gy-2 align-items-center">
                                                <div class="col-sm-5">
                                                    <label for="subject">Subject</label>
                                                    <select class="form-select" name="subject" id="subject">
                                                        <option value="Use of English">Use of English</option>
                                                        <option value="Agricultural Science">Agricultural Science</option>
                                                        <option value="Animal Husbandry">Animal Husbandry</option>
                                                        <option value="Automobile Parts Merchandising">Automobile Parts Merchandising</option>
                                                        <option value="Biology">Biology</option>
                                                        <option value="Book Keeping">Book Keeping</option>
                                                        <option value="Catering Craft Practice">Catering Craft Practice</option>
                                                        <option value="Chemistry">Chemistry</option>
                                                        <option value="Christian Studies">Christian Studies</option>
                                                        <option value="Civic Education">Civic Education</option>
                                                        <option value="Clothing & Textile">Clothing & Textile</option>
                                                        <option value="Commerce">Commerce</option>
                                                        <option value="Computer & IT">Computer & IT</option>
                                                        <option value="Cosmetology">Cosmetology</option>
                                                        <option value="Dyeing & Bleaching">Dyeing & Bleaching</option>
                                                        <option value="Economics">Economics</option>
                                                        <option value="Financial Accounting">Financial Accounting</option>
                                                        <option value="Fisheries">Fisheries</option>
                                                        <option value="Food & Nutrition">Food & Nutrition</option>
                                                        <option value="Further Mathematics">Further Mathematics</option>
                                                        <option value="Garment Making">Garment Making</option>
                                                        <option value="General Mathematics">General Mathematics</option>
                                                        <option value="Geography">Geography</option>
                                                        <option value="Government">Government</option>
                                                        <option value="Hausa">Hausa</option>
                                                        <option value="Health Education">Health Education</option>
                                                        <option value="History">History</option>
                                                        <option value="Home Management">Home Management</option>
                                                        <option value="Igbo">Igbo</option>
                                                        <option value="Insurance">Insurance</option>
                                                        <option value="Islamic Studies">Islamic Studies</option>
                                                        <option value="Literature in English">Literature in English</option>
                                                        <option value="Marketing">Marketing</option>
                                                        <option value="Music">Music</option>
                                                        <option value="Office Practice">Office Practice</option>
                                                        <option value="Painting & Decorating">Painting & Decorating</option>
                                                        <option value="Photography">Photography</option>
                                                        <option value="Physical Education">Physical Education</option>
                                                        <option value="Physics">Physics</option>
                                                        <option value="Salesmanship">Salesmanship</option>
                                                        <option value="Stenography">Stenography</option>
                                                        <option value="Store Keeping">Store Keeping</option>
                                                        <option value="Store Management">Store Management</option>
                                                        <option value="Tourism">Tourism</option>
                                                        <option value="Type Writing">Type Writing</option>
                                                        <option value="Visual Art">Visual Art</option>
                                                        <option value="Yoruba">Yoruba</option>
                                                    </select>
                                                </div><!--end col-->
                                                
                                                <div class="col-sm-5">
                                                    <label for="year">Score</label>
                                                    <input type="number" min="0" max="400" step="1" name="score" class="form-control" id="year">
                                                </div><!--end col-->

                                                <div class="col-auto">
                                                    <br>
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div><!--end col-->
                                            </div>
                                        </form>
                                        @endif
                                        <hr>
                                        <table class="table table-borderedless table-nowrap">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Id</th>
                                                    <th scope="col">Subject</th>
                                                    <th scope="col">Score</th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($applicant->utmes as $utme)
                                                <tr>
                                                    <th scope="row">{{ $loop->iteration }}</th>
                                                    <td>{{ $utme->subject }}</td>
                                                    <td>{{ $utme->score }}</td>
                                                    <td>
                                                        <div class="hstack gap-3 fs-15">
                                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteScore{{$utme->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>
            
                                                            <div id="deleteScore{{$utme->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <div class="modal-body text-center p-5">
                                                                            <div class="text-end">
                                                                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="mt-2">
                                                                                <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                                                                                </lord-icon>
                                                                                <h4 class="mb-3 mt-4">Are you sure you want to delete <br>{{ $utme->subject }}?</h4>
                                                                                <form action="{{ url('/applicant/deleteUtme') }}" method="POST">
                                                                                    @csrf
                                                                                    <input name="utme_id" type="hidden" value="{{$utme->id}}">
            
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
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                        <h5 class="card-title mb-3">Upload UTME Results Printout:</h5>
                                        @if($applicant->utmes->count() > 3 && empty($applicant->status))
                                            <form action="{{ url('applicant/uploadUtme') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row ">
        
                                                    <div class="col-md-12 mt-3">
                                                        <label for="utme">Upload UTME</label>
                                                        <input type="file" name="utme" class="form-control" id="utme">
                                                    </div><!--end col-->
        
                                                    <div class="col-auto">
                                                        <br>
                                                        <button type="submit" class="btn btn-primary">Upload</button>
                                                    </div><!--end col-->
                                                </div>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--end tab-pane-->
                    <div class="tab-pane" id="deresult" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-4 pb-2">
                                    @if(empty($applicant->de_result))
                                    <form action="{{ url('applicant/saveDe') }}" method="POST" enctype="multipart/form-data">
                                        @csrf

                                        <div class="col-md-12">
                                            <label for="de_school_attended">Direct Entry Institution</label>
                                            <textarea id="de_school_attended" name="de_school_attended" >{!! $applicant->de_school_attended !!}</textarea>
                                        </div><!--end col-->
            
                                        <div class="col-md-12 mt-3">
                                            <label for="de_result">Upload Direct Entry Result/Certificate</label>
                                            <input type="file" name="de_result" class="form-control" id="de_result">
                                        </div><!--end col-->
                                        <div class="row g-2">
                                            @if(empty($applicant->status))
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-success">Save</button>
                                                </div>
                                            </div>
                                            @endif
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="tab-pane fade" id="guardian" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Guardian Information</h5>
                                <hr>
                                <form action="{{ url('applicant/guardianBioData') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="guardian_id" value="{{ empty($applicant->guardian)?'':$applicant->guardian->id }}">
                                    <input type="hidden" name="user_id" value="{{ $applicant->id }}">

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="name" name="name" value="{{ empty($applicant->guardian)?'':$applicant->guardian->name }}" required>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="text" class="form-control" id="email" name="email" value="{{ empty($applicant->guardian)?'':$applicant->guardian->email }}" required>
                                            </div>
                                        </div>
                                        <!--end col-->

                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="phone_number" class="form-label">Phone Number</label>
                                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ empty($applicant->guardian)?'':$applicant->guardian->phone_number }}"  required>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <label for="address">Address</label>
                                            <textarea id="address" name="address" >{!! empty($applicant->guardian)?'':$applicant->guardian->address !!}</textarea>
                                        </div><!--end col-->

                                        <hr>
                                        @if(empty($applicant->status))
                                        <div class="col-lg-12">
                                            <div class="hstack gap-2 justify-content-end">
                                                <button type="submit" class="btn btn-primary">Save Guardian Information</button>
                                            </div>
                                        </div>
                                        @endif
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="nok" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Next of Kin Information</h5>
                                <hr>
                                <form action="{{ url('applicant/nokBioData') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="nextOfKin_id" value="{{ empty($applicant->nok)?'':$applicant->nok->id }}">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ empty($applicant->nok)?'':$applicant->nok->name }}" required>
                                                </div>    
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="text" class="form-control" id="email" name="email" value="{{ empty($applicant->nok)?'':$applicant->nok->email }}" required>
                                                </div>
                                            </div>
                                            <!--end col-->

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="phone_number" class="form-label">Phone Number</label>
                                                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ empty($applicant->nok)?'':$applicant->nok->phone_number }}" required>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="address">Address</label>
                                                    <textarea id="address" name="address" >{!! empty($applicant->nok)?'':$applicant->nok->address !!}</textarea>
                                                </div>
                                            </div><!--end col-->

                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="relationship" class="form-label">Relationship</label>
                                                    <input type="text" class="form-control" id="relationship" name="relationship" value="{{ empty($applicant->nok)?'':$applicant->nok->relationship }}"  required>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        @if(empty($applicant->status))
                                        <div class="col-lg-12">
                                            <div class="hstack gap-2 justify-content-end">
                                                <button type="submit" class="btn btn-primary">Save Next of Kin Information</button>
                                            </div>
                                        </div>
                                        @endif
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end tab-content-->
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->

</div><!-- container-fluid -->
@endsection