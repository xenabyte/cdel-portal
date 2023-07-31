@extends('student.layout.auth')

@section('content')
<div class="col-lg-8">
    <div class="p-lg-5 p-4">
        <div>
            <h5 class="text-primary">Welcome Back !</h5>
            <p class="text-muted"><strong>Reset Password.</strong></p>
        </div>
        <div class="mt-4">
        <form method="POST" action="{{ url('/student/password/reset') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" name="email" class="form-control" id="email" placeholder="Enter email" value="{{ old('email') }}" autofocus>

                @if ($errors->has('email'))
                    <br>
                    <div class="alert alert-danger" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </div>
                @endif
            </div>

            <br>
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}mb-3">
                <label class="form-label" for="password-input">Password</label>
                <div class="position-relative auth-pass-inputgroup mb-3">
                    <input type="password" class="form-control pe-5 password-input" name="password" placeholder="Enter password" id="password-input">
                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted shadow-none password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                </div>

                @if ($errors->has('password'))
                    <br>
                    <div class="alert alert-danger" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </div>
                @endif
            </div>
            <br>

            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}mb-3">
                <label class="form-label" for="password-input">Confirm Password</label>
                <div class="position-relative auth-pass-inputgroup mb-3">
                    <input type="password" class="form-control pe-5 password-input" name="password_confirmation" placeholder="Enter password" id="password-input">
                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted shadow-none password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                </div>

                @if ($errors->has('password_confirmation'))
                    <br>
                    <div class="alert alert-danger" role="alert">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </div>
                @endif
            </div>

            <div class="mt-4">
                <button class="btn btn-success w-100" type="submit">Save Password Changes</button>
            </div>
        </form>
        </div>

        
    </div>
</div>
@endsection
