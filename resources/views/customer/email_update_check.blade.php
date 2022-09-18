@extends('layouts.customer')


@section('title')
メールアドレス変更
@endsection


@section('content_customer')
<div class="offset-md-2 col-md-8">
    <form id="mainform" method="POST" action="{{ route('customer.email.confirm') }}">
        @csrf
        <label for="email" class="name-label label">新しいメールアドレス</label><br>
        <input name="email"></input><br>
    </form>

    @if ($errors->has('email'))
    <p class="text-danger">{{ $errors->first('email') }}</p>
    @endif
</div><!-- col -->

<button type="submit" id="mainform" class="btn btn-success float-right change"
onclick="clickEvent();">メール送信</button>
<script>
    function clickEvent(){
        document.forms.mainform.submit();
    }
</script>

@endsection


@section('btn_footer')
<a href="{{ route('customer.index') }}" class="btn btn-secondary">戻る</a>
@endsection