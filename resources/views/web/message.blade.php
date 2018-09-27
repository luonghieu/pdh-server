@section('title', 'メッセージ詳細')
@section('screen.id', 'gg2')
@section('controller.id', 'chat')
@extends('layouts.web')
@section('web.content')
  <div class="title">
    <div class="btn-back">
      <a href="{{ route('message.index') }}"><img src="/assets/webview/images/back.png" alt=""></a>
    </div>
    @php
      $listName = [];
      foreach ($messages['room']['users'] as $user) {
        if ($user['id'] != Auth::user()->id) {
           array_push($listName, $user['nickname']);
        }
      }
      $countName = count($messages['room']['users']);
      $listName = implode(",",$listName);
    @endphp
    <div class="title-name">
      <span class="name-member">{{ $listName }}</span>
      @if ($countName > 2)
      <span class="sum-name">({{ $countName }})</span>
      @endif
    </div>
  </div>
  @if ($messages['order'] == null || ($messages['order']['type'] == App\Enums\OrderType::NOMINATION && $messages['order']['status'] == App\Enums\OrderStatus::DONE))
  <div class="msg-head">
    <h2><span class="mitei msg-head-ttl">日程未定</span> {{ (Auth::user()->type == App\Enums\UserType::GUEST) ? 'ゲストに予約リクエストしよう！' : 'ゲストにメッセージを送ってみよう！' }}</h2>
  </div>
  @endif
  @if ($messages['order']['status'] == App\Enums\OrderStatus::DONE && $messages['order']['type'] != App\Enums\OrderType::NOMINATION)
  <div class="msg-head">
    <h2><span class="mitei msg-head-ttl">完了</span> このチャットは終了から24時間使用できます</h2>
  </div>
  @endif
  @if ($messages['order']['status'] == App\Enums\OrderStatus::OPEN && $messages['order']['type'] != App\Enums\OrderType::CALL)
  <div class="msg-head">
    <h2><span class="teian msg-head-ttl">提案中</span>キャストの回答待ちです。</h2>
  </div>
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
              <li class="d-top-time">{{ $messages['order']['duration'] }}時間({{ $messages['order']['cast_class']['cost'] }}P/30分)</li>
            </ul>
          </dt>
          <dt>
            <ul class="detail d-btm">
              <li class="d-btm-money"><p>予定料金：<span>{{ number_format($messages['order']['temp_point']) }}P〜</span></p></li>
              @if ($messages['order']['status'] == App\Enums\OrderStatus::ACTIVE)
              <li class="d-btm-cancel">
                <section class="button-box">
                  <label for="trigger2" class="open_button button-settlement"><span class="btn-cancel">キャンセル</span></label>
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
  @if (($messages['order']['type'] == App\Enums\OrderType::NOMINATION && $messages['order']['status'] == App\Enums\OrderStatus::DONE))
  <div class="msg-head">
    <h2><span class="mitei msg-head-ttl">日程未定</span>キャストに予約リクエストしよう！</h2>
  </div>
  @endif

  <div class="msg">
    <section id="message-box">
      @include('web.content-message',compact('messages'))
    </section>
  </div><!--  msg -->
  <div class="msg-input">
    <form action="" enctype="multipart/form-data" method="POST" class="msg-input-box">
      <input type="hidden" name="room_id" value="{{ $room->id }}" id="room-id">
      <input type="hidden" name="order_id" value="{{ $room->order_id }}" id="order-id">
      <label class="msg-input-pic">
        <img src="/assets/web/images/gg2/picture.svg">
        <input type="file" style="display: none" name="image" accept="image/*" id="image">
      </label>
      <label class="msg-input-camera">
        <img src="/assets/web/images/gg2/camera.svg">
        <input type="file" style="display: none" name="image-camera" accept="image/*" id="image-camera" capture="camera">
      </label>
      <div class="msg-input-text">
        <input type="text" id="content" name="content" placeholder="入力してください">
      </div>
      <label class="msg-input-pic">
        <img src="/assets/web/images/gg2/send.svg">
        <input type="button" id="send-message" style="display: none">
      </label>
    </form>
  </div>
@endsection
@section('web.extra')
  @confirm(['triggerId' => 'trigger2', 'triggerClass' =>'cancel-order', 'buttonLeft' => 'いいえ', 'buttonRight' => 'キャンセルする'])
  @slot('title')
    確定予約をキャンセルしますか？
  @endslot

  @slot('content')
  <p>※この操作は取り消しできません</p>
  <p>※キャンセル料が発生する場合があります</p>
  @endslot
  @endconfirm
@endsection
@section('web.extra_js')
<script>
    $(function(){
      $('.tgl > dl > dt',).click(function(){
        $(this).toggleClass("active");
        $(this).next().slideToggle();
      });
    });
</script>
@endsection
