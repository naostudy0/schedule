@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card register">
                <div class="card-header">仮会員登録確認</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 text-md-right">メールアドレス</label>

                            <div class="col-md-6">
                                <span>{{$email}}</span>
                                <input type="hidden" name="email" value="{{$email}}">
                            </div><!-- col -->
                        </div><!-- form-group -->



                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <a href="{{ route('register') }}" class="btn btn-secondary">修正</a>
                                <button type="submit" formaction="{{ route('register') }}"class="btn btn-primary">仮登録</button>
                            </div>
                        </div><!-- form-group -->
                    </form>
                </div><!-- card-body -->
            </div><!-- card -->
        </div><!-- col -->
    </div><!-- row -->
</div><!-- container -->
@endsection
