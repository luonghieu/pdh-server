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
              @if(count($tags))
              @foreach($tags as $tag)
              <li class="details-info-list_kibun">{{ $tag->name }}</li>
              @endforeach
              @endif
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
                <p>{{ count(explode(',', $data['cast_ids'])) }}</p>
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
          @if ($data['cast_numbers'] < 3 && $data['obj_cast_class']->id < 2)
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
  @if((Session::has('order_done')))
    <section class="button-box">
      <label for="{{ Session::get('order_done') }}" class="order-done"></label>
    </section>
  <form action="{{ route('web.index') }}" method="GET" id="redirect-index">
  </form>
  @endif

  @if((Session::has('statusCode')))
    @if(406 == Session::get('statusCode'))
      <form action="{{ route('credit_card.index') }}" method="GET" class="form-expired-card">
        <section class="button-box">
          <label for="{{ Session::get('statusCode') }}" class="status-code"></label>
        </section>
      </form>
    @else
      <section class="button-box">
        <label for="{{ Session::get('statusCode') }}" class="status-code"></label>
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
  @confirm(['triggerId' => 'orders', 'triggerCancel' =>'', 'buttonLeft' =>'キャンセル',
   'buttonRight' =>'確定する','triggerSuccess' =>'right sb-form-orders'])

    @slot('title')
      予約を確定しますか？
    @endslot

    @slot('content')
    @endslot
  @endconfirm
  @if(!$user->card)
    @modal(['triggerId' => 'md-require-card', 'triggerClass' =>'lable-register-card','button' =>'クレジットカードを登録する
'])
      @slot('title')
        クレジットカードを <br>登録してください
      @endslot

      @slot('content')
      ※キャストと合流するまで <br>料金は発生しません
      @endslot
    @endmodal
  @endif

  @if((Session::has('order_done')))
    @modal(['triggerId' => Session::get('order_done'), 'triggerClass' =>'modal-redirect'])
      @slot('title')
        予約が完了しました
      @endslot

      @slot('content')
      ただいまキャストの調整中です
      予約状況はホーム画面の予約一覧をご確認ください
      @endslot
    @endmodal
  @endif
  @if((Session::has('statusCode')) && 406 == Session::get('statusCode'))
    @modal(['triggerId' => Session::get('statusCode'), 'button' =>'クレジットカード情報を更新する', 'triggerClass' =>'expired-card'])
      @slot('title')
      @endslot

      @slot('content')
      予約日までにクレジットカードの <br> 有効期限が切れます <br><br> 予約を完了するには <br> カード情報を更新してください
      @endslot
    @endmodal
  @else
    @if((Session::has('statusCode')))
      @modal(['triggerId' => Session::get('statusCode'), 'triggerClass' =>'error-code'])
        @slot('title')
        @endslot

        @if(Session::get('statusCode') ==400)
          @slot('content')
          開始時間は現在以降の時間を指定してください
          @endslot
        @endif

        @if(Session::get('statusCode') ==409)
          @slot('content')
          すでに予約があります
          @endslot
        @endif

        @if(Session::get('statusCode') ==422)
          @slot('content')
          この操作は実行できません
          @endslot
        @endif

        @if(Session::get('statusCode') ==500)
          @slot('content')
          サーバーエラーが発生しました
          @endslot
        @endif
      @endmodal
    @endif
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
