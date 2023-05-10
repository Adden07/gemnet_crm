{{-- @php
    if (\Request::is('web_admin/*')) {
        $view = 'layouts.admin_error';
        $admin = true;
    } else {
        $view = 'layouts.admin_error';
        $admin = false;
    }
@endphp --}}

@extends('layouts.admin_error')

@section('content')
<div class="container">
    <div class="jumbotron mt-5 bg-light border">
        <h1 class="display-4 text-danger mb-3">@yield('code') @yield('title')</h1>
        <p class="lead">@yield('message')</p>
        {{-- @if($admin)
            <a href="{{route('admin.home')}}" class="btn btn-danger btn-lg px-4">Go to Dashboard</a>
        @else
            <a href="{{route('front.home')}}" class="btn btn-theme btn-lg px-4">Go to Homepage</a>
        @endif --}}
    </div>
</div>
@endsection
