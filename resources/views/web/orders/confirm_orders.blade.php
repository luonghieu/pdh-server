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
        <div class="details-header__title">äºˆç´„å†…å®¹</div>
        <div class="details-list-box">
          <ul class="details-header__list">
            <li><i><img src="{{ asset('assets/web/images/common/map.svg') }}"></i><p class="word18">{{ $data['area'] or $data['other_area'] }}</p></li>
            <li><i><img src="{{ asset('assets/web/images/common/clock.svg') }}"></i>
              <p>
                {{ isset($data['time']) ? $data['time'].'åˆ†å¾Œ' : Carbon\Carbon::parse($data['otherTime'])->format('Yå¹´mæœˆdæ—¥') }}
                {{ (isset($data['time_detail'])) ? $data['time_detail']['hour'].':'.$data['time_detail']['minute'] : ''}}
              </p>
            </li>
            <li><i><img src="{{ asset('assets/web/images/common/glass.svg') }}"></i><p>{{ $data['duration'] }}æ™‚é–“</p></li>
            <li><i><img src="{{ asset('assets/web/images/common/diamond.svg') }}"></i>
              <p>{{ $castClass->name }} {{ $data['cast_numbers'] .'å' }}
              </p>
            </li>
          </ul>
          <div class="btn2-s"><a href="{{ route('guest.orders.call') }}">å¤‰æ›´</a></div>
        </div>
      </section>
      <section class="details-list">
        <div class="details-list__line"><p></p></div>
        <div class="details-list__header">
          <div class="details-header__title">ä»Šæ—¥ã®æ°—åˆ†</div>
        </div>
        <div class="details-list__content show">
          <div class="details-list-box">
            <ul class="details-info-list">
              @foreach($tags as $tag)
                <li class="details-info-list_kibun">{{ $tag->name }}</li>
              @endforeach
            </ul>
            <div class="btn2-s"><a href="{{ route('guest.orders.get_step2') }}">å¤‰æ›´</a></div>
          </div>
        </div>
      </section>

      <section class="details-list details-shimei">
        <div class="details-list__line"><p></p></div>
        <div class="details-list__header">
          <div class="details-header__title">å¸Œæœ›ã—ã¦ã„ã‚‹ã‚­ãƒ£ã‚¹ãƒˆ</div>
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
            <div class="btn2-s"><a href="{{ route('guest.orders.get_step3') }}">å¤‰æ›´</a></div>
          </div>
        </div>
      </section>

      <section class="details-total">
        <div class="details-list__line"><p></p></div>
        <div class="details-total__content">
          <div class="details-list__header">
            <div class="details-header__title">åˆè¨ˆ</div>
          </div>
          <div class="details-total__marks">{{ number_format($tempPoint) .'P' }}</div>
        </div>
        @php
          $campaignFrom = Carbon\Carbon::parse(env('CAMPAIGN_FROM'));
          $campaignTo = Carbon\Carbon::parse(env('CAMPAIGN_TO'));
          $timeCreateGuest = Carbon\Carbon::parse(Auth::user()->created_at);
          $timeDisplay = now()->subDay(7);
        @endphp
        @if (Auth::user()->is_guest && Auth::user()->is_verified && !Auth::user()->campaign_participated
          && now()->between($campaignFrom, $campaignTo) && $timeCreateGuest > $timeDisplay)
          <div class="notify-campaign-confirm">
            <span>â€»ã‚­ãƒ£ãƒ³ãƒšãƒ¼ãƒ³ãŒé©ç”¨ã•ã‚Œã‚‹å ´åˆã€ã‚­ãƒ£ã‚¹ãƒˆã¨åˆæµå¾Œã«ç„¡æ–™æ™‚é–“åˆ†ã®ãƒã‚¤ãƒ³ãƒˆãŒä»˜ä¸Žã•ã‚Œã€è§£æ•£å¾Œã«ä¸è¶³åˆ†ã®ãƒã‚¤ãƒ³ãƒˆã®ã¿ãŒæ±ºæ¸ˆã•ã‚Œã¾ã™ã€‚</span>
          </div>
        @endif
      </section>
    </div>
    <div class="reservation-policy">
      <label class="checkbox">
        <input type="checkbox" class="cb-cancel">
        <span class="sp-disable" id="sp-cancel"></span>
        <a href="{{ route('guest.orders.cancel') }}">ã‚­ãƒ£ãƒ³ã‚»ãƒ«ãƒãƒªã‚·ãƒ¼</a>
        ã«åŒæ„ã™ã‚‹
      </label>
    </div>
    <input type="hidden" value="" name="cast_ids" id="cast-ids-nominate">
    <input type="hidden" value="{{ $type }}" name="type_order">
    <input type="hidden" value="{{ $tempPoint }}" name="temp_point_order">
    <button type="button" class="form_footer ct-button disable" id="btn-confirm-orders" disabled="disabled">äºˆç´„ãƒªã‚¯ã‚¨ã‚¹ãƒˆã‚’ç¢ºå®šã™ã‚‹</button>
  </form>
  @if(($statusCode))
    <section class="button-box">
      <label for="{{ $statusCode }}" class="status-code"></label>
    </section>
  @endif
