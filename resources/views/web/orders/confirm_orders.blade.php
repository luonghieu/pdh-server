@section('title', '予約内容のご確認')
@section('screen.id', 'gl3')
@section('screen.class', 'ge3')
@extends('layouts.web')
@section('web.content')
  <a href="javascript:void(0)" id="confirm-order-submit" class="gtm-hidden-btn" onclick="dataLayer.push({
          'userId': '<?php echo Auth::user()->id; ?>',
          'event': 'callbooking_complete'
          });"></a>
  <div class="settlement-confirm">
    <section class="details-list">
      <div class="details-header__title">予約内容</div>
        <div class="details-list-box">
          <ul class="details-header__list">
            <li><i><img src="{{ asset('assets/web/images/common/map.svg') }}"></i><p class="word18 address-detail"></p></li>
            <li><i><img src="{{ asset('assets/web/images/common/clock.svg') }}"></i>
              <p class="time-detail-call"></p>
            </li>
            <li><i><img src="{{ asset('assets/web/images/common/glass.svg') }}"></i>
              <p class="duration-call"></p>
            </li>
            <li><i><img src="{{ asset('assets/web/images/common/diamond.svg') }}"></i>
              <p class="cast-numbers-call"></p>
            </li>
          </ul>
          <div class="btn2-s"><a href="{{ route('guest.orders.call') }}">変更</a></div>
        </div>
    </section>
    <section class="details-list">
      <div class="details-list__line"><p></p></div>
      <div class="details-list__header">
        <div class="details-header__title">今日の気分</div>
      </div>
      <div class="details-list__content show">
        <div class="details-list-box">
          <ul class="details-info-list"></ul>
          <div class="btn2-s"><a href="{{ route('guest.orders.get_step2') }}">変更</a></div>
        </div>
      </div>
    </section>

{{--     <section class="details-list details-shimei">
      <div class="details-list__line"><p></p></div>
      <div class="details-list__header">
        <div class="details-header__title">指名リクエスト</div>
      </div>
      <div class="details-list__content show">
        <div class="details-list-box">
          <div class="details-list-box">
              <p class="total-nominated-call"></p>
              <ul class="details-list-box__pic"></ul>
          </div>
          <div class="btn2-s"><a href="{{ route('guest.orders.get_step3') }}">変更</a></div>
        </div>
      </div>
    </section> --}}

    @if (Auth::check() && Auth::user()->is_guest && Auth::user()->is_multi_payment_method)
      <input type="hidden" id="current-point" value="">
      <section class="details-list">
        <div class="details-list__line"><p></p></div>
        <div class="details-list__header">
          <div class="details-header__title">決済方法選択</div>
        </div>
        <div class="grade-list transfer-order">
          <div class="transfer-left">
            <label>
              <input type="radio" name="transfer_order" class="grade-radio" value="{{ \App\Enums\OrderPaymentMethod::CREDIT_CARD }}" checked="checked" >
            </label>
            <p>クレジットカード</p>
          </div>
          <div class="transfer-right">
            <label>
              <input type="radio" name="transfer_order" class="grade-radio" value="{{ \App\Enums\OrderPaymentMethod::DIRECT_PAYMENT }}">
            </label>
            <p>銀行振込</p>
          </div>
        </div>
      </section>
    @endif
    <div id="show-registered-card">
      <section class="details-list">
        <div class="details-list__line"><p></p></div>
        <div class="details-list__header">
          <div class="details-header__title">クレジットカードの登録</div>
        </div>
        <div class="details-list__content show">
          <div class="details-list-box">
            <div class="btn2-s">
              @if(!Auth::user()->is_card_registered)
              <a class="link-arrow link-arrow--left link-credit-card tc-verification-link inactive-button-order" href="#" style="color: #222222;">未登録</a>
              @else
              <a class="link-arrow link-arrow--left link-credit-card tc-verification-link" href="#" style="color: #222222;">登録済み</a>
              @endif
            </div>
          </div>
        </div>
      </section>
    </div>
    <!-- coupons -->
    <div id="show-coupons-order"></div>

    <section class="details-total">
      <div class="details-list__line"><p></p></div>

      <!-- show-point-coupon -->
      <div id="show-point-coupon"></div>

      <div class="details-total__content">
        <div class="details-list__header">
          <div class="details-header__title">合計</div>
        </div>
        <div class="details-total__marks" id="total_point-order-call"></div>
      </div>

      <input type="hidden" id="temp_point_order_call" value="">
      <input type="hidden" id="point_used" value="">
    </section>
  </div>
  <div class="reservation-policy">
    <label class="checkbox">
      <input type="checkbox" class="cb-cancel">
      <span class="sp-disable" id="sp-cancel"></span>
      <a href="{{ route('guest.orders.cancel') }}">キャンセルポリシー</a>
      に同意する
    </label>
  </div>
  <button type="button" class="form_footer ct-button disable" id="btn-confirm-orders" disabled="disabled">
    予約リクエストを確定する
  </button>
  <section class="button-box">
    <label for="orders" class="lb-orders"></label>
  </section>
