@extends('center.layout.auth')

@section('content')
<div class="col-lg-8">
    <div class="p-lg-5 p-4">

        <div>
            <h5 class="text-primary">Welcome!</h5>
            <p class="text-muted">Create a Center Administrator Account.</p>
        </div>

        <div class="mt-4">
            <form class="needs-validation" method="POST" novalidate action="{{ url('center/register') }}">
                @csrf

                <div class="p-2 mt-4 border-top border-top-dashed pt-3">

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

                    <div class="mt-4 border-top border-top-dashed pt-3">
                        <button class="btn btn-success w-100" id='submit-button' type="submit">Register</button>
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <p class="mb-0">Already have an account? <a href="{{url('/center')}}" class="fw-semibold text-primary text-decoration-underline"> Login here!</a> </p>
                </div>
            </form>
        </div>
        <!-- end card -->

    </div>
</div>

@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        html: '<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
    });
</script>
@endif
@endsection
