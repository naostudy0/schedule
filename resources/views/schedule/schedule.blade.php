@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row">
        <div class="calendar col-sm-10 col-md-8 col-lg-4">
            
            @if (session('flash_msg'))
            <div class="flash-message">
                {{ session('flash_msg') }}
            </div><!-- flash-message -->
            @endif

            <div class="card">
                <div class="card-header text-center">
                    <a class="btn btn-outline-secondary float-left" href="{{ url('/schedule?date=' . $calendar->getPreviousMonth()) }}">◀︎</a>
                    <h2>{{ $calendar->getTitle() }}</h2>
                    <a class="btn btn-outline-secondary float-right" href="{{ url('/schedule?date=' . $calendar->getNextMonth()) }}">▶︎</a>
                </div><!-- card-header -->

                <div class="card-body">
                    {!! $calendar->render() !!}
                </div><!-- card-body -->
            </div><!-- card -->

            <p class="description">日付をクリックして予定を登録できます</p>
            <p class="description add">予定共有管理から予定共有ユーザーを追加すると、予定入力時に共有選択画面が追加されます。</p>
        </div><!-- calendar -->

        <div class="plan-list col-lg-8 col-md-10 col-lg-7">
            <h2>予定一覧</h2>
            <ul>
                @if (count($plan_colors) >= 2)
                    <li class="color selected" id="all">ALL</li>

                    @foreach ($plan_colors as $key => $value)
                        <li class="color" id="{{ $value }}">&nbsp;</li>
                    @endforeach
                @else
                    @foreach ($plan_colors as $key => $value)
                        <li class="color selected" id="{{ $value }}">&nbsp;</li>
                    @endforeach
                @endif
            </ul>

            @foreach( $plan_days as $plan_day )
                <div class="plans-oneday">
                    <h3>{{ str_replace('-', '/', $plan_day['start_date']) }}</h3>
                    @foreach( $plans as $plan )
                        @if( $plan['start_date'] ===  $plan_day['start_date'] )
                            @if( $plan['user_id'] == Auth::id() )
                                <div class="plan-wrap {{ $plan['color'] }}" style="background-color: {{ $plan['color'] }};">
                                    <section class="plan">
                                        <div class="plan-main">
                                            <i class="fas fa-angle-double-down float-right"></i>
                                            <p class="datetime">
                                                {{ str_replace('-', '/', $plan['start_date']) }} <span class="start-time">{{ substr($plan['start_time'], 0, 5) }}</span> 〜 <span class="inline-block">{{ str_replace('-', '/', $plan['end_date']) }} {{ substr($plan['end_time'], 0, 5) }}</span>
                                        
                                                @if($plan['share_user_id'])
                                                <i class="fas fa-user-friends float-right share-icon"></i>
                                                @endif
                                            </p>
                                            
                                            <p class="content">{{$plan['content']}}</p>
                                        </div><!-- plan-main -->

                                        <div class="plan-other">
                                            <div class="detail">
                                                <p>{{$plan['detail']}}</p>
                                            </div><!-- detail -->

                                            @if($plan['share_user_id'])
                                            <div class="share-users">
                                                <p>
                                                    この予定は、<br>
                                                
                                                    @foreach($plan['share_users'] as $user_data)
                                                        <span class="user-name">&nbsp;&nbsp;{{ $user_data['name'] }}</span>さん<br>
                                                    @endforeach
                                                    
                                                    と共有しています
                                                </p>
                                            </div><!-- share-users -->
                                            @endif

                                            <div class="btn-wrap">
                                                <a class="btn btn-primary" href="/plan/update?id={{ $plan['id'] }}">修正</a>
                                                <a class="btn btn-danger" href="/plan/delete_confirm?id={{ $plan['id'] }}">削除</a>
                                            </div><!-- btn-wrap -->
                                        </div><!-- plan-other -->
                                    </section><!-- plan -->
                                </div><!-- plan_wrap -->
                            @else
                                <div class="plan-wrap {{ $plan['color'] }}" style="background-color: {{ $plan['color'] }};">
                                    <section class="plan">
                                        <div class="plan-main plan-share">
                                            <i class="fas fa-angle-double-down float-right"></i>
                                            <p class="datetime">
                                                {{ str_replace('-', '/', $plan['start_date']) }} <span class="start-time">{{ substr($plan['start_time'], 0, 5) }}</span> 〜 <span class="inline-block">{{ str_replace('-', '/', $plan['end_date']) }} {{ substr($plan['end_time'], 0, 5) }}</span>
                                                @if($plan['share_user_id'])
                                                <i class="fas fa-user-friends float-right share-icon"></i>
                                                @endif
                                            </p>

                                            <p class="content">{{$plan['content']}}</p>
                                        </div><!-- plan-main -->

                                        <div class="plan-other plan-share">
                                            <div class="detail">
                                                <p>{{$plan['detail']}}</p>
                                            </div><!-- detail -->

                                            <div class="share-users">
                                                <p><span class="user-name">{{$plan['name']}}</span>さんにより追加されました</p><br>
                                                <p>
                                                    この予定は、<br>
                                                
                                                    @foreach($plan['share_users'] as $user_data)
                                                        <span class="user-name">&nbsp;&nbsp;{{ $user_data['name'] }}</span>さん<br>
                                                    @endforeach
                                                    
                                                    と共有されています
                                                </p>
                                            </div><!-- share-users -->
                                        </div><!-- plan-other -->
                                    </section><!-- plan -->
                                </div><!-- plan_wrap -->
                            @endif
                        @endif
                    @endforeach
                </div><!-- plans-oneday -->
            @endforeach
        </div><!-- plan-list -->
    </div><!-- row -->
