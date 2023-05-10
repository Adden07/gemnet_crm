@extends('layouts.frontend')

@section('content')
<section class="pt-5 mt-3 pb-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3 col-sm-8 offset-sm-2">
                <form class="registration custom_form" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="box-header">
                        <h2>Reset Your Password!</h2>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}. You will be redirected to login page soon.
                        </div>
                        <script>
                            setTimeout(function(){
                                window.location.href = "{{ route('login') }}";
                            }, 3000);
                        </script>
                    @endif

                    <div class="form-group mb-3">
                        <label>Email Address</label>
                        <input type="text" id="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="d-flex text-center">
                        <button type="submit" class="btn btn-block btn-theme btn-sm">Send Password Reset Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection