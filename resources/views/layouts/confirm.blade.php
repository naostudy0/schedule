@extends('layouts.base')


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
                                            <time class="date">{{ is_array($plan_data) ? $plan_data['start_date'] . ' ' . $plan_data['start_time'] : $plan_data->start_datetime }}</time>
                                            <span> 〜 </span>
                                            <time class="date">{{ is_array($plan_data) ? $plan_data['end_date'] . ' ' . $plan_data['end_time'] : $plan_data->end_datetime }}</time>
                                        </li>

                                        <li class="content-wrap">
                                            <span class="tag">内容</span>
                                            <span class="content"><span class="table">{{ is_array($plan_data) ? $plan_data['content'] : $plan_data->content }}</span></span>
                                        </li>

                                        <li class="detail-wrap">
                                            <span class="tag">詳細</span>
                                            <span class="detail"><span class="table">{{ is_array($plan_data) ? $plan_data['detail'] : $plan_data->detail }}</span></span>
                                        </li>

                                        @if ($shared_user_names)
                                            <div class="share-users">
                                                <p>■共有するユーザー</p>
                                                <ul>
                                                    @foreach ($shared_user_names as $name)
                                                        <li>{{ $name }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </ul>

                                    <form method="POST">

                                        @yield('hidden')
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

