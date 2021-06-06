@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card register">
                <div class="card-header">本会員登録</div>

                @isset($message)
                    <div class="card-body">
                        {{$message}}
                    </div><!-- card-body -->
                @endisset

                @empty($message)
                    <div class="card-body">
                        <form method="POST" action="{{ route('register.main.check') }}">
                            @csrf

                            <input type="hidden" name="email_token" value="{{ $email_token }}">

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">名前</label>
                                <div class="col-md-6">
                                    <input
                                        id="name" type="text"
                                        class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                        name="name" value="{{ old('name') }}" required>

                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div><!-- form-group row -->

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div><!-- col -->
                            </div><!-- form-group row -->

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                </div><!-- col -->
                            </div><!-- form-group row -->

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        確認画面
                                    </button>
                                </div><!-- col -->
                            </div><!-- form-group row -->
                        </form>
                    </div><!-- card-body -->
                @endempty
            </div><!-- card register -->
        </div><!-- col -->
    </div><!-- row -->
</div><!-- container -->
@endsection