@extends('user.layout.auth')

@section('content')
<div class="col-lg-8">
    @if(empty($applicant))
    <!-- Primary Alert -->
    <div class="alert alert-primary alert-dismissible alert-additional fade show" id="generalAlert" role="alert" style="display: none;">
        <div class="alert-body">
            <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                    <i class="fs-16 align-middle"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="alert-heading">Welcome to Application Portal</h5>
                    <hr>
                    <p class="mb-0">
                        For Admission into <span id="generalProgrammeCategory"></span> Programme 
                        <strong> ({{ !empty($pageGlobalData->sessionSetting) ? $pageGlobalData->sessionSetting->application_session : null }} Academic Session) </strong>
                        You are most welcome to study at {{ env('SCHOOL_NAME') }}. We offer candidates an excellent and stable academic calendar, comfortable hall of residence, sound morals, entrepreneurial training, skill acquisition, serene, and secure environment for learning. 
                        <strong>This application form will cost ₦<span id="generalFeeAmount"></span></strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="alert alert-success alert-dismissible alert-additional fade show" id="interTransferAlert" role="alert" style="display: none;">
        <div class="alert-body">
            <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                    <i class="fs-16 align-middle"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="alert-heading">Welcome to Application Portal</h5>
                    <hr>
                    <p class="mb-0">
                        For Admission into <span id="interTransferProgrammeCategory"></span> Programme 
                        <strong> ({{ !empty($pageGlobalData->sessionSetting) ? $pageGlobalData->sessionSetting->application_session : null }} Academic Session) </strong>
                        You are most welcome to study at {{ env('SCHOOL_NAME') }}. We offer candidates an excellent and stable academic calendar, comfortable hall of residence, sound morals, entrepreneurial training, skill acquisition, serene, and secure environment for learning. 
                        <strong>This application form will cost ₦<span id="interTransferFeeAmount"></span></strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if(!empty($applicant))
        <div class="p-lg-5">
            <div>
                <p class="text-muted">{{ $applicant->lastname.' '.$applicant->othernames }} Application</p>
            </div>

            <div>
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#base-justified-home" role="tab" aria-selected="false">
                            Make Payment
                        </a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content  text-muted">
                    <div class="tab-pane active" id="base-justified-home" role="tabpanel">
                        <div class="p-2 mt-4">
                            <form class="needs-validation" method="POST" novalidate action="{{ url('applicant/register') }}">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $applicant->id }}">
                                <input type="hidden" name="programme_id" value="{{ $applicant->programme_id }}">
                                <input type="hidden" name="applicationType" value="{{ $applicant->application_type }}">
                                <input type="hidden" name="email" value="{{ $applicant->email }}">
                                <input type="hidden" name="lastname" value="{{ $applicant->lastname }}">
                                <input type="hidden" name="othernames" value="{{ $applicant->othernames }}">
                                <input type="hidden" name="phone_number" value="{{ $applicant->phone_number }}">
                                <input type="hidden" name="programme_category_id" id="programmeCategoryId">

                                <div class="mb-3">
                                    <label for="paymentGateway" class="form-label">Select Payment Gateway<span class="text-danger">*</span></label>
                                    <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required onchange="handlePaymentMethodChange(event)">
                                        <option value= "" selected>Select Payment Gateway</option>
                                        @if(env('UPPERLINK_STATUS'))<option value="Upperlink">Upperlink</option>@endif
                                        @if(env('FLUTTERWAVE_STATUS'))<option value="Rave">Flutterwave</option>@endif
                                        @if(env('MONNIFY_STATUS'))<option value="Monnify">Monnify</option>@endif
                                        @if(env('PAYSTACK_STATUS'))<option value="Paystack">Paystack</option>@endif
                                        @if(env('BANK_TRANSFER_STATUS'))<option value="BankTransfer">Transfer</option>@endif
                                    </select>
                                </div>

                                <hr>
                                <!-- Primary Alert -->
                                <div class="alert alert-primary alert-dismissible alert-additional fade show" role="alert" style="display: none" id="transferInfo">
                                    <div class="alert-body">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <i class="fs-16 align-middle"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="alert-heading">Well done !</h5>
                                                <p class="mb-0">Kindly make transfer to the below transaction </p>
                                                <br>
                                                <ul class="list-group">
                                                    <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Name:</strong> {{env('BANK_NAME')}}</li>
                                                    <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Account Number:</strong> {{env('BANK_ACCOUNT_NUMBER')}}</li>
                                                    <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Account Name:</strong> {{env('BANK_ACCOUNT_NAME')}}</li>
                                                </ul>
                                                <br>
                                                <p>Please send proof of payment as an attachment to {{ env('ACCOUNT_EMAIL') }}, including your name, registration number, and purpose of payment. For any inquiries, you can also call {{ env('ACCOUNT_PHONE') }}.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert-content">
                                        <p class="mb-0">NOTE: PLEASE ENSURE TO VERIFY THE TRANSACTION DETAILS PROPERLY. TRANSFER ONLY TO THE ACCOUNT ABOVE. STUDENTS TAKE RESPONSIBILITY FOR ANY MISPLACEMENT OF FUNDS.</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button class="btn btn-success w-100" id='submit-button' disabled type="submit">Make Payment</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end card -->

        </div>
    @else
        <div class="p-lg-5">

            <div>
                <h5>Kindly fill the form below<h5>


                <form class="needs-validation" method="POST" novalidate action="{{ url('applicant/register') }}">
                    @csrf

                    <div class="p-2 mt-4">
                        <div class="mb-3 border-top border-top-dashed pt-3">
                            <label for="programmeCategory" class="form-label">Select Programme Category<span class="text-danger">*</span></label>
                            <select class="form-select" aria-label="programme_category_id" name="programmeCategory" required onchange="handleProgrammeCategoryChange(event)">
                                <option value="" selected>Select Programme Category</option>
                                @if(!empty($advanceStudyProgrammes))
                                    @foreach($advanceStudyProgrammes as $advanceStudyProgramme)
                                        <option value="{{ $advanceStudyProgramme['title'] }}">{{ $advanceStudyProgramme['title'] }} Programme</option>
                                    @endforeach
                                @endif
                                @foreach($programmeCategories as $programmeCategory)
                                    <option value="{{ $programmeCategory->id }}">{{ $programmeCategory->category }} Programme</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Application Type dropdown, hidden by default -->
                    <input type="hidden" name="programme_category_id" id="programmeCategoryId">
                    <input type="hidden" name="applicationType" id="hiddenApplicationType">

                    <!-- Application Type dropdown, hidden by default -->
                    <div id="applicationTypeContainer" class="p-2 mt-4" style="display: none;">
                        <div class="mb-3">
                            <label for="applicationType" class="form-label">Select Application Type<span class="text-danger">*</span></label>
                            <select class="form-select" aria-label="applicationType" name="applicationTypeDropdown" required onchange="handleApplicationTypeChange(event)">
                                <option value="" selected>Select Application Type</option>
                                <option value="General Application">General Application (UTME & DE)</option>
                                <option value="Inter Transfer Application">Inter Transfer Application</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-2 mt-4 border-top border-top-dashed pt-3" style="display: none" id="applicationForm">

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Enter email address" required>
                            <div class="invalid-feedback">
                                Please enter email
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
                            <div class="invalid-feedback">
                                Please enter password
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm-password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation" id="confirm-password" placeholder="Confirm Password" required>
                            <div class="invalid-feedback">
                                Please confirm password
                            </div>
                        </div>
                    
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Lastname(Surname) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Enter lastname " required>
                            <div class="invalid-feedback">
                                Please enter lastname
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="othernames" class="form-label">Othernames <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="othernames" id="othernames" placeholder="Enter othernames" required>
                            <div class="invalid-feedback">
                                Please enter othernames
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" minlength="14" name="phone_number" id="phone_number" placeholder="Enter phone (+23481111111)" required>
                            <div class="invalid-feedback">
                                Please enter phone number
                            </div>
                        </div>


                        <div class="mb-3">
                            <label for="referrer" class="form-label">Referrer Code </label>
                            <input type="text" class="form-control" name="referrer" id="referrer" value="{{ isset($_GET['ref']) ? $_GET["ref"] : null  }}" required>
                            <div class="invalid-feedback">
                                Please enter referrer code
                            </div>
                        </div>

                        {{-- <div class="alert alert-primary alert-dismissible alert-additional fade show" role="alert" style="display: none" id="paymentInfo">
                            <div class="alert-content">
                                <p id="amount" class="mb-0"></p>
                            </div>
                        </div> --}}


                        <div class="mb-3">
                            <label for="paymentGateway" class="form-label">Select Payment Gateway<span class="text-danger">*</span></label>
                            <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required onchange="handlePaymentMethodChange(event)">
                                <option value= "" selected>Select Payment Gateway</option>
                                @if(env('UPPERLINK_STATUS'))<option value="Upperlink">Upperlink</option>@endif
                                @if(env('FLUTTERWAVE_STATUS'))<option value="Rave">Flutterwave</option>@endif
                                @if(env('MONNIFY_STATUS'))<option value="Monnify">Monnify</option>@endif
                                @if(env('PAYSTACK_STATUS'))<option value="Paystack">Paystack</option>@endif
                                @if(env('BANK_TRANSFER_STATUS'))<option value="BankTransfer">Transfer</option>@endif
                                @if(env('WALLET_STATUS'))<option value="Wallet">Wallet</option>@endif
                            </select>
                        </div>

                        <!-- Primary Alert -->
                        <div class="alert alert-primary alert-dismissible alert-additional fade show" role="alert" style="display: none" id="transferInfo">
                            <div class="alert-body">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="fs-16 align-middle"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="alert-heading">Well done !</h5>
                                        <p class="mb-0">Kindly make transfer to the below transaction </p>
                                        <br>
                                        <ul class="list-group">
                                            <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Name:</strong> {{env('BANK_NAME')}}</li>
                                            <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Account Number:</strong> {{env('BANK_ACCOUNT_NUMBER')}}</li>
                                            <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Account Name:</strong> {{env('BANK_ACCOUNT_NAME')}}</li>
                                        </ul>
                                        <br>
                                        <p>Please send proof of payment as an attachment to {{ env('ACCOUNT_EMAIL') }}, including your name, registration number, and purpose of payment. For any inquiries, you can also call {{ env('ACCOUNT_PHONE') }}.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="alert-content">
                                <p class="mb-0">NOTE: PLEASE ENSURE TO VERIFY THE TRANSACTION DETAILS PROPERLY. TRANSFER ONLY TO THE ACCOUNT ABOVE. STUDENTS TAKE RESPONSIBILITY FOR ANY MISPLACEMENT OF FUNDS.</p>
                            </div>
                        </div>

                        <div class="mt-4 border-top border-top-dashed pt-3">
                            <button class="btn btn-success w-100" id='submit-button' disabled type="submit">Make Payment</button>
                        </div>
                    </div>

                    <div class="mt-5 text-center">
                        <p class="mb-0">Already paid for application ? <a href="{{url('/applicant/login')}}" class="fw-semibold text-primary text-decoration-underline"> Sign in to complete application</a> </p>
                    </div>
                </form>
            </div>
            <!-- end card -->

        </div>
    @endif
</div>

<script>
    function handlePaymentMethodChange(event) {
        const selectedPaymentMethod = event.target.value;
        console.log(selectedPaymentMethod);
        const submitButton = document.getElementById('submit-button');
        if(selectedPaymentMethod != ''){
            if(selectedPaymentMethod == 'Remita' || selectedPaymentMethod == 'Zenith') {
                submitButton.disabled = true;
            }else{
                submitButton.disabled = false;
            }
        }else{
            submitButton.disabled = true;
        }
    }


    function handleProgrammeCategoryChange(event) {
        const selectedValue = event.target.value;
        const advancedStudiesUrl = "{{ env('ADVANCED_STUDIES_URL') }}";
        const applicationTypeContainer = document.getElementById('applicationTypeContainer');
        const hiddenApplicationTypeInput = document.getElementById('hiddenApplicationType');
        const applicationForm = document.getElementById("applicationForm");

        // Update hidden inputs with the selected programme category ID
        document.querySelectorAll('input[name="programme_category_id"]').forEach(input => {
            input.value = selectedValue;
        });

        // Redirect for specific program categories
        if (["IJMB", "Preliminary Studies"].includes(selectedValue)) {
            window.location.href = `${advancedStudiesUrl}?programme=${selectedValue}`;
            return;
        }

        // Post request to get the programme category details
        axios.post(`/getProgrammeCategory`, { programme_category_id: selectedValue })
            .then(response => {
                const data = response.data;

                if (data.status === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Application Closed',
                        text: `Application is closed for ${data.category} programme.`,
                    });
                    applicationTypeContainer.style.display = 'none';
                    applicationForm.style.display = "none";
                    hiddenApplicationTypeInput.value = '';
                } else {
                    // Toggle Application Type dropdown and handle hidden input
                    if (data.category === 'Undergraduate') {
                        applicationTypeContainer.style.display = 'block';
                        applicationForm.style.display = "none";
                        hiddenApplicationTypeInput.value = '';
                    } else {
                        applicationTypeContainer.style.display = 'none';
                        applicationForm.style.display = "block";
                        hiddenApplicationTypeInput.value = 'General Application';

                        getPaymentDetails('General Application', selectedValue);
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching programme category:', error);
            });
    }

    function handleApplicationTypeChange(event) {
        const applicationType = event.target.value;
        const programmeCategoryId = document.querySelector('select[name="programmeCategory"]').value;

        getPaymentDetails(applicationType, programmeCategoryId);
    }

    function getPaymentDetails(applicationType, programmeCategoryId) {

        axios.post(`/getPayments`, {
            programme_category_id: programmeCategoryId,
            applicationType: applicationType 
        })
        .then(response => {
            const data = response.data;

            if (applicationType === "Inter Transfer Application" && data.interApplicationPayment) {
                // Calculate and display Inter Transfer Application fee
                const interTransferAmount = data.interApplicationPayment.structures.reduce((total, structure) => total + parseInt(structure.amount), 0) / 100;
                document.getElementById("interTransferFeeAmount").textContent = interTransferAmount.toLocaleString();
                document.getElementById("interTransferProgrammeCategory").textContent = data.interApplicationPayment.programmeCategory.category;

                // Display the Inter Transfer alert, hide the General alert
                generalAlert.style.display = "none";
                interTransferAlert.style.display = "block";
                applicationForm.style.display = "block";
            } else if (applicationType === "General Application" && data.payment) {
                // Calculate and display General Application fee
                const generalAmount = data.payment.structures.reduce((total, structure) => total + parseInt(structure.amount), 0) / 100;
                document.getElementById("generalFeeAmount").textContent = generalAmount.toLocaleString();
                document.getElementById("generalProgrammeCategory").textContent = data.payment.programmeCategory.category;

                // Display the General alert, hide the Inter Transfer alert
                generalAlert.style.display = "block";
                interTransferAlert.style.display = "none";
                applicationForm.style.display = "block";
            } else {
                // Hide both alerts if no valid payment information is found
                generalAlert.style.display = "none";
                interTransferAlert.style.display = "none";
                applicationForm.style.display = "none";

                // Display a notice if it's an unexpected category without payment info
                Swal.fire({
                    icon: 'info',
                    title: 'Notice',
                    text: 'Payment information is unavailable for this category.',
                });
            }
        })
        .catch(error => {
            console.error('Error fetching payment details:', error);
        });
    }
</script>
@endsection
