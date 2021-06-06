@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card register">
                <div class="card-header">本会員登録確認</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register.main.registered') }}">
                        @csrf

                        <input type="hidden" name="email_token" value="{{ $email_token }}">

                        <div class="form-group row">
                            <label for="name" class="col-md-4 text-md-right">名前</label>
                            <div class="col-md-6">
                                <span>{{$user->name}}</span>
                                <input type="hidden" name="name" value="{{$user->name}}">
                            </div>
                        </div><!-- form-group row -->

                        <div class="form-group row">
                            <label for="password" class="col-md-4 text-md-right">パスワード</label>
                            <div class="col-md-6">
                                <span>******（表示されません）</span>
                                <input type="hidden" name="password" value="{{$user->password}}">
                            </div>
                        </div><!-- form-group row -->

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">修正</a>
                                <button type="submit" class="btn btn-primary">本登録</button>
                            </div>
                        </div><!-- form-group row -->
                    </form>
                </div><!-- card-body -->
            </div><!-- card register -->
        </div><!-- col -->
    </div><!-- row -->
</div><!-- container -->
@endsection