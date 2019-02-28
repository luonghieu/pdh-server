@section('title', 'キャスト予約')
@section('screen.id', 'ge2-1-x')
@extends('layouts.web')
@section('web.content')
  <div class="reservation-item">
    <div class="caption"><!-- 見出し用div -->
      <h2>キャストを呼ぶ場所</h2>
      <label class="place">
        <!-- アイコン -->
        <div class="selectbox">
          <select class="select-prefecture" name="" id="prefecture">
            @foreach ($prefectures as $prefecture)
              <option value="{{ $prefecture['id'] }}" >{{ $prefecture['name'] }}</option>
            @endforeach
          </select>
          <i></i>
        </div>
      </label>
    </div>
    <div class="form-grpup" id="list-municipalities"><!-- フォーム内容 -->

    </div>
  </div>

  <div class="reservation-item">
    <div class="caption"><!-- 見出し用div -->
      <h2>キャストとの合流時間</h2>
    </div>
    <div class="form-grpup"><!-- フォーム内容 -->
      @if(isset($orderOptions['call_time']))
        @foreach($orderOptions['call_time'] as $callTime)
        <label class="button button--green date {{ $callTime['value'] == 30 ? 'active' : '' }} {{ !$callTime['is_active'] ? 'inactive' : '' }} ">
          <input type="radio" name="time_join" value="{{ $callTime['value'] }}"  {{ !$callTime['is_active'] ? 'disabled' : '' }} {{ ($callTime['value'] == 30) ? 'checked' : '' }} class="time-join-call">
          {{ $callTime['name'] }}
        </label>
        @endforeach
      @endif
      <label id="date_input" class="button button--green date " >
        <input type="radio" name="time_join" value="other_time" class="time-join-call">それ以外
      </label>
      <label class="date-input date-input-call" >
        <span>希望日時</span>
        <p class="date-input__text">
          <span class='sp-month month-call'></span>
          <span class='sp-date date-call'></span>
          <span class="sp-time time-call"></span>
        </p>
      </label>
    </div>
  </div>
  <div class="reservation-item">
    <div class="caption"><!-- 見出し用div -->
      <h2>キャストの人数</h2>
      <section class="button-box">
        <label for="notification" class="open_button button-settlement">最低人数について</label>
      </section>
    </div>
    <div class="form-grpup"><!-- フォーム内容 -->
      <div class="cast-number">
        <!-- アイコン -->
        <span class="cast-number__text">キャスト人数</span>
        <label class="cast-number__value"><input type="text" value="1" name="txtCast_Number" readonly id="cast-number-call">人</label>

        <button class="cast-number__button-minus" type="button" name="button"></button>
        <button class="cast-number__button-plus" type="button" name="button"></button>
        @if(isset($orderOptions['max_casts']))
        <input type="hidden" value="{{ $orderOptions['max_casts'] }}" id="max_casts">
        @endif
      </div>
      @php
        $campaignFrom = Carbon\Carbon::parse('2018-11-28');
        $campaignTo = Carbon\Carbon::parse('2018-11-30 23:59:59');
      @endphp
      @if(Auth::user()->is_guest && Auth::user()->is_verified && !Auth::user()->campaign_participated && now()->between($campaignFrom, $campaignTo))
        <div class="notify-campaign-over">
          <span>※3名はキャンペーン対象外です</span>
        </div>
      @endif
    </div>
  </div>
  <div class="reservation-item">
    <div class="caption"><!-- 見出し用div -->
      <h2>キャストを呼ぶ時間</h2>
    </div>
    <div class="form-grpup"><!-- フォーム内容 -->
      <label class="button button--green time">
        <input type="radio" name="time_set" value="1" >1時間
      </label>
      <label class="button button--green time">
        <input type="radio" name="time_set" value="2" >2時間
      </label>
      <label class="button button--green time">
        <input type="radio" name="time_set" value="3" >3時間
      </label>
      <label id="time-input" class="button button--green time">
        <input type="radio" name="time_set" value="other_duration" >4時間以上
      </label>
      <label class="time-input">
        <span>呼ぶ時間</span>
        <div class="selectbox">
          <select id="select-duration-call" name="sl_duration">
            <option value="4" >4時間</option>
            <option value="5" >5時間</option>
            <option value="6" >6時間</option>
            <option value="7" >7時間</option>
            <option value="8" >8時間</option>
            <option value="9" >9時間</option>
            <option value="10" >10時間</option>
          </select>
          <i></i>
        </div>
      </label>
    </div>
  </div>
  <!-- show coupon -->
  <div id="show-coupon-order-call"></div>

  <div class="reservation-item">
    <div class="caption"><!-- 見出し用div -->
      <h2>キャストのクラス</h2>
    </div>
    <div class="form-grpup"><!-- フォーム内容 -->
      <div class="grade-list">
        @if(isset($orderOptions['cast_classes']))
          @php
            rsort($orderOptions['cast_classes']);
          @endphp
          @foreach($orderOptions['cast_classes'] as $castClass)
            <label>
              <img src="{{ asset($castClass['url_image']) }}" alt="">
              <span class="cast_class" id="cast_class" >{{ $castClass['name'] }}</span>
              <span class="cast_price">{{ number_format($castClass['cost']) }}P/30分</span>
              <input type="radio" name="cast_class" class="grade-radio" value="{{ $castClass['id'] }}" data-name = "{{ $castClass['name'] }}" >
            </label>
          @endforeach
        @endif
      </div>
      @if(Auth::user()->is_guest && Auth::user()->is_verified && !Auth::user()->campaign_participated && now()->between($campaignFrom, $campaignTo))
        <div class="notify-campaign-over-cast-class">
          <span>※3名はキャンペーン対象外です</span>
        </div>
      @endif
    </div>
  </div>
  <div class="overlay">
    <div class="date-select ct-date-select">
      <div class="date-select__content">
          @php
            $now = \Carbon\Carbon::now()->addMinutes(30);
            $currentMonth = $now->format('m');
            $currentDate = $now->format('d');
            $currentHour = $now->format('H');
            $currentMinute = $now->format('i');

          @endphp
          <select class="select-month" name="sl_month">
            @foreach(range(1, 12) as $month)
              <option value="{{ $month }}" {{ $currentMonth == $month ? 'selected' : '' }}>{{ $month }}月</option>
            @endforeach
          </select>

          <select class="select-date" name="sl_date">
            @foreach(getDay() as $key => $val)
             <option value="{{ $key }}" {{ $currentDate == $key ? 'selected' : '' }}>{{ $val }}</option>
            @endforeach
          </select>

         <select class="select-hour" name="sl_hour">
          @foreach(range(00, 23) as $hour)
           <option value="{{ $hour }}" {{ $currentHour == $hour ? 'selected' : '' }}>
                {{ $hour<10 ? '0'.$hour : $hour }}時
          </option>
          @endforeach
         </select>

         <select class="select-minute" name="sl_minute">
           @foreach(range(00, 59) as $minute)
           <option value="{{ $minute }}" {{ $currentMinute == $minute ? 'selected' : '' }}>
                {{ $minute<10 ? '0'.$minute : $minute }}分
          </option>
          @endforeach
         </select>

      </div>
      <div class="date-select__footer">
        <button class="date-select__cancel btn-date-select" type="button">キャンセル</button>
        <button class="date-select__ok date-select-nomination btn-date-select" type="button">完了</button>
      </div>
    </div>
  </div>
  <form action="{{ route('guest.orders.get_step2') }}" method="GET">
    <button type="submit" class="form_footer ct-button disable" id="step1-create-call" disabled>
      次に進む (1/3)
    </button>
  </form>
@endsection

@section('web.extra')
  @modal(['triggerId' => 'notification', 'triggerClass' =>''])
    @slot('title')
      男性の人数に対してキャストの呼ぶ人数が少ないのはNGです。
    @endslot

    @slot('content')
      ※違反が発覚した場合は最低人数分の料金をお支払いただきます。
    @endslot
  @endmodal
@endsection

@section('web.script')
<script>
  window.addEventListener("pageshow", function (event) {
    var historyTraversal = event.persisted || (
      typeof window.performance != "undefined" &&
      window.performance.navigation.type === 2
    );

    if (historyTraversal) {
      window.location.replace(window.location.href);
    }
  });
</script>
@endsection
