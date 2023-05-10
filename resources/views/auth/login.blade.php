@extends('layouts.frontend')
@section('title', "Easy way to sign in to your account with us.")
@section('meta_description', "We care about our clients and don't want to bother them with how to sign in that's why we are giving you the easiest form to log in to your account in a very efficient and secure manner. That is very effective for our customers.")

@section('content')
<section class="pt-5 mt-3 pb-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3 col-sm-8 offset-sm-2">
                <form class="registration" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="box-header">
                        <h2>SIGN IN TO YOUR ACCOUNT!</h2>
                    </div>

                    <div class="form-group mb-3">
                        <label>Email Address</label>
                        <input type="text" id="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
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

                    <div class=" d-flex justify-content-between align-items-center">
                        <div class="checkbox checkbox-default pl-0">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">
                                Remember me
                            </label>
                        </div>
                        <button type="submit" class="btn btn-theme btn-sm">Login</button>
                    </div>

                    <div class="border-top mt-3 pt-2 d-flex justify-content-between align-items-center">
                        <h4>
                            <a href="{{ route('password.request') }}" class="color-green">Forget your Password ?</a>
                        </h4>
                        <h4>
                            Need an account? <a href="{{ route('register') }}" class="color-green">Join Shipit4us</a>
                        </h4>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection