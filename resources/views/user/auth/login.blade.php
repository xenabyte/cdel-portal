@extends('user.layout.auth')

@section('content')
<div class="col-lg-8">
    <div class="p-lg-5 p-4">
        <div>
            <h5 class="text-primary">Welcome Back !</h5>
            <p class="text-muted">Sign in to continue to Application Portal.</p>
        </div>

        <div class="mt-4">
            <form method="POST" action="{{ url('/applicant/login') }}">
                @csrf

                <input type="hidden" name="academic_session" value="{{ !empty($pageGlobalData->sessionSetting) ? $pageGlobalData->sessionSetting->application_session : null }}" />
                <div class="mb-3" class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" name="email" id="email" placeholder="Enter email" value="{{ old('email') }}" autofocus>

                    @if ($errors->has('email'))
                        <br>
                        <div class="alert alert-danger" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </div>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} mb-3">
                    <div class="float-end">
                        <a href="{{ url('/applicant/password/reset') }}" class="text-muted">Forgot password?</a>
                    </div>
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

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                    <label class="form-check-label" for="auth-remember-check">Remember me</label>
                </div>

                <div class="mt-4">
                    <button class="btn btn-success w-100" type="submit">Sign In</button>
                </div>
            </form>
        </div>

        <div class="mt-5 text-center">
            <p class="mb-0">Don't have an account ? <a href="{{url('/applicant/register')}}" class="fw-semibold text-primary text-decoration-underline"> Signup</a> </p>
        </div> 
    </div>
</div>
@endsection
