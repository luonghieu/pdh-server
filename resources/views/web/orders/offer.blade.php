@section('title', 'キャスト予約')
@section('screen.id', 'ge2-1-x')
@section('web.extra_css')
<link rel="stylesheet" href="{{ mix('assets/web/css/cast.min.css') }}">
<link rel="stylesheet" href="{{ mix('assets/web/css/ge_4.min.css') }}">
{{-- <link rel="stylesheet" href="{{ mix('assets/web/css/ge_1.min.css') }}"> --}}
@endsection
@extends('layouts.web')
@section('web.content')
    <a href="javascript:void(0)" id="confirm-order-offer-submit" class="gtm-hidden-btn" onclick="dataLayer.push({
        'userId': '<?php echo Auth::user()->id; ?>',
        'event': 'nominationbooking_complete'
    });"></a>
      <form>
        <div class="cast-list">
          <div class="cast-head">
          <div class="cast-body">
              <div class="cast-item cast-item-offer">
                <a href="">
                  <span class="tag cast-class_p">fadsfasdf</span>
                  <span class="cast-ok">今日OK</span>
                  <img src="http://cheers.test/storage/4ca093be755b42fb0cf9d639b62e5d96.jpg">
                  <div class="info">
                    <span class="tick tick-online"></span>
                    <span class="title-info">asdf  21歳</span>
                    <div class="wrap-description">
                      <span class="description">xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</span>
                    </div>
                  </div>
                </a>
              </div>
              <div class="cast-item cast-item-offer">
                <a href="">
                  <span class="tag cast-class_d">fadsfasdf</span>
                  <span class="cast-ok">今日OK</span>
                  <img src="http://cheers.test/storage/4ca093be755b42fb0cf9d639b62e5d96.jpg">
                  <div class="info">
                    <span class="tick tick-online"></span>
                    <span class="title-info">asdf  21歳</span>
                    <div class="wrap-description">
                      <span class="description">xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</span>
                    </div>
                  </div>
                </a>
              </div>
              <div class="cast-item cast-item-offer">
                <a href="">
                  <span class="tag cast-class_b">fadsfasdf</span>
                  <span class="cast-ok">今日OK</span>
                  <img src="http://cheers.test/storage/4ca093be755b42fb0cf9d639b62e5d96.jpg">
                  <div class="info">
                    <span class="tick tick-online"></span>
                    <span class="title-info">asdf  21歳</span>
                    <div class="wrap-description">
                      <span class="description">xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</span>
                    </div>
                  </div>
                </a>
              </div>
          </div>
        </div>
      </div>
        <div class="reservation-item" style="margin-top: 10px;">
          <div class="caption">
            <h2>キャストからのお誘いメッセージ</h2>
          </div>
          <div class="form-group">
            <div class="cheer-input" rows="4">カラオケが好きです！！！普段は赤坂、六本木付近にいるので、いつでもお誘い待っています★
            カラオケが好きです！！！普段は赤坂、六本木付近にいるので、いつでもお誘い待っています★</div>
          </div>
        </div>

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
            <label class="button button--green area">
              <input class="input-area-offer" type="radio" name="offer_area" value="六本木">六本木</label>
            <label class="button button--green area">
              <input class="input-area-offer" type="radio" name="offer_area" value="恵比寿">恵比寿</label>
            <label class="button button--green area">
              <input class="input-area-offer" type="radio" name="offer_area" value="西麻布">西麻布</label>
            <label class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="渋谷">渋谷</label>
            <label class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="赤坂">赤坂</label>
            <label class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="銀座">銀座</label>
            <label class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="中目黒">中目黒</label>
            <label class="button button--green area" >
              <input class="input-area-offer" type="radio" name="offer_area" value="新橋">新橋</label>
            <label class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="池袋">池袋</label>
            <label class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="新宿">新宿</label>
            <label id="area_input" class="button button--green area ">
              <input class="input-area-offer" type="radio" name="offer_area" value="その他">その他</label>
            <label class="area-input area-offer"><span>希望エリア</span>
              <input type="text" id="other_area_offer" placeholder="入力してください" name="other_area_offer" value="">
           </label>
          </div>
        </div>

        <div class="reservation-item">
          <div class="caption"><!-- 見出し用div -->
            <h2>ギャラ飲みの開始時間</h2>
          </div>
          <div class="form-grpup"><!-- フォーム内容 -->
            <label class="date-input d-flex-end">
              <p class="date-input__text">
                <span>2018年</span>
                <span>7月</span>
                <span>2日</span>
                <span>21:30~</span>
              </p>
            </label>
          </div>
        </div>

        <div class="reservation-item">
          <div class="caption"><!-- 見出し用div -->
            <h2>キャストとを呼ぶ時間</h2>
          </div>
          <div class="form-grpup"><!-- フォーム内容 -->
            <label class="button button--green time">
              <input class="input-duration" type="radio" name="time_set_offer" value="1">
              1時間
            </label>
            <label class="button button--green time ">
              <input class="input-duration" type="radio" name="time_set_offer" value="2">
              2時間
            </label>
            <label class="button button--green time">
              <input class="input-duration" type="radio" name="time_set_offer" value="3">
              3時間
            </label>
            <label id="time-input" class="button button--green time">
              <input class="input-duration" type="radio" name="time_set_offer" value="other_time_set">
              4時間以上
            </label>
            <label class="time-input time-input-offer">
              <span>呼ぶ時間</span>
              <div class="selectbox">
                <select class="select-duration" name="sl_duration_nominition">
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

        <div class="reservation-item">
          <div class="caption">
            <h2>クレジットカードの登録</h2>
          </div>

          <a class="link-arrow link-arrow--left">未登録</a>

          <div class="form-group">
            <div class="total-box">
              <span class="total-text">合計</span>
              <span class="total-amount">60,000P~</span>
            </div>

            <div class="reservation-policy">
          <label class="checkbox">
            <input type="checkbox" class="checked-order-offer" name="confrim_order_offer">
            <span class="sp-disable" id="sp-cancel"></span>
            <a href="{{ route('guest.orders.cancel') }}">キャンセルポリシー</a>
            に同意する
          </label>
        </div>
          </div>
        </div>
      </form>
      <button type="button" class="form_footer bt-offer disable" name='orders_offer' id="confirm-orders-offer" disabled="disabled">ギャラ飲みをセッティングする</button>
    <div class="overlay">
      <div class="date-select">
        <div class="date-select__content">
           <select class="" name="">
             <option value="">20時</option>
             <option value="">21時</option>
             <option value="">22時</option>
             <option value="">23時</option>
           </select>
           <select class="" name="">
             <option value="">00分</option>
             <option value="">10分</option>
             <option value="">20分</option>
             <option value="">30分</option>
           </select>
        </div>
        <div class="date-select__footer">
          <button class="date-select__cancel" type="button">キャンセル</button>
          <button class="date-select__ok" type="button">完了</button>
        </div>

      </div>
    </div>
@endsection
