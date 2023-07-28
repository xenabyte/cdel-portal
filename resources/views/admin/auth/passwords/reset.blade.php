@extends('admin.layout.auth')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4">

            <div class="card-body p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">Welcome Back !</h5>
                    <p class="text-muted"><strong>Reset Password</strong></p>
                </div>
                <div class="p-2 mt-4">
                    <form method="POST" action="{{ url('/admin/password/reset') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}"class="form-group{{ $errors->has('email') ? ' has-error' : '' }} mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" class="form-control" id="email" placeholder="Enter email" value="{{ old('email') }}" autofocus>

                            
                            @if ($errors->has('email'))
                            <div class="mt-4 mb-3">
                                <span class="help-block alert alert-danger">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            </div>
                            @endif
                        </div>

                        <br>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}mb-3">
                            <div class="float-end">
                                <a href="{{ url('/admin/password/reset') }}" class="text-muted">Forgot password?</a>
                            </div>
                            <label class="form-label" for="password-input">Password</label>
                            <div class="position-relative auth-pass-inputgroup mb-3">
                                <input type="password" class="form-control pe-5 password-input" name="password" placeholder="Enter password" id="password-input">
                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted shadow-none password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                            </div>
                            
                            @if ($errors->has('password'))
                            <div class="mt-4 mb-3">
                                <span class="help-block alert alert-danger">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
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
                            <div class="mt-4 mb-3">
                                <span class="help-block alert alert-danger">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            </div>
                            @endif
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-success w-100" type="submit">Save Password Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->

    </div>
</div>

@endsection
