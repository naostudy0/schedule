@extends('layouts.base')

@section('style')
<link href="{{ asset('css/app_old.css') }}?{{ date('Ymd') }}" rel="stylesheet">
<link href="{{ asset('css/base.css') }}?{{ date('Ymd') }}" rel="stylesheet">
<link href="{{ asset('css/register.css') }}?{{ date('Ymd') }}" rel="stylesheet">
<link href="{{ asset('css/schedule.css') }}?{{ date('Ymd') }}" rel="stylesheet">
<link href="{{ asset('css/customer.css') }}?{{ date('Ymd') }}" rel="stylesheet">
<link href="{{ asset('css/share.css') }}?{{ date('Ymd') }}" rel="stylesheet">
@endsection

@section('content')
<div class="customer">
    @if (session('flash_msg'))
        <div class="flash-message">
            {{ session('flash_msg') }}
        </div><!-- flash-message -->
    @endif

    <div class="card">
        <div class="card-header text-center">
            <h2 class="title">@yield('title')</h2>
        </div><!-- card-header -->

        <div class="card-body">
        @yield('content_customer')
        </div><!-- card-body -->
    </div><!-- card -->

    <div class="btn-footer">
        @yield('btn_footer')
    </div><!-- btn-footer -->
</div><!-- customer -->
@endsection