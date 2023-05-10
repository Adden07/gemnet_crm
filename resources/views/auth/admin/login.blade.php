@extends('layouts.admin_auth')
@section('title', 'Login')
@section('page-heading', 'Enter your email address and password to access admin panel.')

@section('content')
<form method="POST" action="{{ route('admin.login') }}" class="custom_form" novalidate>
    @csrf
    <div class="form-group mb-3">
        <label for="emailaddress">{{ __('Username') }}</label>
        <input id="email" type="text" name="username" value="{{ old('username') }}" required autocomplete="username" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" autofocus placeholder="Enter your username">
        @error('username')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
        @error('is_active')
        <span class="" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="form-group mb-3">
        <label for="password">{{ __('Password') }}</label>
        <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
        @error('password')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    @if ($errors->has('active'))
    <p class="alert alert-danger mt-2">
        <span class="help-block">
            <strong>{{ $errors->first('active') }}</strong>
        </span>
    </p>
    @endif

    <div class="form-group mb-3">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="custom-control-label" for="remember">{{ __('Remember Me') }}</label>
        </div>
    </div>

    <div class="form-group mb-0 text-center">
        <button class="btn btn-primary btn-block" type="submit"> {{ __('Login') }} </button>
    </div>

</form>
@endsection

@section('after-page')
<div class="row mt-3">
    <div class="col-12 text-center">
        @if (Route::has('password.request'))
        <a class="text-white-50 ml-1" href="{{ route('password.request') }}">
            {{ __('Forgot Your Password?') }}
        </a>
        @endif
    </div> <!-- end col -->
</div>
@endsection