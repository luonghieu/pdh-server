@section('title', 'キャスト予約')
@section('screen.id', 'offer')
@section('web.extra_css')
<link rel="stylesheet" href="{{ mix('assets/web/css/cast.min.css') }}">
<link rel="stylesheet" href="{{ mix('assets/web/css/ge_4.min.css') }}">
@endsection
@extends('layouts.web')
@section('web.extra')
<div class="modal_wrap modal-confirm-offer">
    <input id="order-offer-popup" type="checkbox">
    <div class="modal_overlay">
      <label for="order-offer-popup" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
        <div class="text-box">
          <h2>予約を確定しますか？</h2>
          <p>確定後のキャンセルは<br>キャンセルポリシーに基づいて<br>キャンセル料が発生します</p>
        </div>
        <div class="close_button-box">
          <div class="close_button-block">
            <label for="order-offer-popup" class="close_button  left">キャンセル</label>
          </div>
          <div class="close_button-block" id="lb-order-offer">
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
  <input id="admin-edited" type="checkbox">
  <div class="modal_overlay">
    <label for="" class="modal_trigger" id="lb-err-offer"></label>
    <div class="modal_content modal_content-btn1">
      <div class="text-box" id="err-offer-message">
        <h2>予約情報が変更になっているので、再度予約内容を確認してください</h2>
      </div>
      <label for="admin-edited" class="close_button" id="reload-offer">OK</label>
    </div>
  </div>
</div>

<div class="modal_wrap">
  <input id="show-attention" type="checkbox">
  <div class="modal_overlay">
    <label for="show-attention" class="modal_trigger" id="lb-show-attention"></label>
    <div class="modal_content modal_content-btn1">
      <div class="text-box" id="show-attention-message">
        <div class="ge2-4-block popup-attention">
          <h2>延長料金について</h2>
          <p>キャストとの合流後、終了予定時刻を過ぎた場合は自動的に延長となり延長料金が15分単位で発生します。延長料金は下記のとおりです。</p>
          <p>1人あたりの延長料金</p>
          <ul>
            <li><span>ダイヤモンド</span><span>8750P/15分</span></li>
            <li><span>プラチナ</span><span>3500P/15分</span></li>
            <li><span>ブロンズ</span><span>1750P/15分</span></li>
          </ul>
        </div>
      </div>
      <label for="show-attention" class="close_button">OK</label>
    </div>
  </div>
</div>

<div class="modal_wrap">
  <input id="timeout-offer" type="checkbox">
  <div class="modal_overlay">
    <label for="err-offer" id="lb-err-offer"></label>
    <div class="modal_content modal_content-btn1">
      <div class="text-box" id="timeout-offer-message">
        <h2></h2>
      </div>
      <label for="" class="close_button" id="close-offer">OK</label>
    </div>
  </div>
</div>

<div class="modal_wrap">
  <input id="instructions-offer" type="checkbox">
  <div class="modal_overlay">
    <label for="instructions-offer" id="lb-err-offer"></label>
    <div class="modal_content modal_content-btn1">
      <div class="text-box" id="timeout-offer-message">
        <h2>オファーについて</h2>
        <p>2名以上で参加できるキャストから、ゲストに届く特別なオファーです。その他の予約と比較して</p>
        <p>①少ないステップで予約が完了するための予約がラク <br />②指名予約と比較して、単価が安い</p>
        <p>という特徴があります</p>
      </div>
      <label for="instructions-offer" class="close_button" id="close-offer">OK</label>
    </div>
  </div>
</div>
@endsection
@section('web.content')
  <div class="page-header-timeline">
        <h1 class="text-bold">タイムライン</h1>
    </div>
  <div class="offer-wrap">
    <section class="reservation-cast">
      <div class="reservation-cast__header">
        <p class="reservation-cast__title">予約リクストがとどきました!</p>
      </div>
      <div class="reservation-cast__content">
        <div class="reservation-cast__info">
          <div class="reservation-cast__photo">
            <img src="assets/web/images/offer/cast-photo_001.jpg">
          </div>
          <div class="reservation-cast__name">
            <p>★★Satomi★★</p>
            <p>23歳</p>
          </div>
          <div class="reservation-cast__level">
            <p class="reservation-cast__daiamond">ダイヤモンド</p>
            <p class="reservation-cast__pric">30分あたりの料金<span>12,500p</span>
            </p>
          </div>
        </div>
        <div class="reservation-info">
          <div class="reservation-info__date">
            <div class="reservation-info__day">7月2日(土)</div>
            <div class="reservation-info__time">21:30~<span>(3時間) </span>
            </div>
          </div>
          <div class="reservation-info__place">代々木上原</div>
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
                <select id="offerCoupon" name="">
                  <option value="no">クーポンを使用しない</option>
                  <option value="plan1">初回1時間無料キャンペーン</option>
                </select><i></i>
              </div>
            </div>
            <div class="offer-coupon__content">
              <p class="offer-coupon__text">※割引されるポイントは最大100,000Pになります。</p>
            </div>
          </div>
        </div>
      </div>
      <div class="portlet">
        <div class="portlet-header">
          <h2 class="portlet-header__title">決済方法選択</h2>
        </div>
        <div class="portlet-content">
          <div class="grade-list">
            <label>
              <input class="grade-radio" type="radio" name="test"><span>クレジットカード</span>
            </label>
            <label>
              <input class="grade-radio" type="radio" name="test"><span>銀行振込</span>
            </label>
          </div>
        </div>
      </div>
      <div class="portlet">
        <div id="show-card-cast-offer">
          <div class="caption">
            <h2>クレジットカードの登録</h2>
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
          <div class="reservation-attention"><a href="">予約前の注意事項</a>
          </div>
          <div class="reservation-total">
            <div class="reservation-total__content">
              <div class="reservation-total__item">通常料金<span>60,000 P</span>
              </div>
              <div class="reservation-total__item">割引額<span class="red">-30,000 P</span>
              </div>
              <div class="reservation-total__sum">合計<span>33,000P</span>
              </div>
            </div>
          </div>

          <div class="reservation-policy">
            <label class="checkbox">
              <input type="checkbox" class="checked-cast-offer" name="confrim_order_offer">
              <span class="sp-disable" id="sp-cancel"></span>
              <p><a href="{{ route('guest.orders.cancel') }}">キャンセルポリシー</a> に同意する</p>
            </label>
          </div>
          <div class="reservation-button">
            <button class="button button--green">キャンセル</button>
            <button class="button button--green disabled" id="confirm-cast-order" disabled="disabled">予約リクエストを確定する</button>
          </div>
        </div>
      </div>
    </section>
  </div>
@endsection