@endsection

@section('web.extra')
  <div class="modal_wrap modal-confirm">
    <input id="orders" type="checkbox">
    <div class="modal_overlay">
      <label for="orders" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
        <div class="text-box">
          <h2>予約を確定しますか？</h2>
        </div>
        <div class="close_button-box">
          <div class="close_button-block">
            <label for="orders" class="close_button  left ">キャンセル</label>
          </div>
          <div class="close_button-block">
            <label for="orders" class="close_button right sb-form-orders">確定する</label>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal_wrap">
    <input id="order-call-popup" type="checkbox">
    <div class="modal_overlay">
      <label for="order-call-popup" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn1">
        <div class="text-box show-message-order-call">
          <h2 class="init-padding"></h2>
          <p></p>
        </div>
        <label for="order-call-popup" class="close_button" id="label-show-message">OK</label>
      </div>
    </div>
  </div>

  <div class="modal_wrap">
    <input id="order-done" type="checkbox">
    <div class="modal_overlay">
      <label for="mypage" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn1">
        <div class="text-box show-message-order-call">
          <h2>予約が完了しました</h2>
          <p>ただいまキャストの調整中です<br>予約状況はホーム画面の予約一覧をご確認ください</p>
        </div>
        <label for="order-done" class="close_button order-done">OK</label>
      </div>
    </div>
  </div>

  <div class="modal_wrap">
    <input id="invite-code-ended" type="checkbox">
    <div class="modal_overlay">
      <div class="modal_content modal_content-btn1">
        <div class="text-box messeage-invite-code-ended">
          <p>
            友達招待キャンペーンは3/13を持ちまして <br />
            終了させていただきました <br />
            ご入力いただきましたクーポンコードは <br />
            すべて無効となっております <br />
            ご不明点などございましたらお問い合わせください
          </p>
        </div>
        <label for="invite-code-ended" class="close_button">OK</label>
      </div>
    </div>
  </div>

  <div class="modal_wrap">
    <input id="md-require-card" type="checkbox">
    <div class="modal_overlay">
      <label for="md-require-card" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn1">
        <div class="text-box card-expired">
          <h2>クレジットカードを <br>登録してください</h2>
          <p>※キャストと合流するまで <br>料金は発生しません</p>
        </div>
        <label for="md-require-card" class="close_button lable-register-card">クレジットカードを登録する</label>
      </div>
    </div>
  </div>

  <script>
    var avatarsDefault = "<?php echo asset('assets/web/images/gm1/ic_default_avatar@3x.png'); ?>";
    var linkStepOne = "<?php echo route('guest.orders.call'); ?>";
  </script>

@endsection

@section('web.script')
  <script>
    $(function(){

      var $setElm = $('.word18');
      var cutFigure = '17'; // カットする文字数
      var afterTxt = ' …'; // 文字カット後に表示するテキスト

      $setElm.each(function(){
        var textLength = $(this).text().length;
        var textTrim = $(this).text().substr(0,(cutFigure))

        if(cutFigure < textLength) {
            $(this).html(textTrim + afterTxt).css({visibility:'visible'});
        } else if(cutFigure >= textLength) {
            $(this).css({visibility:'visible'});
        }
      });
    });
  </script>
@endsection
