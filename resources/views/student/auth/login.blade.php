@extends('student.layout.auth')

@section('content')
<div class="col-lg-8">
    <div class="p-lg-5 p-4">
        <div>
            <h5 class="text-primary">Welcome Back !</h5>
            <p class="text-muted">Sign in to Student Portal.</p>
        </div>

        <div class="mt-4">
            <form method="POST" action="{{ url('/student/login') }}">
            @csrf

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                    <input type="text" class="form-control" name="email" id="email" placeholder="Enter Email Address">
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} mb-3">
                    <div class="float-end">
                        <a href="{{ url('/student/password/reset') }}" class="text-muted">Forgot password?</a>
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
    </div>
</div>
@endsection