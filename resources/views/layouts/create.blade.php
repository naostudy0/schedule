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
<div class="plan">
    <div class="card">
        <div class="card-header text-center">
            <h2 class="title">@yield('title')</h2>
        </div><!-- card-header -->

        <div class="card-body">
            <form action="@yield('action')" method="POST" class="create-form">

                @csrf
                <div class="plan-datetime">
                    <div class="start-end flex-center-wrapper">
                        <label for="start_date" class="datetime">開始</label>
                        <input type="date" name="start_date" id="start_date" class="date" 
                            value="{{ old('start_date', is_array($plan_data) ? substr($plan_data['start_datetime'], 0, 10) : substr($plan_data->start_datetime, 0, 10)) }}"><!-- 
                    ---><input type="time" name="start_time" id="start_time" class="time" 
                            value="{{ old('start_time', is_array($plan_data) ?
                                substr($plan_data['start_datetime'], 11, 5) : substr($plan_data->start_datetime, 11, 5)) }}"><br>
                    </div><!-- start-end flex-center-wrapper -->

                    <div class="start-end flex-center-wrapper">
                        <label for="end_date" class="datetime">終了</label>
                        <input type="date" name="end_date" id="end_date" class="date"
                            value="{{ old('end_date', is_array($plan_data) ?
                                substr($plan_data['end_datetime'], 0, 10) : substr($plan_data->end_datetime, 0, 10)) }}"><!-- 
                    ---><input type="time" name="end_time" id="end_time" class="time"
                            value="{{ old('end_time', is_array($plan_data) ?
                                substr($plan_data['end_datetime'], 11, 5) : substr($plan_data->end_datetime, 11, 5)) }}"><br>
                    </div><!-- start-end flex-center-wrapper -->

                </div><!-- plan-datetime -->

                @if ($errors->has('start_date'))
                <p class="text-danger">{{ $errors->first('start_date') }}</p>
                @endif

                @if ($errors->has('start_time'))
                <p class="text-danger">{{ $errors->first('start_time') }}</p>
                @endif

                @if ($errors->has('end_date'))
                <p class="text-danger">{{ $errors->first('end_date') }}</p>
                @endif  

                @if ($errors->has('end_time'))
                <p class="text-danger">{{ $errors->first('end_time') }}</p>
                @endif


                <div class="tag">
                    <label for="color">タグ</label>
                    <div class="color">
                        <span class="input-wrap"><input type="radio" name="color" value="1" {{ $color_checked[1] }}><span class="color1">カラー1</span></span>
                        <span class="input-wrap"><input type="radio" name="color" value="2" {{ $color_checked[2] }}><span class="color2">カラー2</span></span>
                        <span class="input-wrap"><input type="radio" name="color" value="3" {{ $color_checked[3] }}><span class="color3">カラー3</span></span>
                        <span class="input-wrap"><input type="radio" name="color" value="4" {{ $color_checked[4] }}><span class="color4">カラー4</span></span>
                        <span class="input-wrap"><input type="radio" name="color" value="5" {{ $color_checked[5] }}><span class="color5">カラー5</span></span>
                        <span class="input-wrap"><input type="radio" name="color" value="6" {{ $color_checked[6] }}><span class="color6">カラー6</span></span>
                    </div><!-- color -->

                    @if ($errors->has('color'))
                    <p class="text-danger">{{ $errors->first('color') }}</p>
                    @endif
                </div><!-- tag col -->

                <div class="content">
                    <label for="content">内容</label><br>
                    <input type="text" class="" name="content" id="content" value="{{ old('content', is_array($plan_data) ? $plan_data['content'] : $plan_data->content) }}"><br>

                    @if ($errors->has('content'))
                    <p class="text-danger">{{ $errors->first('content') }}</p>
                    @endif
                </div><!-- content -->

                <div class="detail">
                    <label for="detail">詳細 <span>※任意</span></label><br>
                    <textarea name="detail" id="detail" rows="8" cols="40" wrap="soft">{{ old('detail', is_array($plan_data) ? $plan_data['detail'] : $plan_data->detail) }}</textarea><br>

                    @if ($errors->has('detail'))
                    <p class="text-danger">{{ $errors->first('detail') }}</p>
                    @endif
                </div><!-- detail -->

                @php $share_users = is_array($plan_data) ? $share_users : $plan_data->share_users; @endphp
                @if ($share_users)
                <div class="share-users-wrap">
                    <h2>予定共有</h2>
                    <div class="share-users">
                        <div class="radio-wrapper">
                            <input type="radio" id="allow" name="share_users" value="1">
                            <label for="allow">共有する</label>
                        </div><!-- radio-wrapper -->

                        <div class="radio-wrapper">
                            <input type="radio" id="disallow" name="share_users" value="0" checked>
                            <label for="disallow">共有しない</label>
                        </div><!-- radio-wrapper -->

                        <div id="share-checkbox" class="hide share-checkbox">
                            @foreach ($share_users as $share_user)
                                <span class="input-wrap">
                                    <label>
                                        <input type="checkbox" name="share_user[]" value="{{ $share_user->share_id }}">{{ $share_user->name }}
                                    </label>
                                </span>
                            @endforeach
                        </div><!-- share-checkbox -->
                    </div><!-- share-users -->
                </div><!-- share-users-wrap -->
                @endif

                @yield('id')

                <div class="btn-wrap">
                    @yield('btn')
                    <button type="submit" class="btn btn-success float-right">確認</button>  
                </div><!-- btn-wrap -->
            </form>
        </div><!-- card-body -->
    </div><!-- card -->
</div><!-- plan -->
@endsection


@section('script')
<script>
$(function(){
    $('#allow').on('click', function(){
        $('#share-checkbox').slideToggle(this.checked);
    });
    $('#disallow').on('click', function(){
        $('#share-checkbox').slideToggle(this.checked);
    });
});
</script>
@endsection