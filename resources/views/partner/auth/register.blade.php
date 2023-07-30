@extends('partner.layout.auth')

@section('content')
<div class="col-lg-8">
    <div class="p-lg-5 p-4">
        <div>
            <h5 class="text-primary">Welcome!</h5>
            <p class="text-muted">Sign up to continue to Partner Portal.</p>
        </div>

        <div class="mt-4">
            <form method="POST" action="{{ url('/partner/register') }}">
                @csrf
                <div class="mb-3" class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" value="{{ old('name') }}" autofocus>

                    @if ($errors->has('name'))
                        <br>
                        <div class="alert alert-danger" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </div>
                    @endif
                </div>


                <div class="mb-3" class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Enter email" value="{{ old('email') }}" autofocus>

                    @if ($errors->has('email'))
                        <br>
                        <div class="alert alert-danger" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </div>
                    @endif
                </div>



                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} mb-3">
                    <label class="form-label" for="password-input">Password</label>
                    <div class="position-relative auth-pass-inputgroup mb-3">
                        <input type="password" class="form-control pe-5 password-input" name="password" placeholder="Enter password" id="password-input">
                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                    </div>


                    @if ($errors->has('password'))
                        <br>
                        <div class="alert alert-danger" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </div>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('password-confirm') ? ' has-error' : '' }} mb-3">
                    <label class="form-label" for="password-input">Confirm Password</label>
                    <div class="position-relative auth-pass-inputgroup mb-3">
                        <input type="password" class="form-control pe-5 password-input" name="password_confirmation" placeholder="Enter password" id="password-input">
                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                    </div>


                    @if ($errors->has('password_confirmation'))
                        <br>
                        <div class="alert alert-danger" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </div>
                    @endif
                </div>


                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                    <label class="form-check-label" for="auth-remember-check">Remember me</label>
                </div>

                <div class="mt-4">
                    <button class="btn btn-success w-100" type="submit">Sign Up</button>
                </div>
            </form>
        </div>

        <div class="mt-5 text-center">
            <p class="mb-0">Already have an account ? <a href="{{url('/partner/login')}}" class="fw-semibold text-primary text-decoration-underline"> Signin</a> </p>
        </div>
    </div>
</div>
@endsection
