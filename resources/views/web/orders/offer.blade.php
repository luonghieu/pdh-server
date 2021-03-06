@section('title', 'キャストからのギャラ飲みオファー')
@section('screen.id', 'ge2-1-x')
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
        <h2 class="init-padding"></h2>
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
<form>
  <div class="instructions-on-offer">
    <span id="popup-intructions-offer">オファーについて</span>
  </div>
  <div class="time-expired">
    <div class="title-time-expired">
      <span>オファーの有効期限まであと・・・</span>
    </div>
    <div class="time-expired-detail">
      <span id="time-countdown"></span>
      <input type="hidden" id="expired-year" value="{{ \Carbon\Carbon::parse($offer->expired_date)->year }}">
      <input type="hidden" id="expired-month" value="{{ \Carbon\Carbon::parse($offer->expired_date)->month }}">
      <input type="hidden" id="expired-date" value="{{ \Carbon\Carbon::parse($offer->expired_date)->day }}">
      <input type="hidden" id="expired-hour" value="{{ \Carbon\Carbon::parse($offer->expired_date)->hour }}">
      <input type="hidden" id="expired-minute" value="{{ \Carbon\Carbon::parse($offer->expired_date)->minute}}">
      @php
        $now = \Carbon\Carbon::now();
        $expiredDate = $offer->expired_date;
        $checkExpired = 1;
        if ($now < $expiredDate) {
          $checkExpired = 0;
        }
      @endphp
      <input type="hidden" id="check-expired" value="{{ $checkExpired }}">
    </div>
  </div>
  <div class="clear"></div>
  <div class="cast-list">
    <div class="cast-head">
      <div class="cast-body">
        <input type="hidden" value="{{ $offer->id }}" class="offer-id">
        <input type="hidden" value="{{ $offer->status }}" class="offer-status">
        <input type="hidden" value="{{ $offer->deleted_at }}" class="deleted_at">
        @if(count($casts))
          <input type="hidden" value="{{ implode(",", $offer->cast_ids) }}" id="current-cast-id-offer">
          <input type="hidden" value="{{ $offer->class_id }}" id="current-class-id-offer">
          @php
            $class = '';
            switch ($offer->class_id) {
              case 1:
                $class = 'cast-class_b';
                break;
              case 2:
                $class = 'cast-class_p';
                break;
              case 3:
                $class = 'cast-class_d';
                break;
            }
          @endphp
          @foreach($casts as $cast)
          <div class="cast-item {{ count($casts) ==2 ? 'two-item' : 'cast-item-offer' }} img-offer">
            <a href="{{ route('cast.show', $cast->id) }}">
              <span class="tag {{ $class }} ">{{ $cast->castClass->name }}</span>
              @if ($cast->working_today)
              <span class="cast-ok">今日OK</span>
              @endif
              @if ($cast['avatars'] && @getimagesize($cast['avatars'][0]['thumbnail']))
              <img src="{{ $cast->avatars[0]['thumbnail'] }}">
              @else
              <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}">
              @endif
              <div class="info">
                <span class="tick {{ $cast->is_online ? 'tick-online' : 'tick-offline' }}"></span>
                <span id="title-info">{{ str_limit($cast->nickname, 8) }} {{ $cast->age }}歳</span>
                <div class="wrap-description">
                  <p class="description">{{ $cast->intro ? $cast->intro : '...' }}</p>
                </div>
              </div>
            </a>
          </div>
          @endforeach
        @endif
      </div>
    </div>
  </div>

  <div class="reservation-item" style="margin-top: 10px;">
    <div class="caption">
      <h2>キャストからのお誘いメッセージ</h2>
    </div>
    <div class="form-group">
      <div class="cheer-input" rows="4">{!! nl2br($offer->comment) !!}</div>
    </div>
  </div>

  <div class="reservation-item">
    <div class="caption"><!-- 見出し用div -->
      <h2>キャストを呼ぶ場所</h2>
      <label class="place">
        <!-- アイコン -->
        <div class="selectbox">
          <select class="select-prefecture-offer" name="">
            @foreach ($prefectures as $prefecture)
              <option value="{{ $prefecture['id'] }}" {{ $prefecture['id'] == $offer->prefecture_id ? 'selected' : '' }}>{{ $prefecture['name'] }}</option>
            @endforeach
          </select>
          <i></i>
        </div>

      </label>
    </div>
    <div class="form-grpup" id="list-municipalities-offer"><!-- フォーム内容 -->
      @foreach($municipalities as $municipaliti)
      <label class="button button--green area">
        <input class="input-area-offer" type="radio" name="offer_area" value="{{ $municipaliti['name'] }}">{{ $municipaliti['name'] }}</label>
      @endforeach

      <label id="area_input" class="button button--green area ">
        <input class="input-area-offer" type="radio" name="offer_area" value="その他">その他</label>
      <label class="area-input area-offer"><span>希望エリア</span>
        <input type="text" id="other_area_offer" placeholder="入力してください" name="other_area_offer" value="">
     </label>
    </div>
  </div>

  <div class="reservation-item">
    <div class="caption"><!-- 見出し用div -->
      <h2>ギャラ飲みの時間</h2>
    </div>
    <div class="text-time">※希望の開始時間を選択してください</div>
    <div class="label-date">
      @php
      $day = \Carbon\Carbon::parse($offer->date);
      $dayOfWeek = dayOfWeek()[$day->dayOfWeek];
      @endphp
      <p id="temp-date-offer">{{ $day->format('Y年m月d日') }}({{$dayOfWeek}})</p>
    </div>
    <div class="form-grpup"><!-- フォーム内容 -->
      @php
        $startHour = (int)Carbon\Carbon::parse($offer->start_time_from)->format('H');
        $endHour = (int)Carbon\Carbon::parse($offer->start_time_to)->format('H');

        $startTimeFrom = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $offer->date . ' ' . $offer->start_time_from);
        $startTimeTo = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $offer->date . ' ' . $offer->start_time_to);

        if ($endHour < $startHour) {
          $startTimeTo = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $offer->date . ' ' . $offer->start_time_to)->addDay();
          switch ($endHour) {
            case 0:
                $endHour = 24;
                break;
            case 1:
                $endHour = 25;
                break;
            case 2:
                $endHour = 26;
                break;
          }
        }

        $currentTime = Carbon\Carbon::now()->second(0);
        $date = $offer->date;
        $startHourFrom = Carbon\Carbon::parse($offer->start_time_from)->format('H:i');
        $startMinute =  (int)Carbon\Carbon::parse($offer->start_time_from)->format('i');
        if($currentTime->copy()->addMinutes(30)->between($startTimeFrom,$startTimeTo)) {
          $startHour = (int)$currentTime->copy()->addMinutes(30)->format('H');
          $startHourFrom =$currentTime->copy()->addMinutes(30)->format('H:i');
          $startMinute =  (int)$currentTime->copy()->addMinutes(30)->format('i');
          $date = $currentTime->copy()->addMinutes(30)->format('Y-m-d');
        }
      @endphp
    <div class="choose-time-offer">
      <label class="choose-time-title">希望開始時間</label>
      <label class="date-input d-flex-end">
        <p class="date-input__text">
          <span class='time-offer color-placeholder' id='temp-time-offer'>選択してください</span>
        </p>
      </label>
    </div>
    </div>
    <input type="hidden" name="current_date_offer" value="{{ $date }}" id="current-date-offer">
    <input type="hidden" name="start_time_from_offer" value="{{ Carbon\Carbon::parse($offer->start_time_from)->format('H:i') }}" id="start-time-from-offer">
    <input type="hidden" name="start_time_to_offer" value="{{ Carbon\Carbon::parse($offer->start_time_to)->format('H:i') }}" id="start-time-to-offer">
  </div>

  <div class="reservation-item">
    <input type="hidden" id="duration-offer" value="{{ $offer->duration }}">
    <div class="text-duration">
      上記の開始時間から<span style="font-weight: bold;">{{ $offer->duration }}時間</span>
    </div>
    <div class="form-grpup">
      <div class="attention-offer"><a href="javascript::void(0)" style="margin: 5px 6px -7px">延長時間について</a></div>
    </div>
  </div>

  <div class="reservation-item">
    @if (Auth::check() && Auth::user()->is_guest && Auth::user()->is_multi_payment_method)
    <div class="caption">
      <h2>決済方法選択</h2>
    </div>

    <div class="form-grpup" id="transfer_amount">
      <input type="hidden" id="current-point" value="">
        <div class="grade-list transfer_amount-order">
          <div class="transfer-left">
            <label>
              <input type="radio" name="transfer_order_offer" class="grade-radio" value="{{ \App\Enums\OrderPaymentMethod::CREDIT_CARD }}" checked="checked" >
            </label>
            <p>クレジットカード</p>
          </div>
          <div class="transfer-right">
            <label>
              <input type="radio" name="transfer_order_offer" class="grade-radio" value="{{ \App\Enums\OrderPaymentMethod::DIRECT_PAYMENT }}">
            </label>
            <p>銀行振込</p>
          </div>
        </div>
    </div>
    @endif

    <div id="card-registered">
      <div class="caption">
        <h2>クレジットカードの登録</h2>
      </div>
      @if(!Auth::user()->is_card_registered)
      <a class="link-arrow link-arrow--left tc-verification-link inactive-button-order" href="#" style="color: #222222;">未登録</a>
      @else
      <a class="link-arrow link-arrow--left tc-verification-link" href="#" style="color: #222222;">登録済み</a>
      @endif
    </div>
    <div id="show-coupon-order-offer">

    </div>

    <div id="detail-point-offer">
      <div class="settlement-confirm">
        <section class="details-header">
          <div class="details-list__line"><p></p></div>
          <div class="details-total__content">
            <div class="details-total__text">合計</div>
            <div class="details-total__marks total-amount">{{ number_format($offer->temp_point) }}P</div>
            <input type="hidden" value="{{ $offer->temp_point }} " id="temp-point-offer">
            <input type="hidden" id="point_used_offer" value="">
            <span class="details-list__button"></span>
           </div>
        </section>
        <section class="details-list">
          <div class="details-list__line"><p></p></div>
          <div class="details-list__content show">
            <ul class="details-info-list">
              <li class="details-info-list__itme">
                <p class="details-info-list__text">合流{{ $offer->duration*60 }}分</p>
                <p class="details-info-list__marks" id="order-point"></p>
              </li>
              <li class="details-info-list__itme">
                <p class="details-info-list__text">深夜手当</p>
                <p class="details-info-list__marks" id="night-fee"></p>
              </li>
              <li class="details-info-list__itme" id="show-point-coupon-offer" style="display: none;">
                <p class="details-info-list__text">割引額</p>
                <p class="details-info-list__marks sale-point-coupon" id="sale-point-coupon"></p>
              </li>
              <li class="details-info-list__itme">
                <p class="details-info-list__text--subtotal">小計</p>
                <p class="details-info-list__marks--subtotal" id="total-point-order"></p>
              </li>
            </ul>
          </div>
        </section>
      </div>
    </div>

    <div class="reservation-policy">
      <label class="checkbox">
        <input type="checkbox" class="checked-order-offer" name="confrim_order_offer">
        <span class="sp-disable" id="sp-cancel"></span>
        <p><a href="{{ route('guest.orders.cancel') }}">キャンセルポリシー</a>、及び<a href="{{ route('guest.orders.offers_attention') }}">予約前の注意事項</a>
        に同意する</p>
      </label>
    </div>
  </div>
