@section('title', 'キャスト予約')
@section('screen.id', 'ge2-1-x')
@section('web.extra_css')
<link rel="stylesheet" href="{{ mix('assets/web/css/cast.min.css') }}">
<link rel="stylesheet" href="{{ mix('assets/web/css/ge_4.min.css') }}">
@endsection
@extends('layouts.web')
@section('web.extra')
<div class="modal_wrap">
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

@endsection
@section('web.content')
      <form>
        <div class="cast-list">
          <div class="cast-head">
          <div class="cast-body">
            <input type="hidden" value="{{ $offer->id }}" class="offer-id">
            <input type="hidden" value="{{ $offer->status }}" class="offer-status">
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
            <div class="cheer-input" rows="4">{!! str_replace(["\r\n", "\n\r", "\r", "\n"], "<br>", $offer->comment) !!}</div>
          </div>
        </div>

        <div class="reservation-item">
          <div class="caption"><!-- 見出し用div -->
            <h2>キャストを呼ぶ場所</h2>
            <label class="place">
              <!-- アイコン -->
              <div class="selectbox">
                <select class="" name="">
                  <option value="">東京</option>
                </select>
                <i></i>
              </div>
            </label>
          </div>
          <div class="form-grpup"><!-- フォーム内容 -->
            <label class="button button--green area">
              <input class="input-area-offer" type="radio" name="offer_area" value="六本木">六本木</label>
            <label class="button button--green area">
              <input class="input-area-offer" type="radio" name="offer_area" value="恵比寿">恵比寿</label>
            <label class="button button--green area">
              <input class="input-area-offer" type="radio" name="offer_area" value="西麻布">西麻布</label>
            <label class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="渋谷">渋谷</label>
            <label class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="赤坂">赤坂</label>
            <label class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="銀座">銀座</label>
            <label class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="中目黒">中目黒</label>
            <label class="button button--green area" >
              <input class="input-area-offer" type="radio" name="offer_area" value="新橋">新橋</label>
            <label class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="池袋">池袋</label>
            <label class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="新宿">新宿</label>
            <label id="area_input" class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="その他">その他</label>
            <label class="area-input area-offer"><span>希望エリア</span>
              <input type="text" id="other_area_offer" placeholder="入力してください" name="other_area_offer" value="">
           </label>
          </div>
        </div>

        <div class="reservation-item">
          <div class="caption"><!-- 見出し用div -->
            <h2>ギャラ飲みの開始時間</h2>
          </div>
          <div class="form-grpup"><!-- フォーム内容 -->
            <label class="date-input d-flex-end">
              <p class="date-input__text">
                <span id="temp-date-offer">{{ Carbon\Carbon::parse($offer->date)->format('Y年m月d日') }}</span>&nbsp&nbsp&nbsp
                <span class='time-offer' id='temp-time-offer'>{{ Carbon\Carbon::parse($offer->start_time_from)->format('H:i') }}~</span>
              </p>
            </label>
          </div>
          <input type="hidden" name="current_date_offer" value="{{ $offer->date }}" id="current-date-offer">
          <input type="hidden" name="start_time_from_offer" value="{{ Carbon\Carbon::parse($offer->start_time_from)->format('H:i') }}" id="start-time-from-offer">
          <input type="hidden" name="start_time_to_offer" value="{{ Carbon\Carbon::parse($offer->start_time_to)->format('H:i') }}" id="start-time-to-offer">
        </div>

        <div class="reservation-item">
          <div class="caption"><!-- 見出し用div -->
            <h2>キャストとを呼ぶ時間</h2>
          </div>
          <div class="form-grpup"><!-- フォーム内容 -->
            @for($i=1; $i<4; $i++)
            <label class="button button--green time {{ $i == $offer->duration ? 'active' : '' }}">
              <input class="input-duration-offer" type="radio" name="time_set_offer" value="{{ $i }}" {{ $i == $offer->duration ? 'checked' : '' }} >
              {{ $i }}時間
            </label>
            @endfor
            <label id="time-input" class="button button--green time {{ $offer->duration > 3 ? 'active' : '' }}">
              <input class="input-duration-offer" type="radio" name="time_set_offer" value="other_time_set"  {{ $offer->duration > 3 ? 'checked' : '' }}>
              4時間以上
            </label>
            <label class="time-input time-input-offer" style="{{  $offer->duration > 3 ? 'display: flex;' : '' }}">
              <span>呼ぶ時間</span>
              <div class="selectbox">
                <select class="select-duration-offer" name="sl_duration_offer">
                  @for ($i=4; $i <11; $i++)
                  <option value="{{ $i }}" {{ $i == $offer->duration ? 'selected' : '' }}>{{ $i }}時間</option>
                  @endfor
                </select>
                <i></i>
              </div>
            </label>
          </div>
        </div>

        <div class="reservation-item">
          <div class="caption">
            <h2>クレジットカードの登録</h2>
          </div>

          @if(!Auth::user()->card)
          <a class="link-arrow link-arrow--left" href="{{ route('credit_card.index') }}" style="color: #222222;">未登録</a>
          @else
          <a class="link-arrow link-arrow--left" href="{{ route('credit_card.index') }}" style="color: #222222;">登録済み</a>
          @endif
          <div class="form-group">
            <div class="reservation-attention"><a href="{{ route('guest.orders.offers_attention') }}" style="margin: 10px 0px -7px">予約前の注意事項</a></div>
            <div class="total-box">
              <span class="total-text">合計</span>
              <span class="total-amount">{{ number_format($offer->temp_point) }}P~</span>
              <input type="hidden" value="{{ $offer->temp_point }} " id="temp-point-offer">
            </div>

            <div class="reservation-policy">
              <label class="checkbox">
                <input type="checkbox" class="checked-order-offer" name="confrim_order_offer">
                <span class="sp-disable" id="sp-cancel"></span>
                <a href="{{ route('guest.orders.cancel') }}">キャンセルポリシー</a>
                に同意する
              </label>
            </div>
          </div>
        </div>
      </form>
      <button type="button" class="form_footer bt-offer disable" name='orders_offer' id="confirm-orders-offer" disabled="disabled">ギャラ飲みをセッティングする</button>
    <div class="overlay">
      <div class="date-select">
        <div class="date-select__content">
          @php
            $startHour = (int)Carbon\Carbon::parse($offer->start_time_from)->format('H');
            $endHour = (int)Carbon\Carbon::parse($offer->start_time_to)->format('H');

            if ($endHour < $startHour) {
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

            $startMinute =  (int)Carbon\Carbon::parse($offer->start_time_from)->format('i');
          @endphp
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
          <button class="date-select__cancel" type="button">キャンセル</button>
          <button class="date-select__ok date-select-offer" type="button">完了</button>
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

};
</script>
@endsection
