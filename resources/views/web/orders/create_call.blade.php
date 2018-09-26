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
      <label class="button button--green date {{ (isset($currentTime) && $currentTime == '20') ? 'active' : '' }}">
        <input type="radio" name="time_join" value="20" {{ (isset($currentTime) && $currentTime == 20) ? 'checked="checked"' : '' }}>
        20分後
      </label>
      <label class="button button--green date {{ (isset($currentTime) && $currentTime == '30') ? 'active' : '' }} ">
        <input type="radio" name="time_join" value="30" {{ (isset($currentTime) && $currentTime == 30) ? 'checked="checked"' : '' }}>
        30分後
      </label>
      <label class="button button--green date {{ (isset($currentTime) && $currentTime == '60') ? 'active' : '' }} ">
        <input type="radio" name="time_join" value="60" {{ (isset($currentTime) && $currentTime == 60) ? 'checked="checked"' : '' }}>
        60分後
      </label>
      <label class="button button--green date {{ (isset($currentTime) && $currentTime == '90') ? 'active' : '' }} ">
        <input type="radio" name="time_join" value="90" {{ (isset($currentTime) && $currentTime == 90) ? 'checked="checked"' : '' }} >
        90分後
      </label>
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
        <label class="cast-number__value"><input type="text" value="{{ (isset($currentCastNumbers)) ? $currentCastNumbers : 1 }}" name="txtCast_Number">人</label>

        <button class="cast-number__button-minus" type="button" name="button"></button>
        <button class="cast-number__button-plus"type="button" name="button"></button>
      </div>
    </div>
  </div>
  <div class="reservation-item">
    <div class="caption"><!-- 見出し用div -->
      <h2>キャストとを呼ぶ時間</h2>
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
      <label id="time-input" class="button button--green time {{ (isset($currentDuration) && $currentDuration == '4') ? 'active' : '' }}">
        <input type="radio" name="time_set" value="4" {{ (isset($currentDuration) && $currentDuration == 4) ? 'checked="checked"' : '' }}>
        4時間以上
      </label>
      <label class="time-input">
        <span>呼ぶ時間</span>
        <div class="selectbox">
          <select class="" name="sl_duration">
            <option value="4">4時間</option>
            <option value="5">5時間</option>
            <option value="6">6時間</option>
            <option value="7">7時間</option>
            <option value="8">8時間</option>
            <option value="9">9時間</option>
            <option value="10">10時間</option>
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
        <label>
          <img src="{{ asset('assets/web/images/ge2-1-a/grade-icon_001.png') }}" alt="">
          <span class="cast_class">ダイアモンド</span>
          <span class="cast_price">12,500P/30分</span>
          <input type="radio" name="cast_class" class="grade-radio" value="3" {{ (isset($currentCastClass) && $currentCastClass == 3) ? 'checked="checked"' : '' }}>
        </label>
        <label>
          <img src="{{ asset('assets/web/images/ge2-1-a/grade-icon_002.png') }}" alt="">
          <span class="cast_class">プラチナ</span>
          <span class="cast_price">5,000P/30分</span>
          <input type="radio" name="cast_class" class="grade-radio" value="2" {{ (isset($currentCastClass) && $currentCastClass == 2) ? 'checked="checked"' : '' }}>
        </label>
        <label>
          <img src="{{ asset('assets/web/images/ge2-1-a/grade-icon_003.png') }}" alt="">
          <span class="cast_class">ブロンズ</span>
          <span class="cast_price">2,500P/30分</span>
          <input type="radio" name="cast_class" class="grade-radio" value="1" {{ (isset($currentCastClass) && $currentCastClass == 1) ? 'checked="checked"' : '' }}>
        </label>
      </div>
    </div>
  </div>
  <div class="overlay">
    <div class="date-select">
      <div class="date-select__content">
         <select class="select-month" name="sl_month">
          @foreach(range(1, 12) as $month)
           <option value="{{ $month }}"
           {{ (isset($timeDetail) && $timeDetail['month'] ==$month ) ? 'selected' : \Carbon\Carbon::now()->format('m') == $month ? 'selected' : '' }}>
           {{ $month }}月
       </option>
          @endforeach
         </select>
         <select class="select-date" name="sl_date">
            @foreach(getDay() as $key => $val)
             <option value="{{ $key }}"
              {{ (isset($timeDetail) && $timeDetail['date'] ==$key ) ? 'selected' : \Carbon\Carbon::now()->format('d') == $key ? 'selected' : '' }}
              >

             {{ $val }}
             </option>
            @endforeach
         </select>
         <select class="select-hour" name="sl_hour">
          @foreach(range(00, 23) as $hour)
           <option value="{{ $hour }}" {{ (isset($timeDetail) && $timeDetail['hour'] ==$hour ) ? 'selected' : \Carbon\Carbon::now()->format('H') == $hour ? 'selected' : '' }}>
                {{ $hour<10 ? '0'.$hour : $hour }}時
          </option>
          @endforeach
         </select>
         <select class="select-minute" name="sl_minute">
           @foreach(range(00, 59) as $minute)
           <option value="{{ $minute }}" {{ (isset($timeDetail) && $timeDetail['minute'] ==$minute ) ? 'selected' : \Carbon\Carbon::now()->format('i') == $minute ? 'selected' : '' }}>
                {{ $minute<10 ? '0'.$minute : $minute }}分
          </option>
          @endforeach
         </select>
      </div>
      <div class="date-select__footer">
        <button class="date-select__cancel" type="button">キャンセル</button>
        <button class="date-select__ok date-select-nomination" type="button">完了</button>
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
