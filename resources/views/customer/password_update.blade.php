@extends('layouts.customer')

@section('title', 'パスワード変更')

@section('content_customer')
<div class="offset-md-2 col-md-8">
    <form id="mainform" method="POST" action="{{ route('customer.password.store') }}">
        @csrf
        <label for="password_now" class="name-label label">現在のパスワード</label><br>
        <input type="password" name="password_now"></input><br>
        @if ($errors->has('password_now'))
            <p class="text-danger">{{ $errors->first('password_now') }}</p>
        @endif

        <div class="new">
            <label for="password_new" class="name-label label">新しいパスワード</label><br>
            <input type="password" name="password_new"></input><br>
            @if ($errors->has('password_new'))
                <p class="text-danger">{{ $errors->first('password_new') }}</p>
            @endif

            <label for="password_new_confirm" class="name-label label">新しいパスワード（確認用）</label><br>
            <input type="password" name="password_new_confirm"></input><br>
            @if ($errors->has('password_new_confirm'))
                <p class="text-danger">{{ $errors->first('password_new_confirm') }}</p>
            @endif
        </div><!-- new -->
    </form>
</div><!-- col -->

<button type="submit" id="mainform" class="btn btn-success float-right change"
onclick="clickEvent();">更新</button>
<script>
    function clickEvent(){
    document.forms.mainform.submit();
    }
</script>
@endsection


@section('btn_footer')
<a href="{{ route('customer.index') }}" class="logout btn btn-secondary">戻る</a>
@endsection