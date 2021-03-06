@section('title', '決済方法選択')
@section('screen.id', 'gi-4-4')
@extends('layouts.web')
@section('web.content')
  <div class="wrap-payment-methods">
    <div class="title-payment-methods">
      <p>お支払方法を選択してください。</p>
    </div>
    <div class="wrap-radio-payment-methods">
      <ul>
        <li id="credit-method" onclick="window.location.href = '{{ route('purchase.index', ['point'
         => request()->point])}}'">
          <span>クレジットカード</span>
          <img src="/assets/web/images/common/next.svg" alt="" class="right">
        </li>
        <div class="clear"></div>
        <li id="transfer-method" onclick="window.location.href = '{{ route('guest.transfer', ['point'
         => request()->point])}}'">
          <span>銀行振込</span>
          <img src="/assets/web/images/common/next.svg" alt="" class="right">
        </li>
      </ul>
    </div>
  </div>
@endsection
