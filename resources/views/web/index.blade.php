@section('title', 'Cheers')
@section('screen.id', '')

@extends('layouts.web')
@section('web.content')
@if(!Auth::check())
<a href="{{ route('auth.line') }}">
  <img src="{{ asset('images/btn_login_base.png') }}" alt="">
</a>
@else
<h1>hello, {{ Auth::user()->fullname }}</h1>
@endif

@if($token)
<script>
  window.localStorage.setItem('access_token', '{{ $token }}');
</script>
@endif
@endsection
