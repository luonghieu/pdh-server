@section('title', 'メッセージ詳細')
@section('screen.id', 'gg2')
@extends('layouts.web')
@section('web.content')
  @if ($messages['order']['status'] == App\Enums\OrderStatus::OPEN)
  <div class="msg-head">
    <h2><span class="teian msg-head-ttl">提案中</span>キャストの回答待ちです。</h2>
  </div>
  @endif
  @if ($messages['room'] == null || ($messages['order']['type'] == App\Enums\OrderType::NOMINATION && $messages['order']['status'] == App\Enums\OrderStatus::DONE))
  <div class="msg-head">
    <h2><span class="mitei msg-head-ttl">日程未定</span>キャストに予約リクエストしよう！</h2>
  </div>
  @endif
  @if ($messages['order']['status'] == App\Enums\OrderStatus::ACTIVE)
  <div class="msg-head tgl">
    <dl>
      <dt>
        <h2><span class="kakutei msg-head-ttl">予約確定</span>{{ Carbon\Carbon::parse($messages['order']['date'])->format('Y年m月d日') }} {{ Carbon\Carbon::parse($messages['order']['start_time'])->format('H:i') }}〜</h2>
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
              @if ($messages['room']['type'] != App\Enums\RoomType::GROUP && Auth::user()->type != App\Enums\UserType::CAST)
              <li class="d-btm-cancel"><a href="#">キャンセル</a></li>
              @endif
            </ul>
          </dt>
        </dl>
      </dd>
    </dl>
  </div>
  @endif
  @if ($messages['order']['status'] == App\Enums\OrderStatus::PROCESSING)
  <div class="msg-head tgl">
    <dl>
      <dt>
        <h2><span class="goryu msg-head-ttl">合流中</span>{{ Carbon\Carbon::parse($messages['order']['date'])->format('Y年m月d日') }} {{ Carbon\Carbon::parse($messages['order']['start_time'])->format('H:i') }}〜</h2>
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
            </ul>
          </dt>
        </dl>
      </dd>
    </dl>
  </div><!--  msg-head -->
  @endif

  <div class="msg">
    <section id="message-box">
      @include('web.content-message',compact('messages'))
    </section>
  </div><!--  msg -->
  <div class="msg-input">
    <form action="" enctype="multipart/form-data" method="POST" class="msg-input-box">
      <input type="hidden" name="room_id" value="{{ $room->id }}" id="room-id">
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
@section('web.extra_js')
<script>
  $(function(){
    $('.tgl dl dt').click(function(){
      $(this).next().slideToggle();
      $(this).toggleClass("active");
    });
  });
</script>
@endsection
