@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row">
        <div class="share col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header text-center">
                    <h2 class="title">検索結果</h2>
                </div>

                <div class="card-body">
                    <div class="request-user">
                        <dl>
                            <dt>ユーザーID</dt>
                            <dd>{{ $user_result['share_id'] }}</dd>
                            <dt>名前</dt>
                            <dd>{{ $user_result['name'] }}</dd>
                        </dl>
                    </div>

                        <form method="POST" action="/share/send" class="float-right">
                        @csrf
                        <input type="hidden" name="share_id" value="{{ $user_result['share_id'] }}">
                        <button class="btn btn-success">共有申請する</button>
                    </form>
                        <a href="/share" class="btn btn-secondary">戻る</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection