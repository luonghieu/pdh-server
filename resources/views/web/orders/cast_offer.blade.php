@section('title', 'キャスト予約')
@section('screen.id', 'offer')
@section('web.extra_css')
<link rel="stylesheet" href="{{ mix('assets/web/css/cast.min.css') }}">
<link rel="stylesheet" href="{{ mix('assets/web/css/ge_4.min.css') }}">
@endsection
@extends('layouts.web')
@section('web.extra')
<div class="modal_wrap modal-confirm-cast-offer">
    <input id="cast-offer-popup" type="checkbox">
    <div class="modal_overlay">
      <label for="cast-offer-popup" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
        <div class="text-box">
          <h2 style="padding-top: 0px;">予約を確定しますか？</h2>
          <p>確定後のキャンセルは<br>キャンセルポリシーに基づいて<br>キャンセル料が発生します</p>
        </div>
        <div class="close_button-box">
          <div class="close_button-block">
            <label for="cast-offer-popup" class="close_button  left">キャンセル</label>
          </div>
          <div class="close_button-block" id="create-cast-offer">
            <label class="close_button right">確定する</label>
          </div>
        </div>
        </div>
    </div>
</div>

<div class="modal_wrap">
  <input id="err-offer" type="checkbox">
  <div class="modal_overlay">
    <label for="err-offer" class="modal_trigger" id="lb-err-offer"></label>
    <div class="modal_content modal_content-btn1">
      <div class="text-box" id="err-offer-message">
        <h2></h2>
        <p></p>
      </div>
      <label for="err-offer" class="close_button">OK</label>
    </div>
  </div>
</div>

<div class="modal_wrap">
  <input id="timeout-offer" type="checkbox">
  <div class="modal_overlay">
    <label for="err-offer" id="lb-err-offer"></label>
    <div class="modal_content modal_content-btn1">
      <div class="text-box" id="timeout-offer-message">
        <h2 style="font-size: 15px;"></h2>
      </div>
      <label for="" class="close_button redirect-mypage" id="close-offer">OK</label>
    </div>
  </div>
</div>

<div class="modal_wrap ">
  <input id="cancel-cast-offer" type="checkbox">
  <div class="modal_overlay">
    <label for="cancel-cast-offer" class="modal_trigger"></label>
    <div class="modal_content modal_content-btn2">
      <div class="text-box">
        <h2 style="padding-top: 0px;">本当にキャンセルしますか？</h2>
      </div>
      <div class="close_button-box">
        <div class="close_button-block">
          <label for="cancel-cast-offer" class="close_button  left">キャンセル</label>
        </div>
        <div class="close_button-block" id="canceled-cast-offer">
          <label for="cancel-cast-offer" class="close_button right ">はい</label>
        </div>
      </div>
      </div>
  </div>
</div>

