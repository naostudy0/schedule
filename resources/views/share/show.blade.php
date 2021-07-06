@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row">
            <div class="share col-md-8 offset-md-2">
                @if (session('request'))
                    <div class="request-message">
                        <a href="#request">{{ session('request') }}</a>
                    </div><!-- flash-message -->
                @endif
                @if (session('flash_msg'))
                    <div class="flash-message">
                        {{ session('flash_msg') }}
                    </div><!-- flash-message -->
                @endif

                <div class="card">
                    <div class="card-header text-center">
                        <h2 class="title">予定共有管理</h2>
                    </div><!-- card-header -->

                    <section>
                        <h3>ステータス</h3>
                        <div class="part">
                            <i class="fas fa-exchange-alt float-right id-change-btn"></i>
                            <dl class="float-left">
                                <dt class="desc">あなたのユーザーID</dt>
                                <dd>{{ $share['id'] }}</dd>
                            </dl>

                            <div class="clear"></div>

                            <div id="id-change" class="hide">
                                <p class="desc">ユーザーIDを変更</p>

                                <div class="id-change">
                                    <div class="float-left">
                                        <form method="post" action="/share">
                                        @csrf
                                        <input name="share_id" required><!--
                                            --><button type="submit" class="btn btn-primary">変更</button>
                                        </form>
                                    </div>

                                    <form method="post" action="/share" class="random">
                                        @csrf
                                        <input type=hidden name="random" value="random">
                                        <button type="submit" class="btn btn-secondary" id="auto">自動作成</button>
                                    </form> 
                                    <p class="attention-wrap"><span class="attention">ユーザーIDは8文字以内のアルファベット・数字で設定できます。既に他のユーザーが使用しているユーザーIDは使用できません。<span></p>
                                </div>
                            </div>
                        </div>

                        <div class="part">
                        <dl class="float-left">
                            <dt class="desc">ユーザー検索に表示 <i class="far fa-question-circle q-a"></i></dt>
                            <dd>{{ $share['msg'] }}</dd>
                        </dl>
                        
                        <form method="post" action="/share">
                            @csrf
                            <input type=hidden name="share_permission" value="share_permission">
                            <button type="submit" class="btn btn-secondary float-right permission-btn">{{ $share['btn'] }}</button>
                        </form>
                        <div class="clear"></div>
                        <p class="attention-wrap hide" id="accept-desc"><span class="attention">他のユーザーが《ユーザー検索》で「あなたのユーザーID」を入力したときに、検索結果に「あなたの名前」が表示されるかどうかを設定できます。<br>あなたが他のユーザーからの申請を許可しない限り、予定が共有されることはありません。<br>なお、予定は個別に共有するかどうかを設定できます。
                        <br>また「許可しない」に変更した場合でも、既に予定共有しているユーザーは解除されません。
                        </span></p>

                        </div>
                    </section>

                    <section>
                        <h3>予定共有</h3>
                        <div class="part">
                        <h4 class="desc" id="request">予定を共有しているユーザー</h4>
                            @if (!$share_users_data)
                                <p>予定を共有しているユーザーはいません。</p>
                            @else
                                @foreach ($share_users_data as $share_user)
                                    <div class="share-users">
                                        <dl class="float-left">
                                            <dt>ユーザーID</dt>
                                            <dd>{{ $share_user['share_id'] }}</dd>
                                            <dt>名前</dt>
                                            <dd>{{ $share_user['name'] }}</dd>
                                        </dl>
                                        <form action="{{ route('share.delete') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="share_id" value="{{ $share_user['share_id'] }}">
                                            <button type="submit" class="btn btn-secondary float-right permission-btn share-delete">解除</button>
                                        </form>
                                        <div class="clear"></div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="part">
                            <h4 class="desc" id="request">あなたへの申請</h4>
                            @if (!$requested_user_data)
                                <p>現在、申請はありません。</p>
                            @else
                                @foreach ($requested_user_data as $requested_user)
                                <div class="share-users">
                                    <dl class="share-users-inner">
                                        <dt>ユーザーID</dt>
                                        <dd>{{ $requested_user['share_id'] }}</dd>
                                        <dt>名前</dt>
                                        <dd>{{ $requested_user['name'] }}</dd>
                                    </dl>

                                    <div class="permit-buttons">
                                        <form action="{{ route('share.request.permit') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="share_id" value="{{ $requested_user['share_id'] }}">
                                            <input type="hidden" name="permit" value="1">
                                            <button type="submit" class="btn btn-success float-right permission-btn">承認</button>
                                        </form>

                                        <form action="{{ route('share.request.permit') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="share_id" value="{{ $requested_user['share_id'] }}">
                                            <input type="hidden" name="permit" value="2">
                                            <button type="submit" class="btn btn-danger float-right permission-btn">却下</button>
                                        </form>
                                    </div>

                                    <div class="clear"></div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="part">
                            <h4 class="desc">申請中のユーザー</h4>
                            @if (!$requesting_user_data)
                                <p>申請中のユーザーはいません。</p>
                            @else
                                @foreach ($requesting_user_data as $requesting_user)
                                <div class="share-users">
                                    <dl>
                                        <dt>ユーザーID</dt>
                                        <dd>{{ $requesting_user['share_id'] }}</dd>
                                        <dt>名前</dt>
                                        <dd>{{ $requesting_user['name'] }}</dd>
                                    </dl>
                                    <form action="{{ route('share.send.delete') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="share_id" value="{{ $requesting_user['share_id'] }}">
                                        <button type="submit" class="btn btn-secondary delete">取消</button>
                                    </form>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </section>

                    <section>
                        <h3>ユーザー検索</h3>
                        <p>予定を共有したいユーザーのユーザーIDを入力してください。</p>
                        <form method="post" action="/share/request">
                            @csrf
                            <input type="text" name="share_id" required><!--
                         --><button type="submit" class="btn btn-primary">検索</button>
                        </form>
                    </section>
                </div><!-- card -->
            </div><!-- share col -->
        </div><!-- row -->
    </div><!-- container -->
@endsection


@section('script')
<script>
$(function(){
    $('.id-change-btn').on('click', function(){
        $('#id-change').slideToggle();
        $(this).children('i').toggleClass('open');
    });

    $('#auto').on('click', function(){
        if(!confirm('ユーザーIDを自動作成してよろしいですか？\n（既にユーザーIDが設定されている場合は上書きされます。）')){
            return false;
        } else {
            return true;
        }
    });

    $('.q-a').on('click', function(){
        $('#accept-desc').toggleClass('hide');
    });

    $('.delete').on('click', function(){
        if(!confirm('取消してよろしいですか？')){
            return false;
        } else {
            return true;
        }
    });

    $('.share-delete').on('click', function(){
        if(!confirm('解除すると、過去に共有していた予定がすべて解除されます。\n共有を解除してよろしいですか？')){
            return false;
        } else {
            return true;
        }
    });
});
</script>
@endsection