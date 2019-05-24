@section('title', 'Cheers')
@section('controller.id', 'top')
@extends('layouts.web')
@section('web.extra_css')
<link rel="stylesheet" href="{{ mix('assets/web/css/ge_1.min.css') }}">
@endsection
@section('web.extra')
  <div class="modal_wrap" id="input_birthday_modal">
    <input id="trigger3" type="checkbox">
      <div class="modal_overlay">
        <label for="trigger3" class="modal_trigger" id="profile-popup"></label>
        <div class="modal_content modal_content-btn3">
          <div class="content-in" id="profile-message">
            <h2></h2>
          </div>
        </div>
      </div>
  </div>

  @if (Auth::check())
    @php
      $campaignFrom = Carbon\Carbon::parse(env('CAMPAIGN_FROM'));
      $campaignTo = Carbon\Carbon::parse(env('CAMPAIGN_TO'));
    @endphp
    @if (Auth::user()->is_guest && Auth::user()->is_verified && Auth::user()->date_of_birth
        && !Auth::user()->campaign_participated && now()->between($campaignFrom, $campaignTo)
        && Auth::user()->created_at >= $campaignFrom)
      @include('web.users.popup')
    @endif
  @endif

  @if (!Auth::user()->is_verified)
  <div class="modal_wrap">
    <input id="triggerVerify" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger2" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
        <div class="text-box">
          <h2>お知らせ</h2>
          <p>SMSを利用して</p>
          <p>本人確認を行ってください</p>
        </div>
        <div class="close_button-box">
          <div class="close_button-block">
            <label for="triggerVerify" class="close_button left">いいえ</label>
          </div>
          <div class="close_button-block">
            <a href="{{ route('verify.index') }}"><label class="close_button right">本人確認をする</label></a>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <div class="modal_wrap">
    <input id="trigger-freezed-account" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger-freezed-account" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn1">
        <div class="text-box">
          <h2>{{ trans('messages.freezing_account') }}</h2>
        </div>
        <label for="trigger-freezed-account" class="close_button">OK</label>
      </div>
    </div>
  </div>
@endsection
@section('web.content')
  @if(Auth::user() && (Auth::user()->type == \App\Enums\UserType::GUEST))
    <a href="{{route('invite_code.get_invite_code')}}"><img src="/assets/web/images/invite_code/banner_top.png" alt=""></a>
  @endif
  @if (!Auth::check())
    <a href="{{ route('auth.line') }}">
      <img src="{{ asset('images/btn_login_base.png') }}" alt="">
    </a>
  @endif
  @if (isset(request()->first_time) && request()->first_time)
    <a href="javascript:void(0)" class="gtm-hidden-btn" id="first-time-login" name="button1" onclick="dataLayer.push({
      'userId': '<?php echo Auth::user()->id; ?>',
      'event': 'login_complete'
    });"></a>
    <script>
      setTimeout(() => {
          document.getElementById('first-time-login').click();
      }, 500)
    </script>
  @endif
  <div class="top-header-user">
    <div class="wrap-user">
      <div class="wrap-user-left">
        @if (Auth::user()->avatars && !empty(Auth::user()->avatars->first()))
          <img src="{{ Auth::user()->avatars->first()->thumbnail }}" alt="">
        @else
          <img src="{{ asset('assets/web/images/ge1/user_icon.svg') }}" alt="">
        @endif
        @if (Auth::user()->nickname)
        <span class="user-name user-name-nickname">{{ Auth::user()->nickname }}</span>
        @endif
      </div>
      <a href="{{ route('profile.edit') }}" class="edit-button">
        <img src="{{ asset('assets/web/images/ge1/pencil.svg') }}" alt="">
      </a>
    </div>
  </div>
  <div class="prefecture">
    <div class="wrapper-select">
      <img src="{{ asset('assets/web/images/common/map-blue.svg') }}" alt="">
      <form action="">
        <select name="" id="prefecture-id-mypage">
          @foreach($prefectures as $prefecture)
            <option value="{{ $prefecture->id }}" {{ ($prefecture->id == 13) ? 'selected':''}}>{{ $prefecture->name }}</option>
          @endforeach
        </select>
      </form>
      <img src="{{ asset('assets/web/images/common/arrow-blue.svg') }}" alt="">
    </div>
  </div>
  @if ($order->resource)
  <div class="booking">
    <h2>現在の予約</h2>
    <div class="booking-block">
      <div class="booking-date">
        <div class="date-left">
          <span>{{ Carbon\Carbon::parse($order->date)->format('m月d日') }} ({{ dayOfWeek()[Carbon\Carbon::parse($order->date)->dayOfWeek] }})</span>
          <span>{{ $order->address }} {{ \Carbon\Carbon::parse($order->start_time)->format('H:i') }}〜</span>
          <ul>
            @if(count($order->tags))
              @foreach($order->tags as $tag)
                <li>#{{ $tag->name }}</li>
              @endforeach
            @endif
          </ul>
        </div>
        <ul class="date-right">
          <li><img src="{{ asset('assets/web/images/common/glass.svg') }}" alt=""><span>{{ $order->duration }}時間</span></li>
          <li class="init-diamond"><img src="{{ asset('assets/web/images/common/diamond.svg') }}" alt="">
            <span>{{ number_format($order->temp_point) }}P〜</span>
          </li>
          <li><img src="{{ asset('assets/web/images/common/woman.svg') }}" alt=""><span>{{ $order->total_cast }}名</span></li>
        </ul>
        <div class="clear"></div>
      </div>
      <ul class="casts">
        @foreach($order->casts as $cast)
          <li>
            <div class="top-image">
              @if ($cast->avatars->first())
              <img class="lazy" data-src="{{ $cast->avatars->first()->thumbnail }}" alt="">
              @else
              <img class="lazy" data-src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
              @endif
            </div>
          </li>
        @endforeach
      </ul>
      <div class="btn-m cast-message">
        @if (Auth::user()->status)
          @if ($order->casts->first() && $order->room_id)
            <a href="{{ route('message.messages', $order->room_id) }}">メッセージを確認する</a>
          @else
            <a href="{{ route('message.index') }}">メッセージを確認する</a>
          @endif
        @else
        <a href="javascript:void(0)" id="popup-freezed-account">メッセージを確認する</a>
        @endif
      </div>
    </div>
  </div>
  @endif
  @if (Auth::user()->status)
  <a href="{{ route('guest.orders.call') }}" class="cast-call">キャストを呼ぶ<span>複数人、当日以降の予約もOK！</span></a>
  @else
  <a href="javascript:void(0)" class="cast-call" id="popup-freezed-account">キャストを呼ぶ<span>複数人、当日以降の予約もOK！</span></a>
  @endif
  <div class="cast-list">
    <div class="cast-head">
      <h2>在籍中のキャスト</h2>
      @if (Auth::user()->status)
      <a href="{{ route('cast.list_casts') }}"><h2>一覧</h2></a>
      @else
      <a href="javascript:void(0)" id="popup-freezed-account"><h2>一覧</h2></a>
      @endif
    </div>

    <div class="cast-body">
    </div>
  </div>
