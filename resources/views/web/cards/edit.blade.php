@section('title', 'クレジットカード登録')
@section('screen.id', 'gl1')
@extends('layouts.web')
@section('web.content')
<div class="title">
  @php
    if (\Session::has('backUrl')) {
      $backUrl = \Session::get('backUrl')[0];
    } else {
      $backUrl = \URL::previous();
    }
  @endphp
  <div class="btn-back">
    <a href="{{ \URL::previous() }}"><img src="/assets/webview/images/back.png" alt=""></a>
  </div>
  <div class="title-name">
    <span></span>
  </div>
  <div class="btn-register header-item">
    <a id="btn-create">完了</a>
  </div>
</div>
<div class="image-main">
  <img src="/assets/web/images/card/allCard.png" alt="">
</div>
<div class="notify">
  <span></span>
</div>
<div class="content">
  <form action="" id="payment-form" method="POST">
    <div class="sub-title">
      <p>カード情報</p>
    </div>
  <div class="card-number border-bottom">
    <span class="left">カード番号</span>
    <div class="right number">
      <span id="error">カード番号を正しく入力してください</span>
      <input type="hidden" value="{{ $backUrl }}" id="back-url">
      <input type="tel" pattern="[0-9]*" name="number_card" id="number-card" onkeyup="creditValidate()" onkeydown="return numberCardLength(event)">
      <span id="number-card-display" class="old-card">下4桁{{ $card->last4 }}</span>
    </div>
  </div>
  <div class="clear"></div>
  <div class="expiration-date border-bottom">
    <span class="left">有効期限</span>
    <div class="date-select right">
      <select name="month" id="month">
        @for ($i = 1; $i < 13; $i++)
        <option value="{{ $i }}" {{ ($card->exp_month == $i) ? 'selected' : '' }}>{{ $i }}月</option>
        @endfor
      </select>
        @php
          $currenYear = \Carbon\Carbon::now()->format('Y');
        @endphp
      <select name="year" id="year">
        @for ($i = $currenYear; $i <= $currenYear+20; $i++)
        <option value="{{ $i }}" {{ ($card->exp_year == $i) ? 'selected' : '' }}>{{ $i }}年</option>
        @endfor
      </select>
    </div>
  </div>
  <div class="sub-title">
    <p>セキュリティコード</p>
  </div>
  <div class="security-code border-bottom">
    <img src="/assets/webview/images/ic_card_cvv.png" alt="" class="left">
    <input type="tel" pattern="[0-9]*" placeholder="3桁または4桁の数字" class="right" name="card_cvv" id="card-cvv" onkeyup="addColor()" onkeydown="return numberCvvLength(event)">
  </div>
  </form>
</div>
@endsection
@section('web.extra_js')
<script src="/assets/webview/js/script.js"></script>
@endsection
