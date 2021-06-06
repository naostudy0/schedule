@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row">
        <div class="plan-confirm col-lg-10 offset-lg-1 col-sm-12">
            <div class="card">
                <div class="card-header text-center">
                    <h2 class="title">@yield('title')</h2>
                </div><!-- card-header -->
            
                <div class="row">
                    <div class="card-body">
                        <div class="offset-lg-1 col-lg-10">
                            <div class="row">
                                <div class="col-lg-12">
                                    <ul class="confirm-list">
                                        <li class="datetime">
                                            <time class="date">{{ $plan_data['start_date'] }}</time>
                                            <time class="time">{{ $plan_data['start_time'] }}</time>
                                            <span> 〜 </span>
                                            <time class="date">{{ $plan_data['end_date'] }}</time>
                                            <time class="time">{{ $plan_data['end_time'] }}</time>
                                        </li>

                                        <li class="content-wrap">
                                            <span class="tag">内容</span>
                                            <span class="content"><span class="table">{{ $plan_data['content'] }}</span></span>
                                        </li>

                                        <li class="detail-wrap">
                                            <span class="tag">詳細</span>
                                            <span class="detail"><span class="table">{{ $plan_data['detail'] }}</span></span>
                                        </li>
                                    </ul>

                                    <form method="POST">
                                        @csrf
                                        <input type="hidden" name="start_date" id="start_date" value="{{ $plan_data['start_date'] }}">                   
                                        <input type="hidden" name="start_time" id="start_time" value="{{ $plan_data['start_time'] }}">
                                        <input type="hidden" name="end_date" id="end_date" value="{{ $plan_data['end_date'] }}">
                                        <input type="hidden" name="end_time" id="end_time" value="{{ $plan_data['end_time'] }}">
                                        <input type="hidden" name="color" id="color" value="{{ $plan_data['color'] }}">
                                        <input type="hidden" name="content" id="content" value="{{ $plan_data['content'] }}">
                                        <input type="hidden" name="detail" id="detail" value="{{ $plan_data['detail'] }}">
                                        @yield('id')

                                        <div class="btn-wrap">
                                        @yield('btn')
                                        </div><!-- btn-wrap -->
                                    </form>
                                </div><!-- col -->
                            </div><!-- row -->
                        </div><!-- col -->
                    </div><!-- card-body -->
                </div><!-- row -->
            </div><!-- card -->
        </div><!-- plan-confirm -->
    </div><!-- row -->
</div><!-- container -->
@endsection

