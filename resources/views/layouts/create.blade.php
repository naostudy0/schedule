@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row">
        <div class="plan col-lg-10 offset-md-1 col-md-10 col-sm-12">
            <div class="card">
                <div class="card-header text-center">
                    <h2 class="title">@yield('title')</h2>
                </div><!-- card-header -->

                <div class="row">
                    <div class="card-body">
                        <form action="@yield('action')" method="POST">

                            @csrf
                            <div class="plan-datetime offset-lg-1 col-lg-10">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="start-end flex-center-wrapper">
                                            <label for="start_date" class="datetime">開始</label>
                                            <input type="date" name="start_date" id="start_date" class="date" 
                                                value="{{ old('start_date', $plan_data['start_date']) }}"><!-- 
                                        ---><input type="time" name="start_time" id="start_time" class="time" 
                                                value="{{ old('start_time', substr($plan_data['start_time'], 0, 5)) }}"><br>
                                        </div><!-- start-end flex-center-wrapper -->
                                    </div><!-- col -->

                                    <div class="col-lg-6">
                                        <div class="start-end flex-center-wrapper">
                                            <label for="end_date" class="datetime">終了</label>
                                            <input type="date" name="end_date" id="end_date" class="date"
                                                value="{{ old('end_date', $plan_data['end_date']) }}"><!-- 
                                        ---><input type="time" name="end_time" id="end_time" class="time"
                                                value="{{ old('end_time', substr($plan_data['end_time'], 0, 5)) }}"><br>
                                        </div><!-- start-end flex-center-wrapper -->
                                    </div><!-- col -->

                                    <div class="col-lg-12">
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
                                    </div><!-- col -->
                                </div><!-- row -->
                            </div><!-- plan-datetime -->

                            <div class="tag offset-lg-1 col-lg-10">
                                <label for="color">タグ</label>
                                <div class="color">
                                    <input type="radio" name="color" value="1" {{ $color_checked[1] }}><span class="color1">カラー1</span>
                                    <input type="radio" name="color" value="2" {{ $color_checked[2] }}><span class="color2">カラー2</span>
                                    <input type="radio" name="color" value="3" {{ $color_checked[3] }}><span class="color3">カラー3</span>
                                        <br class="br-min-992">
                                    <input type="radio" name="color" value="4" {{ $color_checked[4] }}><span class="color4">カラー4</span>
                                    <input type="radio" name="color" value="5" {{ $color_checked[5] }}><span class="color5">カラー5</span>
                                    <input type="radio" name="color" value="6" {{ $color_checked[6] }}><span class="color6">カラー6</span>
                                </div><!-- color -->

                                @if ($errors->has('color'))
                                <p class="text-danger">{{ $errors->first('color') }}</p>
                                @endif
                            </div><!-- tag col -->

                            <div class="content offset-lg-1 col-lg-10">
                                <label for="content">内容</label><br>
                                <input type="text" class="" name="content" id="content" value="{{ old('content', $plan_data['content']) }}"><br>

                                @if ($errors->has('content'))
                                <p class="text-danger">{{ $errors->first('content') }}</p>
                                @endif
                            </div><!-- content -->

                            <div class="detail offset-lg-1 col-lg-10">
                                <label for="detail">詳細 <span>※任意</span></label><br>
                                <textarea name="detail" id="detail" rows="8" cols="40" wrap="soft">{{ old('detail', $plan_data['detail']) }}</textarea><br>

                                @if ($errors->has('detail'))
                                <p class="text-danger">{{ $errors->first('detail') }}</p>
                                @endif
                            </div><!-- detail -->

                            @if ($plan_data['share_users'])
                            <div class="share offset-lg-1 col-lg-10">
                                <h2>予定共有</h2>
                                <div class="share_users">
                                    <div class="radio-wrapper">
                                        <input type="radio" id="disallow" name="share_users" value="0" checked>
                                        <label for="disallow">共有しない</label>
                                    </div>
                                    <div class="radio-wrapper">
                                        <input type="radio" id="allow" name="share_users" value="1">
                                        <label for="allow">共有する</label>
                                    </div>

                                    <div id="share-checkbox" class="hide">
                                        @foreach ($plan_data['share_users'] as $share_user)
                                        <label><input type="checkbox" name="share_user[]" value="{{$share_user['share_id']}}">{{$share_user['name']}}</label>
                                        @endforeach
                                    </div>
                                </div>
                            </div><!-- detail -->
                            @endif

                            @yield('id')

                            <div class="btn-wrap offset-lg-1 col-lg-10">
                                @yield('btn')
                                <button type="submit" class="btn btn-success float-right">確認</button>  
                            </div><!-- btn-wrap -->
                        </form>
                    </div><!-- card-body -->
                </div><!-- row -->
            </div><!-- card -->
        </div><!-- plan -->
    </div><!-- row -->
</div><!-- container -->
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