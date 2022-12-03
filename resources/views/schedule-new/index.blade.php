@extends('layouts.base')

@section('style')
<link href="{{ asset('css/app.css') }}?{{ date('Ymd') }}" rel="stylesheet">
<link href="{{ asset('css/base.css') }}?{{ date('Ymd') }}" rel="stylesheet">
<link href="{{ asset('css/schedule_new.css') }}?{{ date('Ymd') }}" rel="stylesheet">
@endsection

@section('head_script')
<script src="{{ asset('js/app.js') }}" defer></script>
@endsection

@section('content')
<router-view />
@endsection