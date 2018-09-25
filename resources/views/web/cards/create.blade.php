@section('title', 'クレジットカード登録')
@section('screen.id', 'gl1')
@extends('layouts.web')
@section('web.content')
<div class="title">
  <div class="btn-back">
    <a href="{{ \URL::previous() }}"><img src="/assets/webview/images/back.png" alt=""></a>
  </div>
  <div class="title-name">
    <span>メッセージ一覧</span>
  </div>
  <div class="btn-register header-item">
    <a id="btn-create">登録</a>
  </div>
</div>
<div class="image-main">
  <img src="/assets/webview/images/ic_credit_cards@2x.png" alt="">
</div>
<div class="notify" id="notify">
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
      <input type="text" pattern="[0-9]*" name="number_card" id="number-card" value="" onkeyup="creditValidate()" onkeydown="return numberCardLength(event)">
      <span id="number-card-display">0000 0000 0000 0000</span>
    </div>
  </div>
  <div class="clear"></div>
  <div class="expiration-date border-bottom">
    <span class="left" id="thuong">有効期限</span>
    <div class="date-select right">
      <select name="month" id="month">
        @for ($i = 1; $i < 13; $i++)
        <option value="{{ $i }}">{{ $i }}月</option>
        @endfor
      </select>
        @php
          $currenYear = \Carbon\Carbon::now()->format('Y');
        @endphp
      <select name="year" id="year">
        @for ($i = $currenYear; $i <= $currenYear+20; $i++)
        <option value="{{ $i }}">{{ $i }}年</option>
        @endfor
      </select>
    </div>
  </div>
  <div class="sub-title">
    <p>セキュリティコード</p>
  </div>
  <div class="security-code border-bottom">
    <img src="/assets/webview/images/ic_card_cvv.png" alt="" class="left">
    <input type="text" pattern="[0-9]*" placeholder="3桁または4桁の数字" class="right" name="card_cvv" id="card-cvv" onkeyup="addColor()" onkeydown="return numberCvvLength(event)">
  </div>
  </form>
</div>
@endsection
@section('web.extra_js')
<script src="/assets/webview/js/script.js"></script>
@endsection
