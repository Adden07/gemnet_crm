@extends('layouts.frontend')
@section('title', "If you forget your password you have another option.")
@section('meta_description', "Don't get worried about your password. We have another option for our clients if they forget their password they can recover it in any easy way.")

@section('content')
<section class="pt-4 mt-3 pb-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3 col-sm-8 offset-sm-2">
                <form class="registration custom_form" method="POST" action="{{ route('password.update') }}" novalidate>
                    @csrf
                    <div class="box-header">
                        <h2>Reset Password!</h2>
                    </div>
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group mb-3">
                        <label>E-Mail Address</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" data-parsley-minlength="6">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Confirm Password</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" data-parsley-equalto="#password" data-parsley-minlength="6">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class=" d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-theme btn-block btn-sm">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection