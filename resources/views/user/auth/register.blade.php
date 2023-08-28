@extends('user.layout.auth')

@section('content')
<div class="col-lg-8">
    @if(empty($applicant))
    <!-- Primary Alert -->
    <div class="alert alert-primary alert-dismissible alert-additional fade show" id="generalAlert" role="alert">
        <div class="alert-body">
            <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                    <i class="fs-16 align-middle"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="alert-heading">Welcome to Application Portal</h5>
                    <hr>
                    <p class="mb-0">
                        For Admission into Undergraduate Programme <strong> ({{ !empty($pageGlobalData->sessionSetting) ? $pageGlobalData->sessionSetting->application_session : null }} Academic Session) </strong>You are most welcome to study at {{ env('SCHOOL_NAME') }}. We offer candidates excellent and stable academic calendar, comfortable hall of residence, sound morals, entrepreneurial training, skill acquisition, serene and secure environment for learning. <strong> This application form will cost ₦{{ number_format($payment->structures->sum('amount')/100, 2) }}</strong>
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
                        For Admission into Undergraduate Programme <strong> ({{ !empty($pageGlobalData->sessionSetting) ? $pageGlobalData->sessionSetting->application_session : null }} Academic Session) </strong>You are most welcome to study at {{ env('SCHOOL_NAME') }}. We offer candidates excellent and stable academic calendar, comfortable hall of residence, sound morals, entrepreneurial training, skill acquisition, serene and secure environment for learning. <strong> This application form will cost ₦{{ number_format($interPayment->structures->sum('amount')/100, 2) }}</strong>
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

                                <div class="mb-3">
                                    <label for="paymentGateway" class="form-label">Select Payment Gateway<span class="text-danger">*</span></label>
                                    <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required onchange="handlePaymentMethodChange(event)">
                                        <option value= "" selected>Select Payment Gateway</option>
                                        <option value="Paystack">Paystack</option>
                                        <option value="Remita">Remita</option>
                                        <option value="Zenith">Zenith Pay</option>
                                        <option value="BankTransfer">Transfer</option>
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
                <h5>Kindly fill the form below</h5>
                <form class="needs-validation" method="POST" novalidate action="{{ url('applicant/register') }}">
                    @csrf
                    <div class="p-2 mt-4">
                        <div class="mb-3 border-top border-top-dashed pt-3">
                            <label for="applicationType" class="form-label">Select Application Type<span class="text-danger">*</span></label>
                            <select class="form-select" aria-label="applicationType" name="applicationType" required onchange="handleApplicationTypeChange(event)">
                                <option value= "" selected>Select Application Type</option>
                                <option value="General Application">General Application(UTME & DE)</option>
                                <option value="Inter Transfer Application">Inter Transfer Application</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-2 mt-4 border-top border-top-dashed pt-3" style="display: none" id="applicationForm">
                    
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
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Enter email address" required>
                            <div class="invalid-feedback">
                                Please enter email
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="referrer" class="form-label">Referrer Code </label>
                            <input type="text" class="form-control" name="referrer" id="referrer" value="{{ isset($_GET['ref']) ? $_GET["ref"] : null  }}" required>
                            <div class="invalid-feedback">
                                Please enter referrer code
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="programme_id" class="form-label">Select Programme<span class="text-danger">*</span></label>
                            <select class="form-select" aria-label="programme_id" name="programme_id" required>
                                <option value= "" selected>Select Programme</option>
                                @foreach($programmes as $programme)
                                    <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                                @endforeach
                            </select>
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
                                <option value="Paystack">Paystack</option>
                                <option value="Remita">Remita</option>
                                <option value="Zenith">Zenith Pay</option>
                                <option value="BankTransfer">Transfer</option>
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

                        <div class="mt-5 text-center">
                            <p class="mb-0">Already paid for application ? <a href="{{url('/applicant/login')}}" class="fw-semibold text-primary text-decoration-underline"> Sign in to complete application</a> </p>
                        </div>

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

    function handleApplicationTypeChange(event) {
        const selectedValue = event.target.value;
        const generalAlert = document.getElementById("generalAlert");
        const interTransferAlert = document.getElementById("interTransferAlert");
        const applicationForm = document.getElementById("applicationForm");

        // Toggle visibility based on the selected value
        if (selectedValue === "") {
            applicationForm.style.display = "none";
            generalAlert.style.display = "none";
            interTransferAlert.style.display = "none";
        } else if (selectedValue === "Inter Transfer Application") {
            applicationForm.style.display = "block";
            generalAlert.style.display = "none";
            interTransferAlert.style.display = "block";
        } else {
            generalAlert.style.display = "block";
            interTransferAlert.style.display = "none";
            applicationForm.style.display = "block";
        }
    }

    // function handleProgrammeChange(event) {
    //     const selectedProgramme = event.target.value;
    //     if(selectedProgramme != ''){
    //         axios.get("{{ url('/applicant/programmeById')  }}/"+selectedProgramme)
    //         .then(response => {
    //             const data = response.data;
    //             const totalAmount = getTotalAmountForApplicationFee(data);
                
    //             // Set the total amount in the paragraph element
    //             const amountParagraph = document.getElementById('amount');
    //             amountParagraph.textContent = `Application Fee(Non Refundable): ₦${totalAmount.toFixed(2)}`;
    //             document.getElementById('paymentInfo').style.display = 'block';
    //         })
    //         .catch(error => {
    //             console.error(error);
    //         });
    //     }else{
    //         document.getElementById('paymentInfo').style.display = 'none';
    //     }
    // }

    // function getTotalAmountForApplicationFee(data) {
    //     const applicationFeePayment = data.payments.find(payment => payment.title === "Application Fee");
    //     if (!applicationFeePayment) {
    //         return 0;
    //     }

    //     const totalAmount = applicationFeePayment.structures.reduce((total, structure) => total + parseInt(structure.amount), 0);
    //     return totalAmount/100 + 52;
    // }

</script>
@endsection