@endsection
@section('web.content')
  @if(isset($order))
  <div class="page-header-timeline">
        <h1 class="text-bold">キャスト予約</h1>
    </div>
  <div class="offer-wrap">
    <section class="reservation-cast">
      <div class="reservation-cast__header">
        <p class="reservation-cast__title">予約リクストがとどきました!</p>
      </div>
      <div class="reservation-cast__content">
        <div class="reservation-cast__info">
          <div class="reservation-cast__photo">
            @if($order->nominees[0]->avatars)
              @if (@getimagesize($order->nominees[0]->avatars[0]->thumbnail))
                <img style="border-radius: 15px;" src="{{ $order->nominees[0]->avatars[0]->thumbnail }}" alt="">
                @else
                <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
              @endif
            @else
            <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
            @endif
          </div>
          <div class="reservation-cast__name">
            <p>{{ $order->nominees[0]->nickname }}</p>
            <p>{{ $order->nominees[0]->age }}歳</p>
          </div>
          <div class="reservation-cast__level">
            @php
              $class = '';
              switch ($order->nominees[0]->class_id) {
                  case 1:
                      $class = 'bronz-class';
                      break;
                  case 2:
                      $class = 'platinum-class';
                      break;
                  case 3:
                      $class = 'daiamond-class';
                      break;
              }
            @endphp
            <p class="reservation-cast__daiamond {{ $class }}">{{ $order->nominees[0]->castClass->name }}</p>
            <p class="reservation-cast__pric">30分あたりの料金<span style="font-weight: bold;">{{ number_format($order->nominees[0]->cost ) }} P</span>
            </p>
          </div>
        </div>
        <div class="reservation-info">
          <div class="reservation-info__date">
            <div class="reservation-info__day">
              {{ Carbon\Carbon::parse($order->date)->format('m月d日') }}
              ({{ dayOfWeek()[Carbon\Carbon::parse($order->date)->dayOfWeek] }})
            </div>
            <div class="reservation-info__time">
              {{ Carbon\Carbon::parse($order->start_time)->format('H:i') }}~<span>({{ $order->duration }}時間) </span>
              <input type="hidden" id="duration-cast-offer" value="{{ $order->duration }}">
            </div>
          </div>
          <div class="reservation-info__place">{{ $order->address }}</div>
          <input type="hidden" id="prefecture-cast-offer" value="{{ $order->prefecture_id }}">
          <input type="hidden" id="address-cast-offer" value="{{ $order->address }}">
          <input type="hidden" id="cast_offer-id" value="{{ $order->id }}">
          <input type="hidden" id="cast-id" value="{{ $order->nominees[0]->id }}">
          <input type="hidden" id="class_cast-id" value="{{ $order->nominees[0]->castClass->id }}">
          <input type="hidden" id="order-status" value="{{ $order->status }}">
          <input type="hidden" id="date-cast-offer" value="{{ Carbon\Carbon::parse($order->date)->format('Y-m-d') }}">
          <input type="hidden" id="time-cast-offer" value="{{ Carbon\Carbon::parse($order->start_time)->format('H:i') }}">
        </div>
      </div>
    </section>
    <section class="reservation-settlement">
      <div class="portlet">
        <div class="portlet-header">
          <h2 class="portlet-header__title">クーポン</h2>
        </div>
        <div class="portlet-content">
          <div class="offer-coupon">
            <div class="offer-coupon__header">
              <div class="selectbox">
                <select id="cast-offer-coupon" name="">
                  <option value="">クーポンを使用しない</option>
                  @if(count($coupons))
                    @foreach($coupons as $coupon)
                      <option value="{{ $coupon['id'] }}">{{ $coupon['name'] }}</option>
                    @endforeach
                  @endif
                </select><i></i>
              </div>
            </div>
            <div class="offer-coupon__content">
              <p class="offer-coupon__text"></p>
            </div>
          </div>
        </div>
      </div>
      @if (Auth::check() && Auth::user()->is_guest && Auth::user()->is_multi_payment_method)
      <div class="portlet">
        <div class="portlet-header">
          <h2 class="portlet-header__title">決済方法選択</h2>
        </div>
        <div class="portlet-content">
          <div class="grade-list">
            <label>
              <input class="grade-radio" value="{{ \App\Enums\OrderPaymentMethod::CREDIT_CARD }}" type="radio" name="payment_method" checked="checked"><span>クレジットカード</span>
            </label>
            <label>
              <input class="grade-radio" value="{{ \App\Enums\OrderPaymentMethod::DIRECT_PAYMENT }}" type="radio" name="payment_method"><span>銀行振込</span>
            </label>
          </div>
        </div>
      </div>
      @endif
      <div class="portlet">
        <div id="show-card-cast-offer">
          <div class="portlet-header">
            <h2 class="portlet-header__title">クレジットカードの登録</h2>
          </div>
          @if(!Auth::user()->is_card_registered)
          <a class="link-arrow link-arrow--left tc-verification-link inactive-button-order" href="#" style="color: #222222;">
          未登録</a>
          @else
          <a class="link-arrow link-arrow--left tc-verification-link" href="#" style="color: #222222;">登録済み</a>
          @endif
        </div>
      </div>
      <div class="portlet">
        <div class="portlet-content">
          <div class="reservation-attention"><a href="{{ route('guest.orders.nominate_step2') }}">予約前の注意事項</a>
          </div>
          <div class="reservation-total">
            <div class="reservation-total__content">
              <div class="reservation-total__item">通常料金<span>{{ number_format($order->temp_point ) }} P</span>
              </div>
              <div class="reservation-total__item" id="point-sale-coupon"></div>
              <div class="reservation-total__sum" id="current-point">
                合計<span>{{ number_format($order->temp_point ) }} P</span>
              </div>
            </div>
            <input type="hidden" name="total_point" id="total-point-cast-offer">
            <input type="hidden" name="current_point" id="current-point-cast-offer" value="{{ $order->temp_point }}">
          </div>

          <div class="reservation-policy">
            <label class="checkbox">
              <input type="checkbox" class="checked-cast-offer" name="">
              <span class="sp-disable" id="sp-cancel"></span>
              <p><a href="{{ route('guest.orders.cancel') }}">キャンセルポリシー</a> に同意する</p>
            </label>
          </div>
          <div class="reservation-button">
            <button class="button button--green" id="btn-cancel-offer">キャンセル</button>
            <button class="button button--green disable" id="confirm-cast-order" disabled="disabled">予約リクエストを確定する</button>
          </div>
        </div>
      </div>
    </section>
  </div>
  @endif
@endsection
