@section('title', '電話番号を変更する')
@section('screen.id', 'gf3')
@extends('layouts.web')
@section('web.extra_css')
  <link rel="stylesheet" href="{{ mix('assets/web/css/gf_3.min.css') }}">
@endsection
@section('web.content')
<form id="profile-verify-code" action="#" method="GET">
  {{ csrf_field() }}
  <div class="page-header sms-header">
    <h1 class="text-bold">電話番号を変更する</h1>
  </div>
  <div class="cast-search">
    <section class="search">
      <div class="search-header sms-header">
        <h2 class="sms-title">SMSを利用して本人確認を行います</h2>
      </div>
      <div class="input-phone init-height">
        <label class="init-text">現在の電話番号</label>
        <input type="text" name="old_phone" id="old-phone" class="text-old-phone" value="{{ $phone ? $phone : '未設定' }}" disabled="" />
      </div>
      <div class="input-phone init-height init-mt">
        <label class="init-text">新しい電話番号</label>
        <input type="tel" name="phone" id="phone" value="" placeholder="電話番号を入力" />
      </div>
      <label id="phone-error" data-field="phone" class="error error-phone help-block" for="phone"></label>
    </section>
  </div>
  <button type="submit" class="btn-phone-update">上記の番号にSMSを送る</button>
</form>
@endsection
