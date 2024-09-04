@php
    $formSession =  session('previous_section');
    $student = Auth::guard('student')->user();
    $applicant = $student->applicant;

    $percent = 1;
        $total = 6;
        if($applicant->application_type == 'UTME'){
            $total = 8;
        }

        if($applicant->application_type != 'UTME'){
            $total = 7;
        }

        if(!empty($applicant->lastname)){
            $percent = $percent + 1;
        }
        if(!empty($applicant->programme)){
            $percent = $percent + 1;
        }
        if(!empty($applicant->guardian)){
            $percent = $percent + 1;
        }
        if(count($applicant->olevels) > 4 && $applicant->sitting_no != 0){
            $percent = $percent + 1;
        }
        if(!empty($applicant->olevel_1)){
            $percent = $percent + 1;
        }
        if(!empty($applicant->application_type) && $applicant->application_type == 'UTME'){
            if(count($applicant->utmes) > 3){
                $percent = $percent + 1;
            }
            if(!empty($applicant->utme)){
                $percent = $percent + 1;
            }
        }elseif(!empty($applicant->application_type) && $applicant->application_type != 'UTME'){
            if(!empty($applicant->de_result)){
                $percent = $percent + 1;
            }
        }

        if(!empty($applicant->nok)){
            $percent = $percent + 1;
            $total = $total + 1;
        }

        $percent = round(($percent/$total)*100);
