@section('title', '本人確認')
@section('screen.id', 'gf3')
@extends('layouts.web')
@section('web.extra_css')
  <link rel="stylesheet" href="{{ mix('assets/web/css/gf_3.min.css') }}">
@endsection
@section('web.content')
  <div class="modal_wrap">
    <input id="triggerPhoneNumberIncorrect" type="checkbox">
    <div class="modal_overlay phone-number-incorrect">
      <label for="triggerPhoneNumberIncorrect" class="modal_trigger" ></label>
      <div class="modal_content modal_content-btn3">
        <div class="content-in">
         <h2></h2>
        </div>
      </div>
    </div>
  </div>
  <div class="page-header sms-header">
    <h1 class="text-bold">本人確認</h1>
  </div>

  <div class="cast-search">
    <section class="search">
      <div class="search-header sms-header">
        <h2 class="sms-title">SMSを利用して本人確認を行います</h2>
      </div>
      <div class="input-phone">
        <input type="tel" pattern="[0-9]*" id="phone-number-verify" onkeydown="return numberPhoneLength(event)" placeholder="電話番号を入力">
      </div>
    </section>
  </div>

  <a href="javascript:void(0)" id="send-number" class="number-phone-verify-wrong" data-href="{{ route('verify.code') }}">規約に同意してSMSを送る</a>

  <div class="page-header sms-header">
    <h6 class="text-verify"><a href="/service/law">利用規約はこちら</a></h6>
  </div>
  @if(isset($token))
    <script>
        window.localStorage.setItem('access_token', '{{ $token }}');
    </script>
  @endif
@endsection
@section('web.extra_js')
  <script>
    function numberPhoneLength(event)
    {
      var phoneNumber = $('#phone-number-verify').val();
      var phoneNumberLen = phoneNumber.length;
      if (phoneNumberLen < 11 || event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39) {
        return true;
      } else {
        return false;
      }
    }
  </script>
@endsection
