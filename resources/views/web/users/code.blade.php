@section('title', '本人確認')
@section('screen.id', 'gf3')
@extends('layouts.web')
@section('web.extra_css')
  <link rel="stylesheet" href="{{ mix('assets/web/css/gf_3.min.css') }}">
@endsection
@section('web.content')
  <input type="hidden" id="is-verify" value="{{ $isVerify }}">
  <div class="modal_wrap">
    <input id="verify-success" type="checkbox">
    <div class="modal_overlay">
      <label for="verify-success" class="modal_trigger" ></label>
      <div class="modal_content modal_content-btn3">
        <div class="content-in">
          @if (Auth::user()->is_verified)
          <h2>{{ trans('messages.phone_update_success') }}</h2>
          @else
          <h2>{{ trans('messages.user_verify_success') }}</h2>
          @endif
        </div>
      </div>
    </div>
  </div>
  <div class="modal_wrap">
    <input id="triggerVerifyIncorrect" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn1">
        <div class="text-box">
          <p>認証コードが間違っています</p>
        </div>
        <label for="triggerVerifyIncorrect" class="close_button" id="alert-code-wrong">OK</label>
      </div>
    </div>
  </div>

  <div class="modal_wrap">
    <input id="triggerAcceptResenCode" type="checkbox">
    <div class="modal_overlay">
      <label for="triggerAcceptResenCode" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
        <div class="text-box">
          <p>SMSを再送しますか？</p>
        </div>
        <div class="close_button-box">
          <div class="close_button-block">
            <label for="triggerAcceptResenCode" class="close_button left">いいえ</label>
          </div>
          <div class="close_button-block">
            <label class="close_button right resend-code" >はい</label>
          </div>
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
      <div class="enter-number">
        <input type="number" id="code-number-1" onkeydown="return numberCodeLength(event, 1)">
        <input type="number" id="code-number-2" onkeydown="return numberCodeLength(event, 2)">
        <input type="number" id="code-number-3" onkeydown="return numberCodeLength(event, 3)">
        <input type="number" id="code-number-4" onkeydown="return numberCodeLength(event, 4)">
      </div>

    </section>
  </div>

  <div class="page-header sms-header">
    <h5>SMSが届かない場合</h5>
    <h6 class="text-verify resend-code"><a href="javascript:void(0)">SMSを再送する</a></h6>
  </div>

@endsection
@section('web.extra_js')
<<<<<<< HEAD
=======
  <script src="{{ asset('assets/web/js/jRange/jquery.range.js') }}"></script>
  <script src="{{ asset('assets/web/js/gf-3.js') }}"></script>
>>>>>>> develop
  <script>
    function numberCodeLength(event, num)
    {
      var codeNumber = $('#code-number-'+num).val();
      var codeNumberLen = codeNumber.length;

<<<<<<< HEAD
      if (codeNumberLen < 1 || event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39) {
=======
      if (codeNumberLen < 1 || event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39)
      {
>>>>>>> develop
        return true;
      } else {
        return false;
      }
    }
  </script>
@endsection
