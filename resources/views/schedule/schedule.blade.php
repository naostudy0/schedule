@extends('layouts.base')

@section('style')
<link href="{{ asset('css/app_old.css') }}?{{ date('Ymd') }}" rel="stylesheet">
<link href="{{ asset('css/base.css') }}?{{ date('Ymd') }}" rel="stylesheet">
<link href="{{ asset('css/register.css') }}?{{ date('Ymd') }}" rel="stylesheet">
<link href="{{ asset('css/schedule.css') }}?{{ date('Ymd') }}" rel="stylesheet">
@endsection

@section('content')
  <div class="container">
    <div class="row">
      <div class="calendar col-sm-10 col-md-8 col-lg-4">
        @if (session('flash_msg'))
          <div class="flash-message" style="margin-top:10px;">
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
        @if (! $days_have_plan)
          <p class="description">日付をクリックして予定を登録できます</p>
          <p class="description add">予定共有管理から予定共有ユーザーを追加すると、予定入力時に共有選択画面が追加されます。</p>
        @endif
      </div><!-- calendar -->

      <div class="plan-list col-lg-8 col-md-10 col-lg-7">
        <h2>予定一覧</h2>
        @if ($days_have_plan)
          <ul>
            @php $plan_colors = []; @endphp
            @foreach ($plans as $plan)
              @if (! in_array($plan->color, $plan_colors))
                <li class="color" id="{{ array_flip(config('const.plan_color'))[$plan->color] }}">&nbsp;</li>
                @php $plan_colors[] = $plan->color; @endphp
              @endif
            @endforeach
            @if (count(array_unique($plan_colors)) >= 2)
              <li class="color selected" id="all">ALL</li>
            @endif
          </ul>
        @endif

        @foreach ($days_have_plan as $day)
          <div class="plans-oneday">
            <h3>{{ $day }}</h3>
            @foreach ($plans as $plan)
              @if (\Carbon\Carbon::parse($plan->start_datetime)->isoFormat('YYYY/MM/DD(ddd)') !== $day)
                @php continue; @endphp
              @endif
              <div class="plan-wrap {{ array_flip(config('const.plan_color'))[$plan->color] }}"
                style="background-color: {{ array_flip(config('const.plan_color'))[$plan->color] }};"
              >
                <section class="plan-sec">
                  <div class="plan-main {{ $plan->user_id != Auth::id() ? 'plan-share' : '' }}">
                    <i class="fas fa-angle-double-down float-right"></i>
                    <p class="datetime">
                      {{ \Carbon\Carbon::parse($plan->start_datetime)->format('Y/m/d') }}
                      <span class="start-time">
                        {{ \Carbon\Carbon::parse($plan->start_datetime)->format('H:i') }}
                      </span>
                      &nbsp;〜&nbsp;
                      <span class="inline-block">
                        {{ \Carbon\Carbon::parse($plan->end_datetime)->format('Y/m/d H:i') }}
                      </span>
                        @if($plan->shared_user_ids)
                          <i class="fas fa-user-friends float-right share-icon"></i>
                        @endif
                    </p>
                    <p class="content">{{ $plan->content }}</p>
                  </div><!-- plan-main -->
                  <div class="plan-other {{ $plan->shared_user_ids && $plan->plan_made_user_id !== Auth::id() ? 'plan-share' : '' }}">
                    <div class="detail">
                      <p>{{ $plan->detail }}</p>
                    </div><!-- detail -->
                    @if ($plan->shared_user_ids)
                      <div class="share-users">
                        @if ($plan->plan_made_user_id !== Auth::id())
                          <p><span class="user-name">{{ $plan->made_user_name }}</span>さんにより追加されました</p><br>
                        @endif
                        {{-- 予定共有が自分のみの場合は表示しない --}}
                        @if ($plan->shared_user_ids != Auth::id())
                          <p>
                            この予定は、<br>
                              @foreach (explode(',', $plan->shared_user_ids) as $shared_user_id)
                                @if ($shared_user_id != Auth::id())
                                  <span class="user-name">
                                    &nbsp;&nbsp;{{ $shared_user_names[$plan->plan_id][$shared_user_id] }}
                                  </span>さん<br>
                                @endif
                              @endforeach
                            と共有しています
                          </p>
                        @endif
                      </div><!-- share-users -->
                    @endif
                    @if ($plan->user_id === Auth::id())
                      <div class="btn-wrap">
                        <a class="btn btn-primary" href="/plan/update?id={{ $plan->plan_id }}">修正</a>
                        <a class="btn btn-danger" href="/plan/delete_confirm?id={{ $plan->plan_id }}">削除</a>
                      </div><!-- btn-wrap -->
                    @endif
                  </div><!-- plan-other -->
                </section><!-- plan -->
              </div><!-- plan_wrap -->
            @endforeach
          </div><!-- plans-oneday -->
        @endforeach
      </div><!-- plan-list -->
    </div><!-- row -->
  </div><!-- container -->
@endsection

@section('body_script')
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script>
    $(function() {
      $('.plan-main').on('click', function() {
        $(this).next().slideToggle();
        $(this).children('i').toggleClass('open');
      });

      function initialize() {
        $('.selected').each(function() {
          $(this).removeClass('selected');
        });
        $('.hide').each(function() {
          $(this).removeClass('hide');
        });
        $('.show').each(function() {
          $(this).removeClass('show');
        });
        $('.plans-oneday').each(function() {
          $('.plans-oneday').show();
        });
        $('.plan-wrap').each(function() {
          $('.plan-wrap').show();
        });
      }

      var select_id = '';
      function selected(select_id) {
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

      $('#pink').on('click', function() {
        initialize();

        selected($('#pink'));
        changeShow($('.pink'));

        hide_class_array = [$('.orange'),$('.palegreen'),$('.skyblue'),$('.tomato'),$('.gray')];
        eachChangeHide(hide_class_array);

        oneday_hide();
      });

      $('#orange').on('click', function() {
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