<!-- Timeline -->
  <div class="timeline">
    <div class="tl-head">
      <h2>キャストのつぶやき</h2>
    </div>

    <div class="tl-list">
    @foreach ($newIntros as $intro)
      <div class="tl-item">
        <div class="tl-item_avatar">
          <a href="{{ Auth::user()->status ? route('cast.show', ['id' => $intro->id]) : 'javascript:void(0)' }}" id="{{ Auth::user()->status ? '' : 'popup-freezed-account' }}">
            <img src="{{ ($intro->avatars && isset($intro->avatars[0]) &&$intro->avatars[0]->thumbnail) ? $intro->avatars[0]->thumbnail :'/assets/web/images/gm1/ic_default_avatar@3x.png' }}" alt="avatar" class="image-intro">
          </a>
        </div>

        <div class="tl-item_info">
          <h3 class="info-title">{{ str_limit($intro->nickname, 15) }}  {{ $intro->age }}歳</h3>
          <p class="info-text">{{ $intro->intro }}</p>
        </div>
      </div>
    @endforeach

    </div>
  </div>
  <div class="wrap-banner-methods-used">
    <a href="/service/guest_ht"><img src="/assets/web/images/ge1/banner_methods_used.png" alt=""></a>
  </div>
@endsection
@section('web.script')
  @if (Session::has('no_active'))
    <script>
      jQuery(document).ready(function($) {
        $('#trigger-freezed-account').trigger('click');
      })
    </script>
  @endif
  <script>
    jQuery(document).ready(function($) {
      $('body').on('click', '#popup-freezed-account', function () {
        $('#trigger-freezed-account').trigger('click');
      })
    })
  </script>
  <script>
    $(function () {
      var popup_mypage = window.sessionStorage.getItem('popup_mypage');

      if (popup_mypage) {
        $('#profile-popup').trigger('click');
        $('#profile-message h2').html(popup_mypage);
        window.sessionStorage.removeItem('popup_mypage');

        setTimeout(() => {
          $('#input_birthday_modal').css('display', 'none');
        }, 3000);
      }
    })

    if(localStorage.getItem("order_call")){
      localStorage.removeItem("order_call");
    }

    if (localStorage.getItem("order_offer")) {
      localStorage.removeItem("order_offer");
    }

    if(localStorage.getItem("reason1")){
      localStorage.removeItem("reason1");
    }

    if(localStorage.getItem("reason2")){
      localStorage.removeItem("reason2");
    }

    if(localStorage.getItem("reason3")){
      localStorage.removeItem("reason3");
    }

    if(localStorage.getItem("other_reason")){
      localStorage.removeItem("other_reason");
    }

    localStorage.removeItem("textarea_reason");
  </script>
@endsection
