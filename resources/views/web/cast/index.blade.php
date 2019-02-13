@section('title', 'Cheers')
@extends('layouts.web')
@section('web.extra_css')
<link rel="stylesheet" href="{{ mix('assets/web/css/ge_1.min.css') }}">
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
      <span class="total-point-cast">{{ number_format($user->total_point + $user->point) }}P</span>
      <div class="user-icon init-image-radius" id="cast-icon">
        @if ($user->avatars && !empty($user->avatars->first()->thumbnail))
          <img src="{{ $user->avatars->first()->thumbnail }}" alt="">
        @else
          <img src="{{ asset('assets/web/images/ge1/user_icon.svg') }}" alt="">
        @endif
      </div>
    </div>
    @if ($user->nickname)
    <span class="user-name">{{ $user->nickname }}</span>
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
        <a href="{{ route('cast_mypage.bank_account.index') }}">
          <div class="rounded-circle m-rounded-circle">
            <img src="{{ asset('assets/web/images/cast/ic_pig_blue.svg') }}">
            <div class="text-center m-auto">振込口座</div>
          </div>
        </a>
      </div>
      <div class="m-circle ">
        <a href="{{ route('cast.transfer_history') }}">
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
      @php
        $arrCost = [];
        for($i =500; $i<=15000; $i+=100) {
          array_push($arrCost, $i);
        }

        if(!in_array($user->cost,$arrCost)) {
          array_push($arrCost, $user->cost);
        }

        sort($arrCost);
      @endphp
        <select name="point_cast" id="point-cast" disabled>
          @foreach($arrCost as $cost)
          <option value="{{ $cost }}" {{ $user->cost == $cost ? 'selected' : ''}}>{{number_format($cost) }}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>
  <section class="button-box">
    <label for="lb-update-cost" class="update-cost"></label>
  </section>
  <a href="javascript:void(0)" id="change-point">変更する</a>
  <div class="rank-schedule">
    <p>現在のキャストクラス　{{$castClass->name}}</p>
    @if($user->class_id == 1)
      <p>プラチナクラスまで‥</p>
    @elseif($user->class_id == 2)
      <p>プラチナクラスキープまで‥</p>
    @endif
    <div class="times-ordered">
      <div class="title-times-ordered">参加回数</div>
      <div class="chart-times-ordered">
        <div class="wrapper-rank-schedule">
          <ul class="indicators">
            @for($i = 0; $i < 10; $i++)
            <li>$i</li>
              @endfor
          </ul>
          <div class="progress-wrapper">
            <div class="progress-bar"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="clear"></div>
    <div class="times-ordered">
      <div class="title-times-ordered">平均評価</div>
      <div class="chart-times-ordered">
        <div class="wrapper-rank-schedule">
          <ul class="indicators">
            <li>0</li>
            <li>1</li>
            <li>2</li>
            <li>3</li>
            <li>4</li>
            <li></li>
          </ul>
          <div class="progress-wrapper">
            <div class="progress-bar-avg"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="clear"></div>
    <p>※次回クラス変更日‥◯月◯日</p>
    <p>※ダイヤモンドクラスへの</p>
  </div>
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

