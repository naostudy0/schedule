@extends('layouts.app')


@section('content')
<div class="customer">
    <div class="link-wrap">
        <p class="top-link float-left"><a href="{{ route('schedule') }}">予定一覧へ戻る</a></p>
        @yield('btn_header')
    </div><!-- link-wrap -->

    <div class="card">
        <div class="card-header text-center">
            <h2 class="title">@yield('title')</h2>
        </div><!-- card-header -->

        @if (session('flash_msg'))
            <div class="flash-message">
                {{ session('flash_msg') }}
            </div><!-- flash-message -->
        @endif

        <div class="card-body">
        @yield('content_customer')
        </div><!-- card-body -->
    </div><!-- card -->

    <div class="btn-footer">
        @yield('btn_footer')
    </div><!-- btn-footer -->
</div><!-- customer -->
@endsection