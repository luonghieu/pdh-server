@section('title', '電話番号を変更する')
@section('screen.id', 'gf3')
@extends('layouts.web')
@section('web.extra_css')
  <link rel="stylesheet" href="{{ asset('assets/web/css/gf_3.css') }}">
@endsection
@section('web.content')
<form id="profile-verify-code" action="#" method="GET">
  {{ csrf_field() }}
  <div class="page-header sms-header">
    <h1 class="text-bold">本人確認</h1>
  </div>
  <div class="cast-search">
    <section class="search">
      <div class="search-header sms-header">
        <h2 class="sms-title">SMSを利用して本人確認を行います</h2>
      </div>
      <div class="input-phone init-height">
        <label class="init-text">現在の電話番号</label>
        <input type="text" name="old_phone" class="text-old-phone" value="{{ $phone }}" disabled="" />
      </div>
      <div class="input-phone init-height init-mt">
        <label class="init-text">新しい電話番号</label>
        <input type="number" name="phone" id="phone" value="" placeholder="電話番号入力" />
      </div>
      <label id="phone-error" class="error error-phone help-block" for="phone"></label>
    </section>
  </div>
  <button type="submit" id="send-number" class="number-phone-verify-wrong bd-none">上記の番号にSMSを送る</button>
</form>
@endsection
