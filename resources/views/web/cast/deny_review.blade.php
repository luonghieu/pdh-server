@section('title', '審査結果画面')
@extends('layouts.web')
@section('web.content')
<div class="no-cast">
    <figure><img class="logo-cheers color-green" src="{{ asset('assets/web/images/ic_launcher.png') }}" alt=""></figure>
    <p class="color-green text-center">残念ながら</p>
    <p class="color-green text-center pb-2">審査に通過しませんでした</p>
 </div>
@endsection