@endsection

@section('web.extra')

  <div class="modal_wrap modal-confirm">
    <input id="orders" type="checkbox">
    <div class="modal_overlay">
      <label for="orders" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
        <div class="text-box">
          <h2>äºˆç´„ã‚’ç¢ºå®šã—ã¾ã™ã‹ï¼Ÿ</h2>
        </div>
        <div class="close_button-box">
          <div class="close_button-block">
            <label for="orders" class="close_button  left ">ã‚­ãƒ£ãƒ³ã‚»ãƒ«</label>
          </div>
          <div class="close_button-block">
            <label for="orders" class="close_button right sb-form-orders">ç¢ºå®šã™ã‚‹</label>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if(!$user->card)
    <div class="modal_wrap">
      {{-- <input id="md-require-card" type="checkbox"> --}}
      <div class="modal_overlay">
        <label for="md-require-card" class="modal_trigger"></label>
        <div class="modal_content modal_content-btn1">
          <div class="text-box">
            <h2>ã‚¯ãƒ¬ã‚¸ãƒƒãƒˆã‚«ãƒ¼ãƒ‰ã‚’â€¨<br>ç™»éŒ²ã—ã¦ãã ã•ã„</h2>
            <p>â€»ã‚­ãƒ£ã‚¹ãƒˆã¨åˆæµã™ã‚‹ã¾ã§â€¨<br>æ–™é‡‘ã¯ç™ºç”Ÿã—ã¾ã›ã‚“</p>
          </div>
          <label for="md-require-card" class="close_button lable-register-card">ã‚¯ãƒ¬ã‚¸ãƒƒãƒˆã‚«ãƒ¼ãƒ‰ã‚’ç™»éŒ²ã™ã‚‹</label>
        </div>
      </div>
    </div>
  @endif

  @if(($statusCode))
    @php
      $statusCode = $statusCode;

      $triggerId = $statusCode;
      $label = $statusCode;

      if(200 == $statusCode) {
        $triggerClass = 'order-done';
        $title = 'äºˆç´„ãŒå®Œäº†ã—ã¾ã—ãŸ';
        $content = 'ãŸã ã„ã¾ã‚­ãƒ£ã‚¹ãƒˆã®èª¿æ•´ä¸­ã§ã™<br>äºˆç´„çŠ¶æ³ã¯ãƒ›ãƒ¼ãƒ ç”»é¢ã®äºˆç´„ä¸€è¦§ã‚’ã”ç¢ºèªãã ã•ã„';
      }

      if (406 == $statusCode) {
        $button = 'ã‚¯ãƒ¬ã‚¸ãƒƒãƒˆã‚«ãƒ¼ãƒ‰æƒ…å ±ã‚’æ›´æ–°ã™ã‚‹';
        $triggerClass = 'lable-register-card';
        $content = 'äºˆç´„æ—¥ã¾ã§ã«ã‚¯ãƒ¬ã‚¸ãƒƒãƒˆã‚«ãƒ¼ãƒ‰ã® <br> æœ‰åŠ¹æœŸé™ãŒåˆ‡ã‚Œã¾ã™ <br><br> äºˆç´„ã‚’å®Œäº†ã™ã‚‹ã«ã¯ <br> ã‚«ãƒ¼ãƒ‰æƒ…å ±ã‚’æ›´æ–°ã—ã¦ãã ã•ã„';
      }

      if (400 == $statusCode) {
        $content = 'é–‹å§‹æ™‚é–“ã¯ç¾åœ¨æ™‚åˆ»ã‹ã‚‰60åˆ†ä»¥é™ã®æ™‚é–“ã‚’é¸æŠžã—ã¦ãã ã•ã„';
      }

      if (409 == $statusCode) {
        $content = 'ã™ã§ã«äºˆç´„ãŒã‚ã‚Šã¾ã™';
      }

      if (422 == $statusCode) {
        $content = 'ã“ã®æ“ä½œã¯å®Ÿè¡Œã§ãã¾ã›ã‚“';
      }

      if (500 == $statusCode) {
        $content = 'ã‚µãƒ¼ãƒãƒ¼ã‚¨ãƒ©ãƒ¼ãŒç™ºç”Ÿã—ã¾ã—ãŸ';
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
          var cutFigure = '17'; // ã‚«ãƒƒãƒˆã™ã‚‹æ–‡å­—æ•°
          var afterTxt = ' â€¦'; // æ–‡å­—ã‚«ãƒƒãƒˆå¾Œã«è¡¨ç¤ºã™ã‚‹ãƒ†ã‚­ã‚¹ãƒˆ

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