@section('title', 'ポイント履歴')
@section('screen.id', 'gl2-1')

@extends('layouts.web')
@section('web.extra')
<div class="modal_wrap modal5">
  <input id="trigger5" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger5" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn5">
        <div class="text-box">
          <h2>領収書を発行する</h2>
          <p>宛名(任意)</p>
          <input type="text" placeholder="例：株式会社チアーズ">
          <p>但し書き(任意)</p>
          <input type="text" placeholder="例：飲食代">
        </div>
        <div class="close_button-box">
          <div class="close_button-block left">
            <label for="trigger5" class="btn4">キャンセル</label>
          </div>
          <div class="close_button-block">
            <label for="trigger5" class="btn-bg">発行する</label>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
@section('web.content')
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
          {{ \Carbon\Carbon::parse($point['created_at'])->format('m月d日') }} (木)
          {{ \Carbon\Carbon::parse($point['created_at'])->format('h:i') }}
        </span>
        <span class="item_status">{{ $type }}</span>
        <span class="item_point {{ ($pointView >= 0) ? "point-plus" : "init-point-minus" }}">
          {{ number_format($pointView) }}P
        </span>
      </div>
      @if (($pointView <= 0) && ($point['type'] != \App\Enums\PointType::EVICT))
        <div class="item_right">
          <div class=""><img src="{{ asset('assets/web/images/gl2-1/arrow.svg') }}" alt=">"></div>
        </div>
      @else
        <div class="item_right">
          <label for="trigger5" class="btn-bg">領収書を発行</label>
        </div>
      @endif
    </div>
  @endforeach
</div>  <!-- /list_wrap -->
@endsection
