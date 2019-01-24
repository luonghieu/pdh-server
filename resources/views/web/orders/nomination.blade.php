@section('title', 'キャスト予約')
@section('screen.id', 'ge2-1-x')
@section('screen.class', 'ge2-1-b')
@extends('layouts.web')
@section('web.content')
    <a href="javascript:void(0)" id="confirm-order-nomination-submit" class="gtm-hidden-btn" onclick="dataLayer.push({
        'userId': '<?php echo Auth::user()->id; ?>',
        'event': 'nominationbooking_complete'
    });"></a>
<form action="{{ route('guest.orders.post_nominate') }}" method="POST" class="create-call-form" id="create-nomination-form">
  {{ csrf_field() }}
  <div class="cast-selected">
        <div class="cast-selected__photo">
          @if (@getimagesize($cast['avatars'][0]['thumbnail']))
          <img src="{{ $cast['avatars'][0]['thumbnail'] }}" alt="">
          @else
          <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
          @endif
        </div>
        <div class="cast-selected__content">
          <p class="cast-selected__name cast-name">{{ $cast['nickname'] }}</p>
          <div class="cast-selected__bottom">
            @php
              $class = '';
              switch ($cast['class_id']) {
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
            <p class="cast-class {{ $class }}">{{ $cast['class'] }}</p>
            <input type="hidden" name="class_id" value="{{ $cast['class_id'] }}">
            <p class="cast-selected__price price-show">30分あたりの料金<span>{{ number_format($cast['cost']) .'P' }}</span></p>
          </div>
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
              <input class="input-area" type="radio" name="nomination_area" value="六本木">六本木</label>
            <label class="button button--green area">
              <input class="input-area" type="radio" name="nomination_area" value="恵比寿">恵比寿</label>
            <label class="button button--green area">
              <input class="input-area" type="radio" name="nomination_area" value="西麻布">西麻布</label>
            <label class="button button--green area ">
              <input class="input-area" type="radio" name="nomination_area" value="渋谷">渋谷</label>
            <label class="button button--green area ">
              <input class="input-area" type="radio" name="nomination_area" value="赤坂">赤坂</label>
            <label class="button button--green area ">
              <input class="input-area" type="radio" name="nomination_area" value="銀座">銀座</label>
            <label class="button button--green area ">
              <input class="input-area" type="radio" name="nomination_area" value="中目黒">中目黒</label>
            <label class="button button--green area" >
              <input class="input-area" type="radio" name="nomination_area" value="新橋">新橋</label>
            <label class="button button--green area ">
              <input class="input-area" type="radio" name="nomination_area" value="池袋">池袋</label>
            <label class="button button--green area ">
              <input class="input-area" type="radio" name="nomination_area" value="新宿">新宿</label>
            <label id="area_input" class="button button--green area ">
              <input class="input-area" type="radio" name="nomination_area" value="その他">その他</label>
            <label class="area-input area-nomination"><span>希望エリア</span>
              <input type="text" id="other_area_nomination" placeholder="入力してください" name="other_area_nomination" value="">
           </label>
          </div>
        </div>

        <div class="reservation-item">
          <div class="caption"><!-- 見出し用div -->
            <h2>キャストとの合流時間</h2>
          </div>
          <div class="form-grpup"><!-- フォーム内容 -->
            @if(isset($orderOptions['call_time']))
              @foreach($orderOptions['call_time'] as $callTime)
              <label class="button button--green date {{ $callTime['value'] == 30 ? 'active' : '' }} {{ !$callTime['is_active'] ? 'inactive' : '' }}">
                <input class="input-time-join" type="radio" name="time_join_nomination" value="{{ $callTime['value'] }}" {{ !$callTime['is_active'] ? 'disabled' : '' }} {{ $callTime['value'] == 30 ? 'checked' : '' }} >
                {{ $callTime['name'] }}
              </label>
              @endforeach
            @endif
            <label id="date_input" class="button button--green date ">
              <input class="input-time-join" type="radio" name="time_join_nomination" value="other_time" >それ以外</label>
            <label class="date-input date-input-nomination">
              <span>希望日時</span>
              <p class="date-input__text">
                <span class='sp-month month-nomination'></span>
                <span class='sp-date date-nomination'></span>
                <span class="sp-time time-nomination"></span>
              </p>
            </label>
        </div>
        </div>
        <div class="reservation-item">
          <div class="caption"><!-- 見出し用div -->
            <h2>キャストを呼ぶ時間</h2>
          </div>
          <div class="form-grpup"><!-- フォーム内容 -->
            <label class="button button--green time">
              <input class="input-duration" type="radio" name="time_set_nomination" value="1">
              1時間
            </label>
            <label class="button button--green time ">
              <input class="input-duration" type="radio" name="time_set_nomination" value="2">
              2時間
            </label>
            <label class="button button--green time">
              <input class="input-duration" type="radio" name="time_set_nomination" value="3">
              3時間
            </label>
            <label id="time-input" class="button button--green time">
              <input class="input-duration" type="radio" name="time_set_nomination" value="other_time_set">
              4時間以上
            </label>
            <label class="time-input time-input-nomination">
              <span>呼ぶ時間</span>
              <div class="selectbox">
                <select class="select-duration" name="sl_duration_nominition">
                  <option value="4" >4時間</option>
                  <option value="5" >5時間</option>
                  <option value="6" >6時間</option>
                  <option value="7" >7時間</option>
                  <option value="8" >8時間</option>
                  <option value="9" >9時間</option>
                  <option value="10" >10時間</option>
                </select>
                <i></i>
              </div>
            </label>
          </div>
          <div class="caption">
            <h2>クレジットカードの登録</h2>
          </div>

          @if(!Auth::user()->is_card_registered)
          <a class="link-arrow link-arrow--left tc-verification-link inactive-button-order" href="#" style="color: #222222;">未登録</a>
          @else
          <a class="link-arrow link-arrow--left tc-verification-link" href="#" style="color: #222222;">登録済み</a>
          @endif
        </div>

        <div class="reservation-attention"><a href="{{ route('guest.orders.nominate_step2') }}" style="margin: 10px 0px -7px;">予約前の注意事項</a></div>

        <div class="reservation-total">
          <div class="reservation-total__content">
            <div class="reservation-total__sum">合計<span class="total-point">0P~</span></div>
            <p class="reservation-total__text">内訳：{{ number_format($cast['cost']) }}(キャストP/30分)✖0時間</p>
          </div>

        </div>

         <div class="reservation-policy">
          <label class="checkbox">
            <input type="checkbox" class="checked-order" name="confrim_order_nomination">
            <span class="sp-disable" id="sp-cancel"></span>
            <a href="{{ route('guest.orders.cancel') }}">キャンセルポリシー</a>
            に同意する
          </label>
        </div>
        <button type="button" class="form_footer ct-button disable" name='orders_nomination' id="confirm-orders-nomination" disabled="disabled">予約リクエストを確定する</button>
        <div class="overlay">
          <div class="date-select ct-date-select">
          <div class="date-select__content">
          @php
            $now = \Carbon\Carbon::now()->addMinutes(30);
            $currentMonth = $now->format('m');
            $currentDate = $now->format('d');
            $currentHour = $now->format('H');
            $currentMinute = $now->format('i');
          @endphp
         <select class="select-month" name="sl_month_nomination">
          @foreach(range(1, 12) as $month)
           <option value="{{ $month }}" {{ $currentMonth == $month ? 'selected' : '' }}>{{ $month }}月</option>
          @endforeach
         </select>
         <select class="select-date" name="sl_date_nomination">
            @foreach(getDay() as $key => $val)
             <option value="{{ $key }}" {{ $currentDate == $key ? 'selected' : '' }}>{{ $val }}</option
              >
            @endforeach
         </select>
         <select class="select-hour" name="sl_hour_nomination">
          @foreach(range(00, 23) as $hour)
           <option value="{{ $hour }}" {{ $currentHour == $hour ? 'selected' : '' }}>
                {{ $hour<10 ? '0'.$hour : $hour }}時
          </option>
          @endforeach
         </select>
         <select class="select-minute" name="sl_minute_nomination">
           @foreach(range(00, 59) as $minute)
           <option value="{{ $minute }}" {{ $currentMinute == $minute ? 'selected' : '' }}>
                {{ $minute<10 ? '0'.$minute : $minute }}分
          </option>
          @endforeach
         </select>
      </div>
      <div class="date-select__footer">
        <button class="date-select__cancel btn-date-select" type="button">キャンセル</button>
        <button class="date-select__ok choose-time btn-date-select" type="button">完了</button>
      </div>
    </div>
    </div>
    <input type="hidden" value="{{ $cast['cost'] or 0 }}" class="cost-order">
    <input type="hidden" value="{{ $cast['id'] or 0 }}" name="cast_id" class="cast-id">
  </form>

  @if((Session::has('status_code')))
    @php
      $statusCode = Session::get('status_code');
    @endphp
      <section class="button-box">
        <label for="{{ $statusCode }}" class="status-code-nomination"></label>
      </section>
  @endif

@endsection

@section('web.extra')

  <div class="modal_wrap modal-confirm-nominate">
    <input id="orders-nominate" type="checkbox">
    <div class="modal_overlay">
      <label for="orders-nominate" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
        <div class="text-box">
          <h2>予約を確定しますか？</h2>
        </div>
        <div class="close_button-box">
          <div class="close_button-block">
            <label for="orders-nominate" class="close_button  left ">キャンセル</label>
          </div>
          <div class="close_button-block">
            <label for="orders-nominate" class="close_button right cf-orders-nominate">確定する</label>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if(!$user->is_card_registered)
    <div class="modal_wrap">
      <input id="md-require-card" type="checkbox">
      <div class="modal_overlay">
        <label for="md-require-card" class="modal_trigger"></label>
        <div class="modal_content modal_content-btn1">
          <div class="text-box">
            <h2>クレジットカードを <br>登録してください</h2>
            <p>※キャストと合流するまで <br>料金は発生しません</p>
          </div>
          <label for="md-require-card" class="close_button lable-register-card">クレジットカードを登録する</label>
        </div>
      </div>
    </div>
  @endif

  @if(Session::has('status_code'))
    @php
      $statusCode = Session::get('status_code');
      $triggerId = $statusCode;
      $label = $statusCode;

      if (406 == $statusCode) {
        $button = 'クレジットカード情報を更新する';
        $triggerClass = 'lable-register-card';
        $content = '予約日までにクレジットカードの <br> 有効期限が切れます <br><br> 予約を完了するには <br> カード情報を更新してください';
      }

      if (400 == $statusCode) {
        $content = '開始時間は現在時刻から30分以降の時間を選択してください';
      }

      if (409 == $statusCode) {
        $content = 'すでに予約があります';
      }

      if (422 == $statusCode) {
        $content = 'この操作は実行できません';
      }

      if (500 == $statusCode) {
        $content = 'サーバーエラーが発生しました';
      }

    @endphp

    <div class="modal_wrap">
      <input id="{{ $triggerId }}" type="checkbox">
      <div class="modal_overlay">
        <label for="{{ $label or '' }}" class="modal_trigger"></label>
        <div class="modal_content modal_content-btn1">
          <div class="text-box">
            <h2>{!! $title or '' !!}</h2>
            <p>{!! $content or '' !!}</p>
          </div>
          <label for="{{ $triggerId }}" class="close_button {{ $triggerClass or '' }}">{{ $button or 'OK'}}</label>
        </div>
      </div>
    </div>

  @endif

@endsection
