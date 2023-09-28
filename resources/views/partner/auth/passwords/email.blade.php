@extends('partner.layout.auth')

<!-- Main Content -->
@section('content')
<div class="col-lg-8">
    <div class="p-lg-5 p-4">
        <div>
            <h5 class="text-primary">Welcome Back !</h5>
            <p class="text-muted">Reset Portal</p>
        </div>

        <div class="mt-2 text-center">
            <lord-icon
                src="https://cdn.lordicon.com/rhvddzym.json" trigger="loop" colors="primary:#0ab39c" class="avatar-xl">
            </lord-icon>
        </div>

        <div class="mt-4">
            <form method="POST" action="{{ url('/partner/password/email') }}">
                @csrf

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

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

                <div class="mt-4">
                    <button class="btn btn-success w-100" type="submit">Send Password Reset Link</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
