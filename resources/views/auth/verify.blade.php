@extends('layouts.frontend')

@section('content')
<div class="container">
    <div class="jumbotron mt-5 bg-light border">
        @if (session('resent'))
        <div class="alert alert-success" role="alert">
            A fresh verification link has been sent to your email address.
        </div>
        @endif
        <h1 class="display-4 text-danger mb-3">Verify Your Email Address</h1>
        <p class="lead">Your email is <strong class="text-danger">not verified</strong> yet. Before proceeding, please check your email for a verification link.</p>
        <hr class="my-2 border-top">
        <p>If you did not receive the email please click on the button below to get a new email with your verification code!</p>
        <!-- <a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a> -->
        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="btn btn-theme btn-lg px-4">Click Here</button>
        </form>
    </div>
</div>
@endsection