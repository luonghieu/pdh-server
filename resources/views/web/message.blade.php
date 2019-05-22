@section('title', 'メッセージ詳細')
@section('screen.id', 'gg2')
@section('controller.id', 'chat')
@extends('layouts.web')
@section('web.extra_css')
  <link rel="stylesheet" href="/assets/web/css/croppie.css" />
@endsection
@section('web.extra')
  <div class="modal_wrap">
    <input id="croppie-image-modal" type="checkbox">
    <div class="modal_overlay wrap-croppie-image">
      <img id="my-image" src="#" />
      <div class="wrap-button-croppie">
        <label for="croppie-image-modal" id="crop-image-btn-cancel">Cancel</label>
        <label for="croppie-image-modal" id="crop-image-btn-accept">Choose</label>
      </div>
    </div>
  </div>

  <div class="modal_wrap">
    <input id="modal-confirm-cancel-order" type="checkbox">
    <div class="modal_overlay">
      <label for="modal-confirm-cancel-order" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
        <div class="text-box">
          <h2>確定予約をキャンセルしますか？</h2>
          <p>
            <p>※この操作は取り消しできません</p>
            <p>※キャンセル料が発生する場合があります</p>
          </p>
        </div>
        <div class="close_button-box">
          <div class="close_button-block">
            <label for="modal-confirm-cancel-order" class="close_button  left">いいえ</label>
          </div>
          <div class="close_button-block">
            <label for="modal-confirm-cancel-order" class="close_button cancel-order right">キャンセルする</label>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal_wrap">
    <input id="modal-confirm-skip-order-nominee" type="checkbox">
    <div class="modal_overlay">
      <label for="modal-confirm-skip-order-nominee" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
        <div class="text-box">
          <h2>指名予約の提案を取り下げますか？</h2>
          <p>※キャンセル料は発生しません</p>
        </div>
        <div class="close_button-box">
          <div class="close_button-block">
            <label for="modal-confirm-skip-order-nominee" class="close_button  left">いいえ</label>
          </div>
          <div class="close_button-block">
            <label for="modal-confirm-skip-order-nominee" class="close_button skip-order-nominee right">取り下げる</label>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal_wrap wrap-alert-image-oversize">
    <input id="alert-image-oversize" type="checkbox">
    <div class="modal_overlay">
      <label for="alert-image-oversize" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn3 alert-image-oversize">
        <div class="content-in">
          <h2></h2>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('web.content')
  @if (isset(request()->matching_completed) && request()->matching_completed)
    <a href="javascript:void(0)" id="matching-completed" class="gtm-hidden-btn" onclick="dataLayer.push({
      'userId': '<?php echo Auth::user()->id; ?>',
      'event': 'matching_complete'
    });"></a>
    <script>
        const matchingCompleted = localStorage.getItem('matching_completed');
        const orders = (matchingCompleted) ? matchingCompleted.split(',') : [];
        const currentOrder = '<?php echo request()->order_id ?>';
        if (!orders.length) {
            orders.push(currentOrder);
            document.getElementById('matching-completed').click();
            localStorage.setItem('matching_completed', orders);
        } else {
            if (!orders.includes(currentOrder)) {
                orders.push(currentOrder);
                document.getElementById('matching-completed').click();
                localStorage.setItem('matching_completed', orders);
            }
        }
    </script>
  @endif
  <div class="title">
    @php
      if ($room->type != \App\Enums\RoomType::SYSTEM) {
        $listName = [];
        foreach ($messages['room']['users'] as $user) {
          if ($user['id'] != Auth::user()->id) {
             array_push($listName, $user['nickname']);
          }
        }
        $listName = implode(",",$listName);
      } else {
        $listName = 'Cheers運営局';
      }

      $countName = count($messages['room']['users']);
    @endphp
    <div class="title-name">
      @if ($countName > 2)
      <span class="name-member">{{ $listName }}</span>
      <span class="sum-name">({{ $countName }})</span>
      @else
        @php
          foreach ($messages['room']['users'] as $user) {
            if ($user['id'] != Auth::user()->id) {
              if($user['type'] == App\Enums\UserType::ADMIN) {
                $age = '';
              } else {
                $age = '('.$user['age'].')';
              }
            }
          }
        @endphp
        <span class="name-member">{{ $listName }}</span>
        <span class="sum-name">{{ $age }}</span>
      @endif
    </div>
  </div>
  @if ($room->type != \App\Enums\RoomType::SYSTEM)
    @if ($messages['order'] == null || (count($messages['room']['users']) == 2 && $messages['order']['status'] == App\Enums\OrderStatus::DONE))
    <div class="msg-head">
      <h2><span class="mitei msg-head-ttl">日程未定</span> {{ (Auth::user()->type == App\Enums\UserType::GUEST) ? 'ゲストに予約リクエストしよう！' : 'ゲストにメッセージを送ってみよう！' }}</h2>
    </div>
    @endif
    @if ($messages['order']['status'] == App\Enums\OrderStatus::DONE && count($messages['room']['users']) > 2)
    <div class="msg-head">
      <h2><span class="mitei msg-head-ttl">完了</span> このチャットは終了から24時間使用できます</h2>
    </div>
    @endif
    @if ($messages['order']['status'] == App\Enums\OrderStatus::OPEN)
      @if($messages['order']['type'] == App\Enums\OrderType::NOMINATION)
        <div class="msg-head tgl">
          <dl>
            <dt class="msg-detail-order-nominee">
              <h2>
                <span class="teian msg-head-ttl">提案中</span>
                <span class="status-bar-nominee">キャストの回答待ちです。</span>
                <span class="time-order-nonimee">{{ Carbon\Carbon::parse($messages['order']['date'])->format('Y年m月d日') }} {{ Carbon\Carbon::parse($messages['order']['start_time'])->format('H:i') }}〜</span>
              </h2>
              <i><img src="/assets/web/images/gg2/arrow.svg"></i>
            </dt>
            <dd class="msg-head-detail">
              <dl>
                <dt>
                  <ul class="detail d-top">
                    <li class="d-top-place">{{ $messages['order']['address'] }}</li>
                    @php
                      $cost = $messages['order']['nominees'] ? $messages['order']['nominees'][0]['cost'] : 0;
                    @endphp
                    <li class="d-top-time">{{ $messages['order']['duration'] }}時間({{ number_format($cost) }}P/30分)</li>
                  </ul>
                </dt>
                <dt>
                  <ul class="detail d-btm">
                    <li class="d-btm-money"><p>予定料金：<span>{{ number_format($messages['order']['temp_point']) }}P〜</span></p></li>
                    <li class="d-btm-cancel">
                      <section class="button-box">
                        <label for="modal-confirm-skip-order-nominee" class="open_button"><span class="btn-cancel">キャンセル</span></label>
                      </section>
                    </li>
                  </ul>
                </dt>
              </dl>
            </dd>
          </dl>
        </div>
      @else
        <div class="msg-head">
          <h2><span class="teian msg-head-ttl">提案中</span>キャストの回答待ちです。</h2>
        </div>
      @endif
    @endif
    @if (in_array($messages['order']['status'], [App\Enums\OrderStatus::ACTIVE, App\Enums\OrderStatus::PROCESSING]))
    <div class="msg-head tgl">
      <dl>
        <dt>
          <h2>
            @if ($messages['order']['status'] == App\Enums\OrderStatus::ACTIVE)
            <span class="kakutei msg-head-ttl">予約確定</span>
            @else
            <span class="goryu msg-head-ttl">合流中</span>
            @endif
            {{ Carbon\Carbon::parse($messages['order']['date'])->format('Y年m月d日') }} {{ Carbon\Carbon::parse($messages['order']['start_time'])->format('H:i') }}〜</h2>
          <i><img src="/assets/web/images/gg2/arrow.svg"></i>
        </dt>
        <dd class="msg-head-detail">
          <dl>
            <dt>
              <ul class="detail d-top">
                <li class="d-top-place">{{ $messages['order']['address'] }}</li>
                @if ($messages['order']['type'] != App\Enums\OrderType::NOMINATION)
                <li class="d-top-time">{{ $messages['order']['duration'] }}時間({{ number_format($messages['order']['cast_class']['cost']) }}P/30分)</li>
                @else
                  @php
                    if (isset($messages['order']['casts'][0])) {
                      $cost = $messages['order']['casts'][0]['cast_order']['cost'];
                    }
                  @endphp
                  <li class="d-top-time">{{ $messages['order']['duration'] }}時間({{ number_format($cost) }}P/30分)</li>
                @endif
                @if ($countName > 2)
                  <li class="d-top-users">{{ $countName - 1 }}名</li>
                @endif
              </ul>
            </dt>
            <dt>
              <ul class="detail d-btm">
                @php
                  $tempPoint = 0;
                  if($messages['order']['type'] != App\Enums\OrderType::NOMINATION) {
                    foreach ($messages['order']['casts'] as $cast) {
                      $tempPoint += $cast['cast_order']['temp_point'];
                    }

                    $tempPoint -= $messages['order']['discount_point'];

                    if ($tempPoint < 0) {
                      $tempPoint = 0;
                    }
                  } else {
                    $tempPoint = $messages['order']['temp_point'];
                  }
                @endphp
                <li class="d-btm-money"><p>予定料金：<span>{{ number_format($tempPoint) }}P〜</span></p></li>
                @if ($messages['order']['status'] == App\Enums\OrderStatus::ACTIVE)
                <li class="d-btm-cancel">
                  <section class="button-box">
                    <label for="modal-confirm-cancel-order" class="open_button button-settlement"><span class="btn-cancel">キャンセル</span></label>
                  </section>
                </li>
                @endif
              </ul>
            </dt>
          </dl>
        </dd>
      </dl>
    </div>
    @endif
    @if (($room->type == App\Enums\RoomType::GROUP && $messages['order']['status'] == App\Enums\OrderStatus::CANCELED) || ($messages['order']['type'] == App\Enums\OrderType::NOMINATION && $messages['order']['status'] == App\Enums\OrderStatus::DONE))
    <div class="msg-head">
      <h2><span class="mitei msg-head-ttl">日程未定</span>キャストに予約リクエストしよう！</h2>
    </div>
    @endif
  @endif

  <div class="msg">
    <section id="message-box">
      @include('web.content-message',compact('messages'))
    </section>
  </div><!--  msg -->
  <div class="msg-input messge-input">
    <form action="" enctype="multipart/form-data" method="POST" class="msg-input-box">
      <input type="hidden" name="room_id" value="{{ $room->id }}" id="room-id">
      <input type="hidden" name="order_id" value="{{ $messages['order']['id'] }}" id="order-id">
      <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" id="user-id">
      <label class="msg-input-pic">
        <img src="/assets/web/images/gg2/picture.svg">
        <input type="file" style="display: none" name="image" accept="image/*" id="image">
      </label>
      <label class="msg-input-camera">
        <img src="/assets/web/images/gg2/camera.svg">
        <input type="file" style="display: none" name="image-camera" accept="image/*" id="image-camera" capture="camera">
      </label>
      <div class="msg-input-text">
        <textarea type="text" id="content" name="content" placeholder="入力してください" class="content-message"></textarea>
      </div>
      <button class="msg-input-pic" id="send-message">
        <img src="/assets/web/images/gg2/send.svg">
        <input type="button" style="display: none" >
      </button>
    </form>
  </div>
@endsection
@section('web.extra_js')
<script>
  $(function() {
    var userAgent = navigator.userAgent || navigator.vendor || window.opera;

    $('.tgl > dl > dt',).click(function(){
      $(this).toggleClass("active");
      $(this).next().slideToggle();
    });

    if ($('.pic p img').length > 0) {
       $('.pic p img').load(function(){
         $('#message-box').scrollTop($('#message-box')[0].scrollHeight);
       });
    }

    //android detection
    if (/android/i.test(userAgent)) {
      $(document).scrollTop($('#message-box')[0].scrollHeight);
    }

    // iOS detection
    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
      $('#message-box').scrollTop($('#message-box')[0].scrollHeight);
    }
  });
</script>
<script>
  var ta = document.getElementById("content");
  ta.style.lineHeight = 1.2;//init
  ta.style.height = "30px";//init

  ta.addEventListener("input",function(evt){
      if(evt.target.scrollHeight > evt.target.offsetHeight){
        evt.target.style.height = evt.target.scrollHeight + "px";
      }else{
          var height,lineHeight;
          while (true){
              height = Number(evt.target.style.height.split("px")[0]);
              lineHeight = Number(evt.target.style.lineHeight.split("px")[0]);
              evt.target.style.height = height - lineHeight + "px";
              if(evt.target.scrollHeight > evt.target.offsetHeight){
                  evt.target.style.height = evt.target.scrollHeight + "px";
                  break;
              }
          }
      }
  });
</script>
@endsection
@section('web.script')
  <script src="/assets/web/js/croppie.js"></script>
<script>
  if(localStorage.getItem("order_params")){
    localStorage.removeItem("order_params");
  }

  if(localStorage.getItem("order_offer")){
    localStorage.removeItem("order_offer");
  }
</script>
@stop
