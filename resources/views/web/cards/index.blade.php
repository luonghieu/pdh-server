@section('title', 'クレジットカード登録')
@section('screen.id', 'gl1')
@section('controller.id', 'card-index')
@extends('layouts.web')
@section('web.content')
<div class="title">
  <div class="title-name"></div>
  <div class="btn-register header-item">
    <a href="{{ route('credit_card.update') }}" class="btn-redirect-edit">編集</a>
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
        <span id="number-card-display" class="color-show-page">下4桁{{ $card->last4 }}</span>
    </div>
  </div>
  <div class="clear"></div>
  <div class="expiration-date border-bottom">
    <span class="left">有効期限</span>
    <div class="date-select right">
      <select name="month" id="month" disabled>
        @for ($i = 1; $i < 13; $i++)
        <option value="{{ $i }}" {{ ($card->exp_month == $i) ? 'selected' : '' }}>{{ $i }}月</option>
        @endfor
      </select>
        @php
          $currenYear = \Carbon\Carbon::now()->format('Y');
        @endphp
      <select name="year" id="year" disabled>
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
    <input type="text" pattern="[0-9]*" placeholder="3桁または4桁の数字" class="right number-true" name="card_cvv" id="card-cvv" value="***" disabled>
  </div>
  </form>
</div>
@endsection
