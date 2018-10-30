@section('title', 'Cheers')
@extends('layouts.web')
@section('web.extra_css')
<link rel="stylesheet" href="{{ asset('assets/web/css/ge_1.css') }}">

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
  <div class="top-header" id="top-header-cast">
    <div class="user-data">
      <span class="total-point-title">あなたの売上合計</span>
      <span class="total-point-cast">{{ number_format($user->total_point) }}P</span>
      <div class="user-icon init-image-radius" id="cast-icon">
        @if (Auth::user()->avatars && !empty($user->avatars->first()->thumbnail))
          <img src="{{ $user->avatars->first()->thumbnail }}" alt="">
        @else
          <img src="{{ asset('assets/web/images/ge1/user_icon.svg') }}" alt="">
        @endif
      </div>
    </div>
    @if (Auth::user()->nickname)
    <span class="user-name">{{ Auth::user()->nickname }}</span>
    @endif
  </div>
  <div class="cast-call point-transfer">
    <div class="point-title">
      <img id="img-point" style="opacity: 0" src="{{ asset('assets/web/images/cast/ic_p_blue.svg') }}">
      <span>
        未振込ポイント
      </span>
    </div>
      <span class="point-show" style="opacity: 0">{{number_format($user->point) }}P</span>
  </div>
  <div class="clear"></div>
  <div class="cast-call btn-circle" id="btn-circle">
    <div class="display-flex">
      <div class="m-circle ">
        <a href="{{ route('bank_account.index') }}">
          <div class="rounded-circle m-rounded-circle">
            <img src="{{ asset('assets/web/images/cast/ic_pig_blue.svg') }}">
            <div class="text-center m-auto">振込口座</div>
          </div>
        </a>
      </div>
      <div class="m-circle ">
        <a href="{{ route('cast.payments') }}">
          <div class="rounded-circle m-rounded-circle">
            <img src="{{ asset('assets/web/images/cast/ic_point_white.svg') }}">
            <div class="text-center m-auto">振込履歴</div>
          </div>
        </a>
      </div>
      <div class="m-circle ">
        <a href="<?php echo env('APP_URL') . '/service/cast_qa' ?>">
          <div class="rounded-circle m-rounded-circle">
            <img src="{{ asset('assets/web/images/cast/ic_question_white.png') }}">
            <div class="text-center m-auto ct-circle">よくある質問</div>
          </div>
        </a>
      </div>
    </div>
  </div>
  <div class="clear"></div>
  <div class="cast-call" id="custom-point">
    <div class="expiration-date border-bottom">
      <span class="left" id="sp-text-point">30分あたりのポイント</span>
      <div class="date-select select-point">
        <select name="point_cast" id="point-cast" disabled>
          @for($i =500; $i<=15000; $i+=100)
          <option value="{{ $i }}" {{ $user->cost == $i ? 'selected' : ''}}>{{number_format($i) }}</option>
          @endfor
        </select>
      </div>
    </div>
  </div>
  <section class="button-box">
    <label for="lb-update-cost" class="update-cost"></label>
  </section>
  <a href="javascript:void(0)" id="change-point">変更する</a>
  @if($token)
    <script>
        window.localStorage.setItem('access_token', '{{ $token }}');
    </script>
  @endif
@endsection

@section('web.extra')
  <div class="modal_wrap">
    <input id="update-point-alert" type="checkbox">
    <div class="modal_overlay">
        <label for="update-point-alert" class="modal_trigger" id="update-point-alert"></label>
        <div class="modal_content modal_content-btn3">
            <div class="content-in">
                <h2 id="update-point-success"></h2>
            </div>
        </div>
    </div>
  </div>

  @confirm(['triggerId' => 'lb-update-cost', 'triggerCancel' =>'', 'buttonLeft' =>'いいえ',
   'buttonRight' =>'登録する','triggerSuccess' =>'right cf-update-cost'])

    @slot('title')
      30分あたりのポイントを変更しますか？
    @endslot

    @slot('content')
    @endslot
  @endconfirm
@endsection

<script>
  function responsivePoint () {
    var widthPoint = $('.point-show').width();
    if(100 <= widthPoint) {
      $('#img-point').css({'margin-left' : '12px'});
      $('.point-show').css({'margin' : '5px 9px 0 0', 'font-size' : '19px'});
    }

    $('#img-point').css('opacity',1);
    $('.point-show').css('opacity',1);
  }
  window.onload = responsivePoint;
</script>

