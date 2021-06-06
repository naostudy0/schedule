@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row">
        <div class="customer col-lg-8 offset-lg-2 col-sm-12">
            <a href="{{ route('schedule') }}"><p class="top-link float-left">予定一覧へ戻る</p></a>
            @yield('btn_header')
        </div><!-- col -->

        <div class="customer col-lg-8 offset-lg-2 col-sm-12">
            <div class="card">
                <div class="card-header text-center">
                    <h2 class="title">@yield('title')</h2>
                </div><!-- card-header -->

                <div class="offset-md-2 col-md-8">
                    @if (session('flash_msg'))
                        <div class="flash-message">
                            {{ session('flash_msg') }}
                        </div><!-- flash-message -->
                    @endif
                </div><!-- col -->

                <div class="row">
                    <div class="card-body">
                    @yield('content_customer')
                    </div><!-- card-body -->
                </div><!-- row -->
            
            </div><!-- card -->
            <div class="btn_footer">
                @yield('btn_footer')
            </div>
        </div><!-- plan-confirm -->
    </div><!-- row -->
</div><!-- container -->
@endsection