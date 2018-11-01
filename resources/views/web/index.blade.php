@section('title', 'Cheers')
@section('controller.id', 'top')
@extends('layouts.web')
@section('web.extra_css')
<link rel="stylesheet" href="{{ asset('assets/web/css/ge_1.css') }}">
@endsection
@section('web.extra')
  <div class="modal_wrap">
    <input id="trigger" type="checkbox">
    <div class="modal_overlay">
      <div class="modal_content modal_content-btn1">
      <div class="text-box">
        <h2>Cheersへようこそ！！</h2>
        <p>プロフィールの登録をしてください </p>
      </div>
      <form action="{{ route('profile.edit') }}" method="GET" id="redirect-url">
        {{ csrf_field() }}
        <label for="trigger" class="close_button">プロフィールを登録する</label>
      </form>
      </div>
    </div>
  </div>

  <div class="modal_wrap">
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
    <label for="trigger" class="open_button button-settlement"></label>
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
          <ul class="css-mypage pb">
            @if(count($order->tags))
              @foreach($order->tags as $tag)
                <li>#{{ $tag->name }}</li>
              @endforeach
            @endif
          </ul>
        </div>
        <ul class="date-right">
          <li><img src="{{ asset('assets/web/images/common/glass.svg') }}" alt=""><span>{{ $order->duration }}時間</span></li>
          <li><img src="{{ asset('assets/web/images/common/diamond.svg') }}" alt="">
            <span>{{ number_format($order->temp_point) }}P〜</span>
          </li>
          <li><img src="{{ asset('assets/web/images/common/woman.svg') }}" alt=""><span>{{ $order->total_cast }}名</span></li>
        </ul>
      </div>
      <ul class="casts">
        @foreach($order->casts as $cast)
          <li>
            <div class="top-image">
              @if (@getimagesize($cast->avatars->first()->thumbnail))
              <img src="{{ $cast->avatars->first()->thumbnail }}" alt="">
              @else
              <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
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
        <span class="tag">{{ $cast->class }}</span>
        <img src="{{ ($cast->avatars && @getimagesize($cast->avatars[0]->thumbnail)) ? $cast->avatars[0]->thumbnail :'/assets/web/images/gm1/ic_default_avatar@3x.png' }}">
        <div class="info">
          <span class="tick {{ $cast->is_online == 1? 'tick-online':'tick-offline' }}"></span>
          <span class="title-info">{{ $cast->job }}  {{ $cast->age }}歳</span>
          <span class="description">{{ substr($cast->intro,0,30).'...' }}</span>
        </div>
      </div>
      @endforeach

      <a href="{{ route('cast.list_casts') }}" class="cast-item import"></a>
    </div>
  </div>
  @if($token)
    <script>
        window.localStorage.setItem('access_token', '{{ $token }}');
    </script>
  @endif
@endsection
@section('web.script')
  @if(empty(Auth::user()->nickname) || empty(Auth::user()->date_of_birth) || empty(Auth::user()->avatars[0]))
    <script>
      $(function () {
        $('.open_button').trigger('click');

        $('#redirect-url').click(function(e){
          window.location = '/profile/edit';
        });
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
      }
    })

    if(localStorage.getItem("back_link")){
      localStorage.removeItem("back_link");
    }

  </script>
@endsection
