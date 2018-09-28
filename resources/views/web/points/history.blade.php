@section('title', 'ポイント履歴')
@section('screen.id', 'gl2-1')

@extends('layouts.web')
@section('web.extra')
<form action="#" method="post" id="form-receipt">
  {{ csrf_field() }}
  <div class="modal_wrap modal5">
    <input id="trigger5" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger5" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn5">
        <div class="text-box">
          <h2>領収書を発行する</h2>
          <div>
            <p>宛名(任意)</p>
            <label data-field="name" id="name-error" class="error help-block" for="name"></label>
            <input type="text" id="name" name="name" placeholder="例：株式会社チアーズ">
          </div>
          <div>
            <p>但し書き(任意)</p>
            <label data-field="content" id="content-error" class="error help-block" for="content"></label>
            <input type="text" id="content" name="content" placeholder="例：飲食代">
            <input type="hidden" name="point_id" value="" />
          </div>
        </div>
        <div class="close_button-box">
          <div class="close_button-block left">
            <label for="trigger5" class="btn4">キャンセル</label>
          </div>
          <div class="close_button-block">
            <button type="submit" for="trigger5" class="btn btn-bg bd-none">発行する</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<div class="modal_wrap">
  <a href="" id='mailto'></a>
  <input id="trigger2" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger2" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
        <img id="img-pdf" name="pdf" alt="">
        <div class="mb-10">
          <div class="close_button-block">
            <button type="submit" id="send-mail" img-file="" class="btn btn-bg bd-none">メールで送信</button>
          </div>
          <div class="close_button-block">
            <a class="btn btn-bg bd-none" id="img-download" download>画像を保存</a>
          </div>
        </div>
      </div>
  </div>
</div>
@endsection
@section('web.content')
@if (!$points['data'])
<div class="list_wrap">
  <div class="point-empty">
    <img src="{{ asset('assets/web/images/gl2-1/ic_point_gray.png') }}" alt="">
    <span>ポイント履歴はまだありません</span>
  </div>
</div>
@else
  <label for="trigger2" class="open_button button-settlement"></label>
  <div class="list_wrap">
    @foreach ($points['data'] as $point)
      @php
          switch ($point['type']) {
              case \App\Enums\PointType::EVICT:
                  if ($point['point'] >= 0) {
                    $pointView = abs($point['point']);
                  } else {
                    $pointView = $point['point'];
                  }
                  break;

              default:
                $pointView = $point['point'];
                break;
          }

          switch ($point['type']) {
              case \App\Enums\PointType::BUY:
                  $type = '購入';
                  break;
              case \App\Enums\PointType::PAY:
                  $type = '決済';
                  break;
              case \App\Enums\PointType::AUTO_CHARGE:
                  $type = 'オートチャージ';
                  break;
              case \App\Enums\PointType::EVICT:
                  $type = 'ポイント失効';
                  break;

              default:
                  $type = '';
                  break;
          }
      @endphp
      <div class="list_item {{ !($pointView <= 0) ?: "list_kessai" }}">
        <div class="item_left">
          <span class="item_date">
            {{ \Carbon\Carbon::parse($point['created_at'])->format('m月d日') }} ({{ dayOfWeek()[Carbon\Carbon::parse($point['created_at'])->dayOfWeek] }})
            {{ \Carbon\Carbon::parse($point['created_at'])->format('H:i') }}〜
          </span>
          <span class="item_status">{{ $type }}</span>
          <span class="item_point {{ ($pointView >= 0) ? "point-plus" : "init-point-minus" }}">
            {{ number_format($pointView) }}P
          </span>
        </div>
        @if (($pointView <= 0) && ($point['type'] != \App\Enums\PointType::EVICT))
          <div class="item_right">
            <div class=""><a href="{{ route('history.show', $point['order_id']) }}"><img src="{{ asset('assets/web/images/gl2-1/arrow.svg') }}" alt=">"></a></div>
          </div>
        @else
          <div class="item_right" id="point-{{ $point['id'] }}-btn">
            @if (!$point['receipt'])
              <label for="trigger5" class="btn-bg js-point" point-id="{{ $point['id'] }}">領収書を発行</label>
            @else
              <label for="trigger2" class="btn-bg js-receipt" img-file="{{ $point['receipt']['img_file'] }}">領収書を再発行</label>
            @endif
          </div>
        @endif
      </div>
    @endforeach
  </div>  <!-- /list_wrap -->
@endif
@endsection