@endphp
@extends('student.layout.dashboard')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Screening</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Screening</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="container-fluid mt-5">
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img src="{{ asset('assets/images/profile-bg.jpg') }}" alt="" class="profile-wid-img" />
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
                            <i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i> {{ $applicant->jamb_reg_no }}<br>
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
                            <a class="nav-link fs-14 {{ $formSession == 'bio-data'?'active':null }} {{ $formSession == ''?'active':null }}" data-bs-toggle="tab" href="#overview-tab" role="tab">
                                <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Overview</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'olevel'?'active':null }}" data-bs-toggle="tab" href="#olevel" role="tab">
                                <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Olevel Result</span>
                            </a>
                        </li>

                        @if(!empty($applicant->application_type) && $applicant->application_type == 'UTME')
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'utme'?'active':null }}" data-bs-toggle="tab" href="#jamb" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Jamb Result</span>
                            </a>
                        </li>
                        @elseif(!empty($applicant->application_type) && $applicant->application_type != 'UTME')
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'de'?'active':null }}" data-bs-toggle="tab" href="#deresult" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Direct Entry/Previous Institution Result</span>
                            </a>
                        </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'guardian'?'active':null }}" data-bs-toggle="tab" href="#guardian" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Guardian</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'nok'?'active':null }}" data-bs-toggle="tab" href="#nok" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Next of Kin</span>
                            </a>
                        </li>


                    </ul>
                   
                </div>
                <!-- Tab panes -->
                <div class="tab-content pt-4 text-muted">
                    <div class="tab-pane {{ $formSession == 'bio-data'?'active':null }} {{ $formSession == ''?'active':null }}" id="overview-tab" role="tabpanel">
                        <div class="row">
                            @include('student.layout.clearanceProgress')

                            <!--end col-->
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Bio-Data</h5>
                                        <form action="{{ url('student/saveBioData') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-12 text-center">
                                                    <div class="profile-user position-relative d-inline-block mx-auto mb-2">
                                                        @if(empty($applicant->image))
                                                        <img src="{{asset('assets/images/users/user-dummy-img.jpg')}}" class="rounded-circle avatar-lg img-thumbnail user-profile-image" alt="user-profile-image">
                                                        @else
                                                        <img src="{{asset($applicant->image)}}" class="rounded-circle avatar-lg img-thumbnail user-profile-image" alt="user-profile-image">
                                                        @endif
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
                                                        <input type="date" class="form-control"  id="dob" name="dob" value="{{ isset($applicant->dob) ? substr($applicant->dob, 0, 10) : '' }}" required />
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
                                                    <textarea class="ckeditor" id="address" name="address" >{!! $applicant->address !!}</textarea>
                                                </div><!--end col-->
    
                                                <hr>
                                                @if(empty($student->clearance_status))
                                                <div class="col-lg-12">
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <button type="submit" id="submit-button" class="btn btn-primary">Save</button>
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


                    <!--end tab-pane-->
                    <div class="tab-pane {{ $formSession == 'olevel'?'active':null }}" id="olevel" role="tabpanel">
                        <div class="row">
                            @include('student.layout.clearanceProgress')
    
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">O'Level Results:</h5>
                                            <hr>
                                            @if(empty($applicant->sitting_no))
                                                <form action="{{ url('student/saveSitting') }}" method="POST" enctype="multipart/form-data">
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
                                                            <textarea class="ckeditor" id="schools_attended" name="schools_attended" >{!! $applicant->schools_attended !!}</textarea>
                                                        </div><!--end col-->
    
                                                        <div class="col-auto">
                                                            <br>
                                                            <button type="submit" id="submit-button" class="btn btn-primary">Save</button>
                                                        </div><!--end col-->
                                                    </div>
                                                </form>
                                            @else
                                                @if(empty($student->clearance_status))
                                                    @if($applicant->olevels->count() < 9)
                                                    <form action="{{ url('student/addOlevel') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div id="subjects-container">
                                                            <!-- Fixed Subject: English Language -->
                                                            <div class="row gx-3 gy-2 align-items-center subject-entry">
                                                                <div class="col-sm-3">
                                                                    <label for="subject_0">Subject</label>
                                                                    <input type="text" class="form-control" name="subjects[0][subject]" id="subject_0" value="English Language" readonly required>
                                                                </div><!--end col-->
                                                    
                                                                <div class="col-sm-2">
                                                                    <label for="grade_0">Grade</label>
                                                                    <select class="form-select" name="subjects[0][grade]" id="grade_0" required>
                                                                        <option selected>Choose...</option>
                                                                        <option value="A/R">A/R</option>
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
                                                                    <label for="year_0">Year</label>
                                                                    <input type="number" min="2010" max="2099" step="1" name="subjects[0][year]" class="form-control" id="year_0" required>
                                                                </div><!--end col-->
                                                    
                                                                <div class="col-sm-3">
                                                                    <label for="reg_no_0">Registration Number</label>
                                                                    <input type="text" name="subjects[0][reg_no]" class="form-control" id="reg_no_0" required>
                                                                </div><!--end col-->
                                                            </div><!--end row-->
                                                            <hr>
                                                            <!-- Fixed Subject: General Mathematics -->
                                                            <div class="row gx-3 gy-2 align-items-center subject-entry">
                                                                <div class="col-sm-3">
                                                                    <label for="subject_1">Subject</label>
                                                                    <input type="text" class="form-control" name="subjects[1][subject]" id="subject_1" value="General Mathematics" readonly required>
                                                                </div><!--end col-->
                                                    
                                                                <div class="col-sm-2">
                                                                    <label for="grade_1">Grade</label>
                                                                    <select class="form-select" name="subjects[1][grade]" id="grade_1" required>
                                                                        <option selected>Choose...</option>
                                                                        <option value="A/R">A/R</option>
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
                                                                    <label for="year_1">Year</label>
                                                                    <input type="number" min="2010" max="2099" step="1" name="subjects[1][year]" class="form-control" id="year_1" required>
                                                                </div><!--end col-->
                                                    
                                                                <div class="col-sm-3">
                                                                    <label for="reg_no_1">Registration Number</label>
                                                                    <input type="text" name="subjects[1][reg_no]" class="form-control" id="reg_no_1" required>
                                                                </div><!--end col-->
                                                            </div><!--end row-->
                                                            <hr>
                                                            <!-- Editable Subjects -->
                                                            @for ($i = 2; $i < 9; $i++)
                                                            <div class="row gx-3 gy-2 align-items-center subject-entry">
                                                                <div class="col-sm-3">
                                                                    <label for="subject_{{ $i }}">Subject</label>
                                                                    <select class="form-select" name="subjects[{{ $i }}][subject]" id="subject_{{ $i }}" >
                                                                        <option value="">Choose...</option>
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
                                                                        <option value="Data Processing">Data Processing</option>
                                                                        <option value="Dyeing & Bleaching">Dyeing & Bleaching</option>
                                                                        <option value="Economics">Economics</option>
                                                                        <option value="Financial Accounting">Financial Accounting</option>
                                                                        <option value="Fisheries">Fisheries</option>
                                                                        <option value="Food & Nutrition">Food & Nutrition</option>
                                                                        <option value="Further Mathematics">Further Mathematics</option>
                                                                        <option value="Garment Making">Garment Making</option>
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
                                                                        <option value="Technical Drawing">Technical Drawing</option>
                                                                        <option value="Tourism">Tourism</option>
                                                                        <option value="Type Writing">Type Writing</option>
                                                                        <option value="Visual Art">Visual Art</option>
                                                                        <option value="Yoruba">Yoruba</option>
                                                                    </select>
                                                                </div><!--end col-->
                                                    
                                                                <div class="col-sm-2">
                                                                    <label for="grade_{{ $i }}">Grade</label>
                                                                    <select class="form-select" name="subjects[{{ $i }}][grade]" id="grade_{{ $i }}" >
                                                                        <option value="">Choose...</option>
                                                                        <option value="A/R">A/R</option>
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
                                                                    <label for="year_{{ $i }}">Year</label>
                                                                    <input type="number" min="2010" max="2099" step="1" name="subjects[{{ $i }}][year]" class="form-control" id="year_{{ $i }}" >
                                                                </div><!--end col-->
                                                    
                                                                <div class="col-sm-3">
                                                                    <label for="reg_no_{{ $i }}">Registration Number</label>
                                                                    <input type="text" name="subjects[{{ $i }}][reg_no]" class="form-control" id="reg_no_{{ $i }}" >
                                                                </div><!--end col-->
                                                            </div><!--end row-->
                                                            <hr>
                                                            @endfor
                                                        </div>
                                                        
                                                        <div class="col-auto mt-3">
                                                            <button type="submit" id="submit-button" class="btn btn-primary">Save</button>
                                                        </div><!--end col-->
                                                    </form>
                                                    @endif
                                                @endif
                                            @endif
                                            <hr>
                                            @if(!empty($applicant->olevel_1))
                                            <div class="row mb-2">
                                                <div class="col-sm-6 col-xl-12">
                                                    <!-- Simple card -->
                                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">Olevel Result</span>
                                                    <div class="text-end">
                                                        <form action="{{ url('student/deleteFile') }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="file_type" value="olevel_1">
                                                            <button type="submit" id="submit-button" class="btn btn-danger">Delete Result</button>
                                                            <a href="{{ asset($applicant->olevel_1) }}" target="blank" class="btn btn-success">View</a>
                                                        </form>
                                                    </div>
                                                    @if($applicant->sitting_no > 1)
                                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">Olevel Result (Second Sitting)</span>
                                                    <div class="text-end">
                                                        <form action="{{ url('student/deleteFile') }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="file_type" value="olevel_2">
                                                            <button type="submit" id="submit-button" class="btn btn-danger">Delete Result</button>
                                                            <a href="{{ asset($applicant->olevel_2) }}" target="blank"  class="btn btn-success">View</a>
                                                        </form>
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
                                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editScore{{$olevel->id}}" class="link-primary"><i class="ri-edit-fill"></i></a>
                                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteGrade{{$olevel->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>

                                                                <div id="editScore{{$olevel->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header p-3">
                                                                                <h4 class="card-title mb-0">Edit Score</h4>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                <hr>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form class="mt-2"  action="{{ url('student/updateOlevel') }}" method="POST">
                                                                                    @csrf

                                                                                    <input name="olevel_id" type="hidden" value="{{$olevel->id}}">
                                                                                    <div class="row">
                                                                                        <div class="mb-3">
                                                                                            <label for="subject">Subject</label>
                                                                                            <select class="form-select" name="subject" id="subject" >
                                                                                                <option {{ $olevel->subject == "Agricultural Science" ? "selected":"" }} value="Agricultural Science">Agricultural Science</option>
                                                                                                <option {{ $olevel->subject == "Animal Husbandry" ? "selected":"" }} value="Animal Husbandry">Animal Husbandry</option>
                                                                                                <option {{ $olevel->subject == "Automobile Parts Merchandising" ? "selected":"" }} value="Automobile Parts Merchandising">Automobile Parts Merchandising</option>
                                                                                                <option {{ $olevel->subject == "Biology" ? "selected":"" }} value="Biology">Biology</option>
                                                                                                <option {{ $olevel->subject == "Book Keeping" ? "selected":"" }} value="Book Keeping">Book Keeping</option>
                                                                                                <option {{ $olevel->subject == "Catering Craft Practice" ? "selected":"" }} value="Catering Craft Practice">Catering Craft Practice</option>
                                                                                                <option {{ $olevel->subject == "Chemistry" ? "selected":"" }} value="Chemistry">Chemistry</option>
                                                                                                <option {{ $olevel->subject == "Christian Studies" ? "selected":"" }} value="Christian Studies">Christian Studies</option>
                                                                                                <option {{ $olevel->subject == "Civic Education" ? "selected":"" }} value="Civic Education">Civic Education</option>
                                                                                                <option {{ $olevel->subject == "Clothing & Textile" ? "selected":"" }} value="Clothing & Textile">Clothing & Textile</option>
                                                                                                <option {{ $olevel->subject == "Commerce" ? "selected":"" }} value="Commerce">Commerce</option>
                                                                                                <option {{ $olevel->subject == "Computer & IT" ? "selected":"" }} value="Computer & IT">Computer & IT</option>
                                                                                                <option {{ $olevel->subject == "Cosmetology" ? "selected":"" }} value="Cosmetology">Cosmetology</option>
                                                                                                <option {{ $olevel->subject == "Data Processing" ? "selected" : "" }} value="Data Processing">Data Processing</option>
                                                                                                <option {{ $olevel->subject == "Dyeing & Bleaching" ? "selected":"" }} value="Dyeing & Bleaching">Dyeing & Bleaching</option>
                                                                                                <option {{ $olevel->subject == "Economics" ? "selected":"" }} value="Economics">Economics</option>
                                                                                                <option {{ $olevel->subject == "Financial Accounting" ? "selected":"" }} value="Financial Accounting">Financial Accounting</option>
                                                                                                <option {{ $olevel->subject == "Fisheries" ? "selected":"" }} value="Fisheries">Fisheries</option>
                                                                                                <option {{ $olevel->subject == "Food & Nutrition" ? "selected":"" }} value="Food & Nutrition">Food & Nutrition</option>
                                                                                                <option {{ $olevel->subject == "Further Mathematics" ? "selected":"" }} value="Further Mathematics">Further Mathematics</option>
                                                                                                <option {{ $olevel->subject == "Garment Making" ? "selected":"" }} value="Garment Making">Garment Making</option>
                                                                                                <option {{ $olevel->subject == "General Mathematics" ? "selected":"" }} value="General Mathematics">General Mathematics</option>
                                                                                                <option {{ $olevel->subject == "Geography" ? "selected":"" }} value="Geography">Geography</option>
                                                                                                <option {{ $olevel->subject == "Government" ? "selected":"" }} value="Government">Government</option>
                                                                                                <option {{ $olevel->subject == "Hausa" ? "selected":"" }} value="Hausa">Hausa</option>
                                                                                                <option {{ $olevel->subject == "Health Education" ? "selected":"" }} value="Health Education">Health Education</option>
                                                                                                <option {{ $olevel->subject == "History" ? "selected":"" }} value="History">History</option>
                                                                                                <option {{ $olevel->subject == "Home Management" ? "selected":"" }} value="Home Management">Home Management</option>
                                                                                                <option {{ $olevel->subject == "Igbo" ? "selected":"" }} value="Igbo">Igbo</option>
                                                                                                <option {{ $olevel->subject == "Insurance" ? "selected":"" }} value="Insurance">Insurance</option>
                                                                                                <option {{ $olevel->subject == "Islamic Studies" ? "selected":"" }} value="Islamic Studies">Islamic Studies</option>
                                                                                                <option {{ $olevel->subject == "Literature in English" ? "selected":"" }} value="Literature in English">Literature in English</option>
                                                                                                <option {{ $olevel->subject == "Marketing" ? "selected":"" }} value="Marketing">Marketing</option>
                                                                                                <option {{ $olevel->subject == "Music" ? "selected":"" }} value="Music">Music</option>
                                                                                                <option {{ $olevel->subject == "Office Practice" ? "selected":"" }} value="Office Practice">Office Practice</option>
                                                                                                <option {{ $olevel->subject == "Painting & Decorating" ? "selected":"" }} value="Painting & Decorating">Painting & Decorating</option>
                                                                                                <option {{ $olevel->subject == "Photography" ? "selected":"" }} value="Photography">Photography</option>
                                                                                                <option {{ $olevel->subject == "Physical Education" ? "selected":"" }} value="Physical Education">Physical Education</option>
                                                                                                <option {{ $olevel->subject == "Physics" ? "selected":"" }} value="Physics">Physics</option>
                                                                                                <option {{ $olevel->subject == "Salesmanship" ? "selected":"" }} value="Salesmanship">Salesmanship</option>
                                                                                                <option {{ $olevel->subject == "Stenography" ? "selected":"" }} value="Stenography">Stenography</option>
                                                                                                <option {{ $olevel->subject == "Store Keeping" ? "selected":"" }} value="Store Keeping">Store Keeping</option>
                                                                                                <option {{ $olevel->subject == "Store Management" ? "selected":"" }} value="Store Management">Store Management</option>
                                                                                                <option {{ $olevel->subject == "Technical Drawing" ? "selected":"" }} value="Technical Drawing">Technical Drawing</option>
                                                                                                <option {{ $olevel->subject == "Tourism" ? "selected":"" }} value="Tourism">Tourism</option>
                                                                                                <option {{ $olevel->subject == "Type Writing" ? "selected":"" }} value="Type Writing">Type Writing</option>
                                                                                                <option {{ $olevel->subject == "Visual Art" ? "selected":"" }} value="Visual Art">Visual Art</option>
                                                                                                <option {{ $olevel->subject == "Yoruba" ? "selected":"" }} value="Yoruba">Yoruba</option>
                                                                                            </select>
                                                                                        </div><!--end col-->
                                                                                        
                                                                                        <div class="mb-3">
                                                                                            <label for="grade">Grade</label>
                                                                                            <select class="form-select" name="grade" id="grade" >
                                                                                                <option {{ $olevel->grade == "A/R" ? "selected":"" }} value="A/R">A/R</option>
                                                                                                <option {{ $olevel->grade == "A1" ? "selected":"" }} value="A1">A1</option>
                                                                                                <option {{ $olevel->grade == "B2" ? "selected":"" }} value="B2">B2</option>
                                                                                                <option {{ $olevel->grade == "B3" ? "selected":"" }} value="B3">B3</option>
                                                                                                <option {{ $olevel->grade == "C4" ? "selected":"" }} value="C4">C4</option>
                                                                                                <option {{ $olevel->grade == "C5" ? "selected":"" }} value="C5">C5</option>
                                                                                                <option {{ $olevel->grade == "C6" ? "selected":"" }} value="C6">C6</option>
                                                                                                <option {{ $olevel->grade == "D7" ? "selected":"" }} value="D7">D7</option>
                                                                                                <option {{ $olevel->grade == "E8" ? "selected":"" }} value="E8">E8</option>
                                                                                                <option {{ $olevel->grade == "F9" ? "selected":"" }} value="F9">F9</option>
                                                                                            </select>
                                                                                        </div><!--end col-->
                                    
                                                                                        <div class="mb-3">
                                                                                            <label for="year">Year</label>
                                                                                            <input type="number" min="2010" max="2099" step="1" value="{{ $olevel->year }}" name="year" class="form-control" id="year" >
                                                                                        </div><!--end col-->
                                    
                                                                                        <div class="mb-3">
                                                                                            <label for="reg_no">Registration Number</label>
                                                                                            <input type="text" name="reg_no" class="form-control" value="{{ $olevel->reg_no }}" id="reg_no" >
                                                                                        </div><!--end col-->
                                                                                    </div><!--end row-->

                                                                                    <div class="modal-footer bg-light pt-3 justify-content-center">
                                                                                        <div class="col-auto mt-3">
                                                                                            <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                                                                                        </div><!--end col-->
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div><!-- /.modal-content -->
                                                                    </div><!-- /.modal-dialog -->
                                                                </div><!-- /.modal -->

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
                                                                                    <form action="{{ url('/student/deleteOlevel') }}" method="POST">
                                                                                        @csrf
                                                                                        <input name="olevel_id" type="hidden" value="{{$olevel->id}}">
                
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
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @if($applicant->olevels->count() > 4 && empty($student->clearance_status))
                                            <hr>
                                            <h5 class="card-title mb-3">Upload O'Level Results Printout:</h5>
                                                <form action="{{ url('student/uploadOlevel') }}" method="POST" enctype="multipart/form-data">
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
                                                            <button type="submit" id="submit-button" class="btn btn-primary">Upload</button>
                                                        </div><!--end col-->
                                                    </div>
                                                </form>
                                            @endif
                                    </div>
                                    <!--end card-body-->
                                </div>
                                <!--end card-->
                            </div>
                        </div>
                    </div>

                    <!--end tab-pane-->
                    <div class="tab-pane {{ $formSession == 'utme'?'active':null }}" id="jamb" role="tabpanel">
                        <div class="row">
                            @include('student.layout.clearanceProgress')
    
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-4 pb-2">
                                            <h5 class="card-title mb-3">UTME Results:</h5>
                                            <hr>
                                            @if(empty($applicant->jamb_reg_no))
                                                <form action="{{ url('student/saveUtme') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="col-lg-12">
                                                        <div class="mb-3">
                                                            <label for="jamb_reg" class="form-label">Jamb Registration Number</label>
                                                            <input type="text" class="form-control" id="jamb_reg" name="jamb_reg_no" value="{{ $applicant->jamb_reg_no }}<br>" required>
                                                        </div>
                                                    </div>
    
                                                    <hr>
                                                    <div class="row g-2">
                                                        @if(empty($student->clearance_status))
                                                        <div class="col-lg-12">
                                                            <div class="text-end">
                                                                <button type="submit" id="submit-button" class="btn btn-success">Save</button>
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
                                                            <form action="{{ url('student/deleteFile') }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <input type="hidden" name="file_type" value="utme">
                                                                <button type="submit" id="submit-button" class="btn btn-danger">Delete UTME Printout</button>
                                                                <a href="{{ asset($applicant->utme) }}"  target="blank" class="btn btn-success">View</a>
                                                            </form>
                                                        </div>
                                                    </div><!-- end col -->
                                                </div>
                                                @endif
                                                <hr>
                                                @if(empty($student->clearance_status))
                                                    @if($applicant->utmes->count() < 1)
                                                        <form action="{{ url('student/addUtme') }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div id="subjects-container">
                                                                <!-- Fixed Subject: Use of English -->
                                                                <div class="row gx-3 gy-2 align-items-center subject-entry">
                                                                    <div class="col-sm-6">
                                                                        <label for="subject_0">Subject</label>
                                                                        <input type="text" class="form-control" name="subjects[0][subject]" id="subject_0" value="Use of English" readonly required>
                                                                    </div><!--end col-->
                                                        
                                                                    <div class="col-sm-6">
                                                                        <label for="score_0">Score</label>
                                                                        <input type="number" min="0" max="100" step="1" name="subjects[0][score]" class="form-control" id="score_0" required>
                                                                    </div><!--end col-->
                                                    
                                                                </div><!--end row-->
                                                                <hr>
                                                                <!-- Editable Subjects -->
                                                                @for ($i = 1; $i < 4; $i++)
                                                                <div class="row gx-3 gy-2 align-items-center subject-entry">
                                                                    <div class="col-sm-6">
                                                                        <label for="subject_{{ $i }}">Subject</label>
                                                                        <select class="form-select" name="subjects[{{ $i }}][subject]" id="subject_{{ $i }}" required>
                                                                            <option value="">Select a subject</option>
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
                                                                            <option value="Data Processing">Data Processing</option>
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
                                                                            <option value="Technical Drawing">Technical Drawing</option>
                                                                            <option value="Tourism">Tourism</option>
                                                                            <option value="Type Writing">Type Writing</option>
                                                                            <option value="Visual Art">Visual Art</option>
                                                                            <option value="Yoruba">Yoruba</option>
                                                                        </select>
                                                                    </div><!--end col-->
                                                        
                                                                    <div class="col-sm-6">
                                                                        <label for="score_{{ $i }}">Score</label>
                                                                        <input type="number" min="0" max="100" step="1" name="subjects[{{ $i }}][score]" class="form-control" id="score_{{ $i }}" required>
                                                                    </div><!--end col-->
                                                                </div><!--end row-->
                                                                <hr>
                                                                @endfor
                                                            </div>
                                                            
                                                            <div class="col-auto mt-3">
                                                                <button type="submit" id="submit-button" class="btn btn-primary">Save</button>
                                                            </div><!--end col-->
                                                        </form> 
                                                    @endif                                               
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
                                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editScore{{$utme->id}}" class="link-primary"><i class="ri-edit-fill"></i></a>
                                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteScore{{$utme->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>

                                                                    <div id="editScore{{$utme->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                                        <div class="modal-dialog modal-dialog-centered">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header p-3">
                                                                                    <h4 class="card-title mb-0">Edit Score</h4>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                    <hr>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <form class="mt-2"  action="{{ url('student/updateUtme') }}" method="POST">
                                                                                        @csrf

                                                                                        <input type="hidden" name="utme_id" value="{{ $utme->id }}">
                                                                                        <div class="row">
                                                                                            <div class="mb-3">
                                                                                                <label for="subject">Subject</label>
                                                                                                <select class="form-select" name="subject" id="subject" required>
                                                                                                    <option {{ $utme->subject == "Agricultural Science" ? "selected":"" }} value="Agricultural Science">Agricultural Science</option>
                                                                                                    <option {{ $utme->subject == "Animal Husbandry" ? "selected":"" }} value="Animal Husbandry">Animal Husbandry</option>
                                                                                                    <option {{ $utme->subject == "Automobile Parts Merchandising" ? "selected":"" }} value="Automobile Parts Merchandising">Automobile Parts Merchandising</option>
                                                                                                    <option {{ $utme->subject == "Biology" ? "selected":"" }} value="Biology">Biology</option>
                                                                                                    <option {{ $utme->subject == "Book Keeping" ? "selected":"" }} value="Book Keeping">Book Keeping</option>
                                                                                                    <option {{ $utme->subject == "Catering Craft Practice" ? "selected":"" }} value="Catering Craft Practice">Catering Craft Practice</option>
                                                                                                    <option {{ $utme->subject == "Chemistry" ? "selected":"" }} value="Chemistry">Chemistry</option>
                                                                                                    <option {{ $utme->subject == "Christian Studies" ? "selected":"" }} value="Christian Studies">Christian Studies</option>
                                                                                                    <option {{ $utme->subject == "Civic Education" ? "selected":"" }} value="Civic Education">Civic Education</option>
                                                                                                    <option {{ $utme->subject == "Clothing & Textile" ? "selected":"" }} value="Clothing & Textile">Clothing & Textile</option>
                                                                                                    <option {{ $utme->subject == "Commerce" ? "selected":"" }} value="Commerce">Commerce</option>
                                                                                                    <option {{ $utme->subject == "Computer & IT" ? "selected":"" }} value="Computer & IT">Computer & IT</option>
                                                                                                    <option {{ $utme->subject == "Cosmetology" ? "selected":"" }} value="Cosmetology">Cosmetology</option>
                                                                                                    <option {{ $utme->subject == "Data Processing" ? "selected" : "" }} value="Data Processing">Data Processing</option>
                                                                                                    <option {{ $utme->subject == "Dyeing & Bleaching" ? "selected":"" }} value="Dyeing & Bleaching">Dyeing & Bleaching</option>
                                                                                                    <option {{ $utme->subject == "Economics" ? "selected":"" }} value="Economics">Economics</option>
                                                                                                    <option {{ $utme->subject == "Financial Accounting" ? "selected":"" }} value="Financial Accounting">Financial Accounting</option>
                                                                                                    <option {{ $utme->subject == "Fisheries" ? "selected":"" }} value="Fisheries">Fisheries</option>
                                                                                                    <option {{ $utme->subject == "Food & Nutrition" ? "selected":"" }} value="Food & Nutrition">Food & Nutrition</option>
                                                                                                    <option {{ $utme->subject == "Further Mathematics" ? "selected":"" }} value="Further Mathematics">Further Mathematics</option>
                                                                                                    <option {{ $utme->subject == "Garment Making" ? "selected":"" }} value="Garment Making">Garment Making</option>
                                                                                                    <option {{ $utme->subject == "General Mathematics" ? "selected":"" }} value="General Mathematics">General Mathematics</option>
                                                                                                    <option {{ $utme->subject == "Geography" ? "selected":"" }} value="Geography">Geography</option>
                                                                                                    <option {{ $utme->subject == "Government" ? "selected":"" }} value="Government">Government</option>
                                                                                                    <option {{ $utme->subject == "Hausa" ? "selected":"" }} value="Hausa">Hausa</option>
                                                                                                    <option {{ $utme->subject == "Health Education" ? "selected":"" }} value="Health Education">Health Education</option>
                                                                                                    <option {{ $utme->subject == "History" ? "selected":"" }} value="History">History</option>
                                                                                                    <option {{ $utme->subject == "Home Management" ? "selected":"" }} value="Home Management">Home Management</option>
                                                                                                    <option {{ $utme->subject == "Igbo" ? "selected":"" }} value="Igbo">Igbo</option>
                                                                                                    <option {{ $utme->subject == "Insurance" ? "selected":"" }} value="Insurance">Insurance</option>
                                                                                                    <option {{ $utme->subject == "Islamic Studies" ? "selected":"" }} value="Islamic Studies">Islamic Studies</option>
                                                                                                    <option {{ $utme->subject == "Literature in English" ? "selected":"" }} value="Literature in English">Literature in English</option>
                                                                                                    <option {{ $utme->subject == "Marketing" ? "selected":"" }} value="Marketing">Marketing</option>
                                                                                                    <option {{ $utme->subject == "Music" ? "selected":"" }} value="Music">Music</option>
                                                                                                    <option {{ $utme->subject == "Office Practice" ? "selected":"" }} value="Office Practice">Office Practice</option>
                                                                                                    <option {{ $utme->subject == "Painting & Decorating" ? "selected":"" }} value="Painting & Decorating">Painting & Decorating</option>
                                                                                                    <option {{ $utme->subject == "Photography" ? "selected":"" }} value="Photography">Photography</option>
                                                                                                    <option {{ $utme->subject == "Physical Education" ? "selected":"" }} value="Physical Education">Physical Education</option>
                                                                                                    <option {{ $utme->subject == "Physics" ? "selected":"" }} value="Physics">Physics</option>
                                                                                                    <option {{ $utme->subject == "Salesmanship" ? "selected":"" }} value="Salesmanship">Salesmanship</option>
                                                                                                    <option {{ $utme->subject == "Stenography" ? "selected":"" }} value="Stenography">Stenography</option>
                                                                                                    <option {{ $utme->subject == "Store Keeping" ? "selected":"" }} value="Store Keeping">Store Keeping</option>
                                                                                                    <option {{ $utme->subject == "Store Management" ? "selected":"" }} value="Store Management">Store Management</option>
                                                                                                    <option {{ $utme->subject == "Technical Drawing" ? "selected":"" }} value="Technical Drawing">Technical Drawing</option>
                                                                                                    <option {{ $utme->subject == "Tourism" ? "selected":"" }} value="Tourism">Tourism</option>
                                                                                                    <option {{ $utme->subject == "Type Writing" ? "selected":"" }} value="Type Writing">Type Writing</option>
                                                                                                    <option {{ $utme->subject == "Visual Art" ? "selected":"" }} value="Visual Art">Visual Art</option>
                                                                                                    <option {{ $utme->subject == "Yoruba" ? "selected":"" }} value="Yoruba">Yoruba</option>
                                                                                                </select>
                                                                                            </div><!--end col-->
                                                                                            
                                                                                            <div class="mb-3">
                                                                                                <label for="score">Score</label>
                                                                                                <input type="number" min="0" max="100" step="1" name="score" value="{{ $utme->score }}" class="form-control" id="score" required>
                                                                                            </div><!--end col-->
                                                                                        </div><!--end row-->
                                                                                        <div class="modal-footer bg-light pt-3 justify-content-center">
                                                                                            <div class="col-auto mt-3">
                                                                                                <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                                                                                            </div><!--end col-->
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div><!-- /.modal-content -->
                                                                        </div><!-- /.modal-dialog -->
                                                                    </div><!-- /.modal -->

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
                                                                                        <form action="{{ url('/student/deleteUtme') }}" method="POST">
                                                                                            @csrf
                                                                                            <input name="utme_id" type="hidden" value="{{$utme->id}}">
                    
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
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <hr>
                                                <h5 class="card-title mb-3">Upload UTME Results Printout:</h5>
                                                @if($applicant->utmes->count() > 3 && empty($student->clearance_status))
                                                    <form action="{{ url('student/uploadUtme') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="row ">
                
                                                            <div class="col-md-12 mt-3">
                                                                <label for="utme">Upload UTME</label>
                                                                <input type="file" name="utme" class="form-control" id="utme">
                                                            </div><!--end col-->
                
                                                            <div class="col-auto">
                                                                <br>
                                                                <button type="submit" id="submit-button" class="btn btn-primary">Upload</button>
                                                            </div><!--end col-->
                                                        </div>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--end tab-pane-->
                    <div class="tab-pane {{ $formSession == 'de'?'active':null }}" id="deresult" role="tabpanel">
                        <div class="row">
                            @include('student.layout.clearanceProgress')
                            
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-4 pb-2">
                                            @if(!empty($applicant->de_result))
                                                <div class="row mb-2">
                                                    <div class="col-sm-6 col-xl-12">
                                                        <!-- Simple card -->
                                                        <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">DE Result Printout</span>
                                                        <div class="text-end">
                                                            <form action="{{ url('student/deleteFile') }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <input type="hidden" name="file_type" value="de">
                                                                <button type="submit" id="submit-button" class="btn btn-danger">Delete Result</button>
                                                                <a href="{{ asset($applicant->de_result) }}"  target="blank" class="btn btn-success">View</a>
                                                            </form>
                                                        </div>
                                                    </div><!-- end col -->
                                                </div>
                                                <hr>
                                            @endif
                                            
                                            <form action="{{ url('student/saveDe') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
    
                                                <div class="col-md-12">
                                                    <label for="de_school_attended">Previous Institution(s)</label>
                                                    <textarea class="ckeditor" id="de_school_attended" name="de_school_attended" >{!! $applicant->de_school_attended !!}</textarea>
                                                </div><!--end col-->
                    
                                                <div class="col-md-12 mt-3">
                                                    <label for="de_result">Upload Result/Certificate</label>
                                                    <input type="file" name="de_result" class="form-control" id="de_result">
                                                </div><!--end col-->
                                                <div class="row g-2">
                                                    @if(empty($student->clearance_status))
                                                    <div class="col-lg-12">
                                                        <div class="text-end">
                                                            <button type="submit" id="submit-button" class="btn btn-success">Save</button>
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
                        </div>
                    </div>

                    <div class="tab-pane {{ $formSession == 'guardian'?'active':null }}" id="guardian" role="tabpanel">
                        <div class="row">
                            @include('student.layout.clearanceProgress')
    
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Guardian Information</h5>
                                        <hr>
                                        <form action="{{ url('student/guardianBioData') }}" method="POST" enctype="multipart/form-data">
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
                                                    <textarea class="ckeditor" id="address" name="address" >{!! empty($applicant->guardian)?'':$applicant->guardian->address !!}</textarea>
                                                </div><!--end col-->
    
                                                <hr>
                                                @if(empty($student->clearance_status))
                                                <div class="col-lg-12">
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <button type="submit" id="submit-button" class="btn btn-primary">Save Guardian Information</button>
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
                    </div>

                    <div class="tab-pane {{ $formSession == 'nok'?'active':null }}" id="nok" role="tabpanel">
                        <div class="row">
                            @include('student.layout.clearanceProgress')
    
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Next of Kin Information</h5>
                                        <hr>
                                        <form action="{{ url('student/nokBioData') }}" method="POST" enctype="multipart/form-data">
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
                                                            <textarea class="ckeditor" id="address" name="address" >{!! empty($applicant->nok)?'':$applicant->nok->address !!}</textarea>
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
                                                @if(empty($student->clearance_status))
                                                <div class="col-lg-12">
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <button type="submit" id="submit-button" class="btn btn-primary">Save Next of Kin Information</button>
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