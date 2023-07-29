@extends('admin.layout.auth')

<!-- Main Content -->
@section('content')

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4">

            <div class="card-body p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">Welcome Back !</h5>
                    <p class="text-muted">Reset Password</p>
                </div>
                <div class="p-2 mt-4">
                    <form method="POST" action="{{ url('/admin/password/email') }}">
                        @csrf

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}"class="form-group{{ $errors->has('email') ? ' has-error' : '' }} mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" class="form-control" id="email" placeholder="Enter email" value="{{ old('email') }}" autofocus>

                            @if ($errors->has('email'))
                            <br>
                            <div class="alert alert-danger" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </div>
                            @endif
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-success w-100" type="submit">Send Password Reset Link</button>
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
