@extends('layouts.customer')

@section('title', '登録内容修正')

@section('content_customer')
<div class="offset-md-2 col-md-8">
<form id="mainform" method="POST" action="{{ route('customer.name.store') }}">
    @csrf
    <label for="name" class="name-label label">名前</label><br>
    <input value="{{ Auth::user()->name }}" name="name"></input><br>
</form>

@if ($errors->has('name'))
    <p class="text-danger">{{ $errors->first('name') }}</p>
    @endif
</div>

<button type="submit" id="mainform" class="btn btn-success float-right change"
onclick="clickEvent();">更新</button>
<script>
    function clickEvent(){
        document.forms.mainform.submit();
    }
</script>

@endsection


@section('btn_footer')
<a href="{{ route('customer.index') }}" class="btn btn-secondary">戻る</a>
@endsection