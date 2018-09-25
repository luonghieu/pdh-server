@section('title', 'Cheers')
@extends('layouts.web')
@section('web.extra_css')
<link rel="stylesheet" href="{{ asset('assets/web/css/ge_1.css') }}">
@endsection
@section('web.extra')
  <div class="modal_wrap">
    <input id="trigger" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger" class="modal_trigger"></label>
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
@endsection
@section('web.content')
  @if (!Auth::check())
    <a href="{{ route('auth.line') }}">
      <img src="{{ asset('images/btn_login_base.png') }}" alt="">
    </a>
  @endif
  <section class="button-box" style="display: none;">
    <label for="trigger" class="open_button button-settlement"></label>
  </section>
  <div class="top-header">
    <div class="user-data">
      <div class="user-icon">
        @if (Auth::user()->avatars && !empty(Auth::user()->avatars->first()->path))
        <img src="{{ Auth::user()->avatars->first()->path }}" alt="">
        @else
        <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
        @endif
      </div>
      <a href="#" class="edit-button">
        <img src="{{ asset('assets/web/images/ge1/pencil.svg') }}" alt="">
      </a>
    </div>
    @if (Auth::user()->nickname)
    <span class="user-name">{{ Auth::user()->nickname }}</span>
    @endif
  </div>
  <a href="{{ route('guest.orders.call') }}" class="cast-call">今すぐキャストを呼ぶ<span>最短20分で合流!</span></a>
  <div class="booking">
    <h2>現在の予約</h2>
    <div class="booking-block">
      <div class="booking-date">
        <div class="date-left">
          <span>{{ \Carbon\Carbon::parse($order->date_start)->format('m月d日') }}(土)</span>
          <span>西麻布 {{ \Carbon\Carbon::parse($order->start_time)->format('h:i') }}〜</span>
          <ul>
            <li>#ワイワイ</li>
            <li>#カラオケ</li>
          </ul>
        </div>
        <ul class="date-right">
          <li><img src="{{ asset('assets/web/images/common/glass.svg') }}" alt=""><span>{{ $order->duration }}時間</span></li>
          <li><img src="{{ asset('assets/web/images/common/diamond.svg') }}" alt=""><span>{{ number_format($order->total_point) }}P〜</span></li>
          <li><img src="{{ asset('assets/web/images/common/woman.svg') }}" alt=""><span>{{ $order->total_cast }}名</span></li>
        </ul>
      </div>
      <ul class="casts">
        @foreach($order->casts as $cast)
          <li><img src="{{ $cast->avatars->first()->thumbnail }}" alt=""></li>
        @endforeach

      </ul>
      <div class="btn-m cast-message">
        <a href="#">メッセージを確認する</a>
      </div>
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
@endsection