</form>
<button type="button" class="form_footer bt-offer disable" name='orders_offer' id="confirm-orders-offer" disabled="disabled">ギャラ飲みをセッティングする</button>
<div class="overlay">
  <div class="date-select">
    <div class="date-select__content">
       <select class="select-hour-offer" name="select_hour_offer">
          @for($i = $startHour; $i <= $endHour; $i++)
            <option value="{{ ($i <10) ? '0'.$i : $i }}" {{ $i == $startHour ? 'selected' : '' }}>{{ ($i <10) ? '0'.$i : $i }}時</option>
          @endfor
       </select>
       <select class="select-minute-offer" name="select_minute_offer">
          @foreach(range($startMinute, 59) as $minute)
            <option value="{{ $minute<10 ? '0'.$minute : $minute }}" {{ $startMinute == $minute ? 'selected' : '' }}>
              {{ $minute<10 ? '0'.$minute : $minute }}分
            </option>
          @endforeach
       </select>
    </div>
    <div class="date-select__footer">
      <button class="date-select__cancel reset-color-input" type="button">キャンセル</button>
      <button class="date-select__ok date-select-offer btn-choose-time-success" type="button">完了</button>
    </div>

  </div>
</div>
@endsection

@section('web.script')
<script>
window.onload = function () {

  if ($('.two-item').length) {
    var width = $('.two-item').width();
    var height = width+50;
    $('.two-item').css("height", height+"px");
    $('.cast-body').css('overflow','hidden');
    $('.img-offer img').css("height", (height-51)+"px");

  } else {
    var width = $('.cast-item-offer').width();
    var height = width+50;
    $('.cast-item-offer').css("height",height+"px");
    $('.img-offer img').css("height", (height-51)+"px");
  }

  $('#popup-intructions-offer').click(function(event) {
    $('#instructions-offer').trigger('click');
  });

  $(function(){
    var detailsListButton =$(".details-list__button");
    detailsListButton.on("click",function(){
      $(this).toggleClass("hide");
      $(".details-list").toggle();
    })
  });
};
</script>
@endsection
