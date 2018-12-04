@section('title', '予約内容のご確認')
@section('screen.id', 'gl3')
@section('screen.class', 'ge3')
@extends('layouts.web')
@section('web.content')
    <a href="javascript:void(0)" id="confirm-order-submit" class="gtm-hidden-btn" onclick="dataLayer.push({
            'userId': '<?php echo Auth::user()->id; ?>',
            'event': 'callbooking_complete'
            });"></a>
  @if(session()->has('data'))
  @php
  $data = Session::get('data');
  @endphp
  @endif
  <form action="{{ route('guest.orders.add') }}"  method="POST" class="create-call-form" id="add-orders" name="confirm_orders_form">
    {{ csrf_field() }}
    <div class="settlement-confirm">
      <section class="details-list">
        <div class="details-header__title">予約内容</div>
          <div class="details-list-box">
            <ul class="details-header__list">
              <li><i><img src="{{ asset('assets/web/images/common/map.svg') }}"></i><p class="word18">{{ $data['area'] or $data['other_area'] }}</p></li>
              <li><i><img src="{{ asset('assets/web/images/common/clock.svg') }}"></i>
                <p>
                {{ isset($data['time']) ? $data['time'].'分後' : Carbon\Carbon::parse($data['otherTime'])->format('Y年m月d日') }}
                {{ (isset($data['time_detail'])) ? $data['time_detail']['hour'].':'.$data['time_detail']['minute'] : ''}}
                </p>
              </li>
              <li><i><img src="{{ asset('assets/web/images/common/glass.svg') }}"></i><p>{{ $data['duration'] }}時間</p></li>
              <li><i><img src="{{ asset('assets/web/images/common/diamond.svg') }}"></i>
                <p>{{ $castClass->name }} {{ $data['cast_numbers'] .'名' }}
                </p>
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
            <ul class="details-info-list">
              @foreach($tags as $tag)
              <li class="details-info-list_kibun">{{ $tag->name }}</li>
              @endforeach
            </ul>
            <div class="btn2-s"><a href="{{ route('guest.orders.get_step2') }}">変更</a></div>
          </div>
        </div>
      </section>

      <section class="details-list details-shimei">
        <div class="details-list__line"><p></p></div>
        <div class="details-list__header">
          <div class="details-header__title">指名リクエスト</div>
        </div>
        <div class="details-list__content show">
          <div class="details-list-box">
            <div class="details-list-box">
                <p>{{ count($casts) }}</p>
                <ul class="details-list-box__pic">
                  @foreach($casts as $cast)
                  <li>
                    @if (@getimagesize($cast->avatars[0]->thumbnail))
                      <img src="{{ $cast->avatars[0]->thumbnail }}">
                    @else
                      <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
                    @endif
                  </li>
                  @endforeach
                </ul>
            </div>
            <div class="btn2-s"><a href="{{ route('guest.orders.get_step3') }}">変更</a></div>
          </div>
        </div>
      </section>

      <section class="details-total">
        <div class="details-list__line"><p></p></div>
        <div class="details-total__content">
        <div class="details-list__header">
          <div class="details-header__title">合計</div>
        </div>
          <div class="details-total__marks">{{ number_format($data['temp_point']) .'P' }}</div>
        </div>
        @php
          $campaignFrom = Carbon\Carbon::parse('2018-11-28');
          $campaignTo = Carbon\Carbon::parse('2018-11-30 23:59:59');
        @endphp
        @if(Auth::user()->is_guest && Auth::user()->is_verified && !Auth::user()->campaign_participated && now()->between($campaignFrom, $campaignTo))
          @if ($data['cast_numbers'] < 3 && $castClass->id < 2)
            <div class="notify-campaign-confirm">
              <span>※キャンペーン適用の場合、キャストと合流後に無料時間分のポイントを付与いたします</span>
            </div>
          @endif
        @endif
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
    <button type="button" class="form_footer ct-button disable" id="btn-confirm-orders" disabled="disabled">予約リクエストを確定する</button>
  </form>
  <section class="button-box">
    <label for="orders" class="lb-orders"></label>
  </section>
  @if(($statusCode))
    @if('done' == $statusCode)
      <section class="button-box">
        <label for="{{ $statusCode }}" class="order-done"></label>
      </section>
    <form action="{{ route('web.index') }}" method="GET" id="redirect-index">
    </form>
    @endif

    @if(406 == $statusCode)
      <form action="{{ route('credit_card.index') }}" method="GET" class="form-expired-card">
        <section class="button-box">
          <label for="{{ $statusCode }}" class="status-code"></label>
        </section>
      </form>
    @else
      <section class="button-box">
        <label for="{{ $statusCode }}" class="status-code"></label>
      </section>
    @endif
  @endif

  @if(!$user->card)
    <form action="{{ route('credit_card.index') }}" method="GET" class="register-card">
      <section class="button-box">
        <label for="md-require-card" class="lable-register-card"></label>
      </section>
    </form>
  @endif
@endsection

@section('web.extra')

  <div class="modal_wrap">
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

  @if(!$user->card)
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

  @if(($statusCode))
    @php
      $statusCode = $statusCode;

      $triggerId = $statusCode;
      $label = $statusCode;

      if('done' == $statusCode) {
        $triggerClass = 'modal-redirect';
        $title = '予約が完了しました';
        $content = 'ただいまキャストの調整中です<br>予約状況はホーム画面の予約一覧をご確認ください';
      }

      if (406 == $statusCode) {
        $button = 'クレジットカード情報を更新する';
        $triggerClass = 'expired-card';
        $content = '予約日までにクレジットカードの <br> 有効期限が切れます <br><br> 予約を完了するには <br> カード情報を更新してください';
      }

      if (400 == $statusCode) {
        $content = '開始時間は現在以降の時間を指定してください';
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
