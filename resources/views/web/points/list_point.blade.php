@foreach ($points['data'] as $point)
  @php
      switch ($point['type']) {
          case \App\Enums\PointType::EVICT:
              if ($point['point'] >= 0) {
                $pointView = - ($point['point']);
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
        {{ \Carbon\Carbon::parse($point['created_at'])->format('H:i') }}
      </span>
      <span class="item_status">{{ $type }}</span>
      <span class="item_point {{ ($pointView >= 0) ? "point-plus" : "init-point-minus" }}">
        {{ number_format($pointView) }}P
      </span>
    </div>
    @if ($point['type'] == \App\Enums\PointType::EVICT)
      <div class="item_right">
      </div>
    @elseif ($pointView <= 0)
      <div class="item_right">
        <div class=""><a href="{{ route('history.show', $point['order_id']) }}"><img src="{{ asset('assets/web/images/gl2-1/arrow.svg') }}" alt=">"></a></div>
      </div>
    @else
      <div class="item_right" id="point-{{ $point['id'] }}-btn">
        @if (!$point['receipt'])
          <label for="trigger5" class="btn-bg js-point" point-id="{{ $point['id'] }}">領収書を発行</label>
        @else
          <label for="trigger2" class="btn-bg js-receipt" point-id="{{ $point['id'] }}" img-file="{{ $point['receipt']['img_file'] }}">領収書を再発行</label>
        @endif
      </div>
    @endif
  </div>
@endforeach
