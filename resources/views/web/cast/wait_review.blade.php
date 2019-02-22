@section('title', 'キャスト審査中画面')
@extends('layouts.web')
@section('web.content')
<div class="no-cast">
    <figure><img class="logo-cheers color-green" src="{{ asset('assets/web/images/ic_launcher.png') }}" alt=""></figure>
    <p class="color-green text-center">ただいま審査中です</p>
    <p class="color-green text-center pb-2">しばらくお待ちください</p>
    <p class="text-center">※審査は翌営業日以内に結果をおしらせします</p>
 </div>
@endsection
