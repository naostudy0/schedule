@extends('layouts.base')

@section('head_script')
<script src="{{ asset('js/app.js') }}" defer></script>
@endsection

@section('content')
<router-view />
@endsection