</div><!-- container -->
@endsection


@section('script')
<script>
$(function(){
    $('.plan-main').on('click', function(){
        $(this).next().slideToggle();
        $(this).children('i').toggleClass('open');
    });

    function initialize() {
        $('.selected').each(function(){
            $(this).removeClass('selected');
        });
        $('.hide').each(function(){
            $(this).removeClass('hide');
        });
        $('.show').each(function(){
            $(this).removeClass('show');
        });
        $('.plans-oneday').each(function(){
            $('.plans-oneday').show();
        });
        $('.plan-wrap').each(function(){
            $('.plan-wrap').show();
        });
    }

    var select_id = '';
    function selected(select_id){
        $(select_id).toggleClass('selected');
    }

    var show_class = '';
    function changeShow(show_class){
        $(this).show();
        $(this).toggleClass('show');
    }

    var hide_class = '';
    function changeHide(hide_class){
        $(hide_class).hide();
        $(hide_class).toggleClass('hide');
    }

    var hide_class_array = [];
    function eachChangeHide(hide_class_array){
        $(hide_class_array).each(function(){
            changeHide(this);
        });
    }

    function oneday_hide(){
        $('.plans-oneday').each(function(){
            var class_name = [];
            var counter_child = 0;
            var counter_hide = 0;
            var search = 'hide';

            $(this).children('div').each(function(){
                $(this).each(function(){
                    class_name = $(this).attr('class');
                    if(class_name.indexOf(search) > -1){
                        counter_hide++;
                    }
                });
                counter_child++;
            });

            if(counter_hide == counter_child){
                $(this).hide();
            }
        });
    }

    $('#pink').on('click', function(){
        initialize();

        selected($('#pink'));
        changeShow($('.pink'));

        hide_class_array = [$('.orange'),$('.palegreen'),$('.skyblue'),$('.tomato'),$('.gray')];
        eachChangeHide(hide_class_array);

        oneday_hide();
    });

    $('#orange').on('click', function(){
        initialize();

        selected($('#orange'));
        changeShow($('.orange'));

        hide_class_array = [$('.pink'),$('.palegreen'),$('.skyblue'),$('.tomato'),$('.gray')];
        eachChangeHide(hide_class_array);

        oneday_hide();
    });

    $('#palegreen').on('click', function(){
        initialize();

        selected($('#palegreen'));
        changeShow($('.palegreen'));

        hide_class_array = [$('.pink'),$('.orange'),$('.skyblue'),$('.tomato'),$('.gray')];
        eachChangeHide(hide_class_array);

        oneday_hide();
    });

    $('#skyblue').on('click', function(){
        initialize();

        selected($('#skyblue'));
        changeShow($('.skyblue'));

        hide_class_array = [$('.pink'),$('.orange'),$('.palegreen'),$('.tomato'),$('.gray')];
        eachChangeHide(hide_class_array);

        oneday_hide();
    });

    $('#tomato').on('click', function(){
        initialize();

        selected($('#tomato'));
        changeShow($('.tomato'));

        hide_class_array = [$('.pink'),$('.orange'),$('.palegreen'),$('.skyblue'),$('.gray')];
        eachChangeHide(hide_class_array);

        oneday_hide();
    });

    $('#gray').on('click', function(){
        initialize();

        selected($('#gray'));
        changeShow($('.gray'));

        hide_class_array = [$('.pink'),$('.orange'),$('.palegreen'),$('.skyblue'),$('.tomato')];
        eachChangeHide(hide_class_array);

        oneday_hide();
    });

    $('#all').on('click', function(){
        initialize();
        selected($('#all'));
    });
});
</script>
@endsection