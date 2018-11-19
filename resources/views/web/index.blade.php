@section('title', 'Cheers')
@section('controller.id', 'top')
@extends('layouts.web')
@section('web.extra_css')
<link rel="stylesheet" href="{{ mix('assets/web/css/ge_1.min.css') }}">
@endsection
@section('web.extra')
  <form action="#" method="GET" id="update-date-of-birth">
    {{ csrf_field() }}
    <div class="modal_wrap modal5" id="popup-top">
      <input id="popup-date-of-birth" type="checkbox">
      <div class="modal_overlay">
        <div class="modal_content modal_content-btn5">
          <div class="text-box">
            <h2>生年月日の登録をしよう!</h2>
            <div>
              @php
                $max = \Carbon\Carbon::parse(now())->subYear(20);
              @endphp
              <input type="date" id="date-of-birth" name="date_of_birth" data-date="" max="{{ $max->format('Y-m-d') }}" data-date-format="YYYY年MM月DD日" value="{{ \Carbon\Carbon::parse(Auth::user()->date_of_birth)->format('Y-m-d') }}">
            </div>
            <label data-field="date_of_birth" id="date-of-birth-error" class="error help-block" for="date-of-birth"></label>
          </div>
          <button type="submit" for="popup-date-of-birth" class="close_button">登録する</button>
        </div>
      </div>
    </div>
  </form>
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
    @if(Auth::user()->is_guest && Carbon\Carbon::parse(Auth::user()->created_at)->lt(Carbon\Carbon::parse('2018/11/10 00:00')) && Auth::user()->is_verified)
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
@endsection
@section('web.content')
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

  <section class="button-box" style="display: none;">
    <label for="popup-date-of-birth" class="open_button button-settlement"></label>
  </section>
  <div class="top-header">
    <div class="user-data">
      <div class="user-icon init-image-radius">
        @if (Auth::user()->avatars && !empty(Auth::user()->avatars->first()->thumbnail))
          <img src="{{ Auth::user()->avatars->first()->thumbnail }}" alt="">
        @else
          <img src="{{ asset('assets/web/images/ge1/user_icon.svg') }}" alt="">
        @endif
      </div>
      <a href="{{ route('profile.edit') }}" class="edit-button">
        <img src="{{ asset('assets/web/images/ge1/pencil.svg') }}" alt="">
      </a>
    </div>
    @if (Auth::user()->nickname)
    <span class="user-name">{{ Auth::user()->nickname }}</span>
    @endif
  </div>
  <a href="{{ route('guest.orders.call') }}" class="cast-call">今すぐキャストを呼ぶ<span>最短20分で合流!</span></a>
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
              @if (@getimagesize($cast->avatars->first()->thumbnail))
              <img class="lazy" data-src="{{ $cast->avatars->first()->thumbnail }}" alt="">
              @else
              <img class="lazy" data-src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
              @endif
            </div>
          </li>
        @endforeach
      </ul>
      <div class="btn-m cast-message">
        <a href="{{ route('message.messages', $order->room_id) }}">メッセージを確認する</a>
      </div>
    </div>
  </div>
  @endif
  <div class="cast-list">
    <div class="cast-head">
      <h2>在籍中のキャスト</h2>
      <a href="{{ route('cast.list_casts') }}"><img class="head-icon" src="/assets/web/images/common/arrow-right.svg" alt="arrow-right"></a>
    </div>

    <div class="cast-body">
      @foreach ($casts as $cast)
        <div class="cast-item">
          <a href="{{ route('cast.show', ['id' => $cast->id]) }}">
            @php
              if($cast->class_id == 1) {
                $class = 'cast-class_b';
              }

              if($cast->class_id == 2) {
                $class = 'cast-class_p';
              }

              if($cast->class_id == 3) {
                $class = 'cast-class_d';
              }

            @endphp
            <span class="tag {{ $class }}">{{ $cast->class }}</span>
            <img src="{{ ($cast->avatars && @getimagesize($cast->avatars[0]->thumbnail)) ? $cast->avatars[0]->thumbnail :'/assets/web/images/gm1/ic_default_avatar@3x.png' }}">
            <div class="info">
              <span class="tick {{ $cast->is_online == 1? 'tick-online':'tick-offline' }}"></span>
              <span class="title-info">{{ str_limit($cast->job, 15) }}  {{ $cast->age }}歳</span>
              <div class="wrap-description">
                <span class="description">{{ $cast->intro }}</span>
              </div>
            </div>
          </a>
        </div>
      @endforeach

      <a href="{{ route('cast.list_casts') }}" class="cast-item import"></a>
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
          <a href="{{ route('cast.show', ['id' => $intro->id]) }}"><img src="{{ ($intro->avatars && @getimagesize($intro->avatars[0]->thumbnail)) ? $intro->avatars[0]->thumbnail :'/assets/web/images/gm1/ic_default_avatar@3x.png' }}" alt="avatar"></a>
        </div>

        <div class="tl-item_info">
          <h3 class="info-title">{{ str_limit($intro->nickname, 15) }}  {{ $intro->age }}歳</h3>
          <p class="info-text">{{ $intro->intro }}</p>
        </div>
      </div>
    @endforeach

    </div>
  </div>
@endsection
@section('web.script')
  @if(empty(Auth::user()->date_of_birth) && Auth::user()->is_verified)
    <script>
      $(function () {
        $('.open_button').trigger('click');
      });
    </script>
  @endif
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

    if(localStorage.getItem("back_link")){
      localStorage.removeItem("back_link");
    }

    if(localStorage.getItem("order_call")){
      localStorage.removeItem("order_call");
    }
  </script>
@endsection
