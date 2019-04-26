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
  <div class="top-header-cast">
    <div class="wrap-cast">
      <div class="wrap-cast-left">
        @if (Auth::user()->avatars && !empty(Auth::user()->avatars->first()))
          <img src="{{ Auth::user()->avatars->first()->thumbnail }}" alt="">
        @else
          <img src="{{ asset('assets/web/images/ge1/user_icon.svg') }}" alt="">
        @endif
        @if (Auth::user()->nickname)
          <span class="user-name user-name-nickname">{{ Auth::user()->nickname }}</span>
        @endif
      </div>
      <a href="{{ route('profile.edit') }}" class="btn-edit-cast">
        <img src="{{ asset('assets/web/images/ge1/pencil.svg') }}" alt="">
      </a>
    </div>
  </div>
  <div class="top-header" id="top-header-cast">
    <div class="user-data">
      <span class="total-point-title">あなたの売上合計</span>
      <span class="total-point-cast">{{ number_format($user->total_point + $user->point) }}P</span>
    </div>
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
  <span class="ml-2">※入力した金額の{{ $user->cost_rate * 100 }}割が振り込まれます。</span>
  <a href="javascript:void(0)" id="change-point">変更する</a>
  <div class="cast-call btn-circle wrap-btn-circle" id="btn-circle">
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
  @php
  $now = now()->format('Y-m-d');
  @endphp
  @if($user->class_id != 3 && $rankSchedule && $rankSchedule->from_date <= $now && $rankSchedule->to_date >= $now)
  <div class="time-rank-schedule">
    <span>対象期間：{{Carbon\Carbon::parse($rankSchedule->from_date)->format('Y/m/d')}}〜{{Carbon\Carbon::parse($rankSchedule->to_date)->format('Y/m/d')}}</span>
  </div>
  <div class="rank-schedule">
    @php
      if ($castClass->id == 1) {
        $class = 'class_b';
      }

      if ($castClass->id == 2) {
        $class = 'class_p';
      }

      if ($castClass->id == 3) {
        $class = 'class_d';
      }

      if ($user->class_id == 1) {
        $numOfAttendPlatium = $rankSchedule->num_of_attend_up_platium;
        $numOfAvgRatePlatium = $rankSchedule->num_of_avg_rate_up_platium;
      } else {
        $numOfAttendPlatium = $rankSchedule->num_of_attend_platium;
        $numOfAvgRatePlatium = $rankSchedule->num_of_avg_rate_platium;
      }

      $orderNotJoined = $numOfAttendPlatium - $sumOrders;

      $percentagePointAverage = ($ratingScore*100)/$numOfAvgRatePlatium;
    @endphp
    <p class="rank-schedule-cast-class">あなたのキャストクラス　<span class="{{$class}} cast-class-tag">{{$castClass ? $castClass->name : ''}}</span></p>

    @if (($sumOrders >= $numOfAttendPlatium) && ($ratingScore >= $numOfAvgRatePlatium) && $user->class_id == 1)
    <p class="notify-rank-schedule"><span class="green-text">プラチナクラス</span><span class="gray-text">へ</span><span class="green-text">クラスアップ！</span></p>
    @else
      @if($user->class_id == 1)
        <p class="notify-rank-schedule"><span class="green-text">プラチナクラス</span><span class="gray-text">へ</span><span class="green-text">アップ</span><span class="gray-text">まで</span></p>
      @elseif($user->class_id == 2)
        <p class="notify-rank-schedule"><span class="green-text">プラチナクラス</span><span class="gray-text">の</span><span class="green-text">キープ</span><span class="gray-text">まで</span></p>
      @endif
    @endif
    <div class="times-ordered">
      <div class="title-times-ordered">参加回数</div>
      <div class="chart-times-ordered">
        <div class="wrapper-rank-schedule">
          <ul class="indicators">
            @if($sumOrders > $numOfAttendPlatium)
              @for($i = 0; $i < $numOfAttendPlatium; $i++)
                <li><img src="/assets/web/images/common/ic_glass_blue.svg" alt=""></li>
              @endfor
            @else
              @for($i = 0; $i < $sumOrders; $i++)
                <li><img src="/assets/web/images/common/ic_glass_blue.svg" alt=""></li>
              @endfor
              @for($i = 0; $i < $orderNotJoined; $i++)
                <li class="ic-glass-off"><img src="/assets/web/images/common/ic_glass_gray.svg" alt=""></li>
              @endfor
            @endif
          </ul>
        </div>
      </div>
      <div class="detail-num-order"><span class="green-text">{{$sumOrders}}</span><span class="gray-text">/{{$numOfAttendPlatium}}</span></div>
    </div>
    <div class="clear"></div>
    <div class="times-ordered">
      <div class="title-times-ordered avg-title">平均評価</div>
      <div class="chart-times-ordered">
        <div class="wrapper-rank-schedule">
          <div class="star-rating-schedule">
            <span style="width: {{$percentagePointAverage.'%'}}"></span>
          </div>
        </div>
      </div>
      <div class="detail-num-order"><span class="green-text">{{$ratingScore}}</span><span class="gray-text">/{{$numOfAvgRatePlatium}}</span></div>
    </div>
    <div class="clear"></div>
  </div>
  @endif
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
