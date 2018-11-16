@section('title', 'キャスト予約')
@section('screen.id', 'ge2-1-x')
@extends('layouts.web')
@section('web.content')
<form action="{{ route('guest.orders.post_call') }}" method="POST" class="create-call-form" id="" name="create_call_form">
  {{ csrf_field() }}
  <div class="reservation-item">
    <div class="caption"><!-- 見出し用div -->
      <h2>キャストを呼ぶ場所</h2>
      <label class="place">
        <!-- アイコン -->
        <div class="selectbox">
          <select class="" name="">
            <option value="">東京</option>
          </select>
          <i></i>
        </div>
      </label>
    </div>
    <div class="form-grpup"><!-- フォーム内容 -->
      <label class="button button--green area {{ (isset($currentArea) && $currentArea == '六本木') ? 'active' : '' }}">
        <input type="radio" name="area" value="六本木"
        {{ (isset($currentArea) && $currentArea == '六本木') ? 'checked="checked"' : '' }} >六本木</label>
      <label class="button button--green area {{ (isset($currentArea) && $currentArea == '恵比寿') ? 'active' : '' }}">
        <input type="radio" name="area" value="恵比寿"
        {{ (isset($currentArea) && $currentArea == '恵比寿') ? 'checked="checked"' : '' }}>恵比寿</label>
      <label class="button button--green area {{ (isset($currentArea) && $currentArea == '西麻布') ? 'active' : '' }}">
        <input type="radio" name="area" value="西麻布"
        {{ (isset($currentArea) && $currentArea == '西麻布') ? 'checked="checked"' : '' }}>西麻布</label>
      <label class="button button--green area {{ (isset($currentArea) && $currentArea == '渋谷') ? 'active' : '' }}">
        <input type="radio" name="area" value="渋谷"
        {{ (isset($currentArea) && $currentArea == '渋谷') ? 'checked="checked"' : '' }}>渋谷</label>
      <label class="button button--green area {{ (isset($currentArea) && $currentArea == '赤坂') ? 'active' : '' }}">
        <input type="radio" name="area" value="赤坂"
        {{ (isset($currentArea) && $currentArea == '赤坂') ? 'checked="checked"' : '' }}>赤坂</label>
      <label class="button button--green area {{ (isset($currentArea) && $currentArea == '銀座') ? 'active' : '' }}">
        <input type="radio" name="area" value="銀座"
        {{ (isset($currentArea) && $currentArea == '銀座') ? 'checked="checked"' : '' }}>銀座</label>
      <label class="button button--green area {{ (isset($currentArea) && $currentArea == '中目黒') ? 'active' : '' }}">
        <input type="radio" name="area" value="中目黒"
        {{ (isset($currentArea) && $currentArea == '中目黒') ? 'checked="checked"' : '' }}>中目黒</label>
      <label class="button button--green area {{ (isset($currentArea) && $currentArea == '新橋') ? 'active' : '' }}" >
        <input type="radio" name="area" value="新橋"
        {{ (isset($currentArea) && $currentArea == '新橋') ? 'checked="checked"' : '' }}>新橋</label>
      <label class="button button--green area {{ (isset($currentArea) && $currentArea == '池袋') ? 'active' : '' }}">
        <input type="radio" name="area" value="池袋"
        {{ (isset($currentArea) && $currentArea == '池袋') ? 'checked="checked"' : '' }}>池袋</label>
      <label class="button button--green area {{ (isset($currentArea) && $currentArea == '新宿') ? 'active' : '' }}">
        <input type="radio" name="area" value="新宿"
        {{ (isset($currentArea) && $currentArea == '新宿') ? 'checked="checked"' : '' }}>新宿</label>
      <label id="area_input" class="button button--green area {{ (isset($currentOtherArea)) ? 'active' : '' }}">
        <input type="radio" name="area" value="その他" {{ (isset($currentOtherArea)) ? 'checked="checked"' : '' }}>その他</label>
      <label class="area-input" style="{{ (isset($currentOtherArea)) ? 'display: flex;' : '' }}">
        <span>希望エリア</span>
        <input type="text" placeholder="入力してください" name="other_area" value="{{ $currentOtherArea or '' }}"
       >
     </label>
    </div>
  </div>

  <div class="reservation-item">
    <div class="caption"><!-- 見出し用div -->
      <h2>キャストとの合流時間</h2>
    </div>
    <div class="form-grpup"><!-- フォーム内容 -->
      @if(isset($orderOptions['call_time']))
        @foreach($orderOptions['call_time'] as $callTime)
        <label class="button button--green date {{ ($callTime['value'] == 60 && (!isset($currentTime) && !isset($timeDetail))) ? 'active' : '' }} {{ (isset($currentTime) && $currentTime == $callTime['value'] ) ? 'active' : '' }} {{ !$callTime['is_active'] ? 'inactive' : '' }}">
          <input type="radio" name="time_join" value="{{ $callTime['value'] }}" {{ (isset($currentTime) && $currentTime == $callTime['value']) ? 'checked="checked"' : '' }} {{ !$callTime['is_active'] ? 'disabled' : '' }} {{ ($callTime['value'] == 60 && (!isset($currentTime) && !isset($timeDetail))) ? 'checked' : '' }}>
          {{ $callTime['name'] }}
        </label>
        @endforeach
      @endif
      <label id="date_input" class="button button--green date {{ (isset($timeDetail)) ? 'active' : '' }}" >
        <input type="radio" name="time_join" value="other_time" {{ (isset($timeDetail)) ? 'checked="checked"' : '' }}>それ以外
      </label>
      <label class="date-input" style="{{ (isset($timeDetail)) ? 'display: flex;' : '' }}">
        <span>希望日時</span>
        <p class="date-input__text">
          <span class='sp-month'>{{ (isset($timeDetail)) ? $timeDetail['month'] .'月' : ''}}</span>
          <span class='sp-date'>{{ (isset($timeDetail)) ? $timeDetail['date'] .'日' : ''}}</span>
          <span class="sp-time">{{ (isset($timeDetail)) ? $timeDetail['hour'].':'.$timeDetail['minute'] : ''}}</span>
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
        <label class="cast-number__value"><input type="text" value="{{ (isset($currentCastNumbers)) ? $currentCastNumbers : 1 }}" name="txtCast_Number" readonly>人</label>

        <button class="cast-number__button-minus" type="button" name="button"></button>
        <button class="cast-number__button-plus"type="button" name="button"></button>
        @if(isset($orderOptions['max_casts']))
        <input type="hidden" value="{{ $orderOptions['max_casts'] }}" id="max_casts">
        @endif
      </div>
    </div>
  </div>
  <div class="reservation-item">
    <div class="caption"><!-- 見出し用div -->
      <h2>キャストを呼ぶ時間</h2>
    </div>
    <div class="form-grpup"><!-- フォーム内容 -->
      <label class="button button--green time {{ (isset($currentDuration) && $currentDuration == '1') ? 'active' : '' }}">
        <input type="radio" name="time_set" value="1" {{ (isset($currentDuration) && $currentDuration == 1) ? 'checked="checked"' : '' }} >
        1時間
      </label>
      <label class="button button--green time {{ (isset($currentDuration) && $currentDuration == '2') ? 'active' : '' }}">
        <input type="radio" name="time_set" value="2" {{ (isset($currentDuration) && $currentDuration == 2) ? 'checked="checked"' : '' }}>
        2時間
      </label>
      <label class="button button--green time {{ (isset($currentDuration) && $currentDuration == '3') ? 'active' : '' }}">
        <input type="radio" name="time_set" value="3" {{ (isset($currentDuration) && $currentDuration == 3) ? 'checked="checked"' : '' }}>
        3時間
      </label>
      <label id="time-input" class="button button--green time {{ (isset($currentOtherDuration)) ? 'active' : '' }}">
        <input type="radio" name="time_set" value="other_duration" {{ (isset($currentOtherDuration)) ? 'checked="checked"' : '' }}>
        4時間以上
      </label>
      <label class="time-input" style="{{ (isset($currentOtherDuration)) ? 'display: flex;' : '' }}">
        <span>呼ぶ時間</span>
        <div class="selectbox">
          <select class="" name="sl_duration">
            <option value="4" {{ (isset($currentDuration) && $currentDuration == 4) ? 'selected' : '' }}>4時間</option>
            <option value="5" {{ (isset($currentDuration) && $currentDuration == 5) ? 'selected' : '' }}>5時間</option>
            <option value="6" {{ (isset($currentDuration) && $currentDuration == 6) ? 'selected' : '' }}>6時間</option>
            <option value="7" {{ (isset($currentDuration) && $currentDuration == 7) ? 'selected' : '' }}>7時間</option>
            <option value="8" {{ (isset($currentDuration) && $currentDuration == 8) ? 'selected' : '' }}>8時間</option>
            <option value="9" {{ (isset($currentDuration) && $currentDuration == 9) ? 'selected' : '' }}>9時間</option>
            <option value="10" {{ (isset($currentDuration) && $currentDuration == 10) ? 'selected' : '' }}>10時間</option>
          </select>
          <i></i>
        </div>
      </label>
    </div>
  </div>
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
              <input type="radio" name="cast_class" class="grade-radio" value="{{ $castClass['id'] }}" {{ (isset($currentCastClass) && $currentCastClass == $castClass['id']) ? 'checked="checked"' : '' }}
               >
            </label>
          @endforeach
        @endif
      </div>
    </div>
  </div>
  <div class="overlay">
    <div class="date-select ct-date-select">
      <div class="date-select__content">
          @php
            $now = \Carbon\Carbon::now()->addMinutes(60);
            $currentMonth = $now->format('m');
            $currentDate = $now->format('d');
            $currentHour = $now->format('H');
            $currentMinute = $now->format('i');
          @endphp
         <select class="select-month" name="sl_month">
          @foreach(range(1, 12) as $month)
           <option value="{{ $month }}"
           {{ (isset($timeDetail) && $timeDetail['month'] ==$month ) ? 'selected' : $currentMonth == $month ? 'selected' : '' }}>
           {{ $month }}月
       </option>
          @endforeach
         </select>
         <select class="select-date" name="sl_date">
            @foreach(getDay() as $key => $val)
             <option value="{{ $key }}"
              {{ (isset($timeDetail) && $timeDetail['date'] ==$key ) ? 'selected' : $currentDate == $key ? 'selected' : '' }}
              >

             {{ $val }}
             </option>
            @endforeach
         </select>
         <select class="select-hour" name="sl_hour">
          @foreach(range(00, 23) as $hour)
           <option value="{{ $hour }}" {{ (isset($timeDetail) && $timeDetail['hour'] ==$hour ) ? 'selected' : $currentHour == $hour ? 'selected' : '' }}>
                {{ $hour<10 ? '0'.$hour : $hour }}時
          </option>
          @endforeach
         </select>
         <select class="select-minute" name="sl_minute">
           @foreach(range(00, 59) as $minute)
           <option value="{{ $minute }}" {{ (isset($timeDetail) && $timeDetail['minute'] ==$minute ) ? 'selected' : $currentMinute == $minute ? 'selected' : '' }}>
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
  <button type="submit" class="form_footer ct-button disable" name="sb_create" disabled>次に進む (1/4)</button>
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

  if(localStorage.getItem("order_call")){
    localStorage.removeItem("order_call");
  }
</script>
@endsection
