@php
  $currentCastTransferStatus = request()->cast_transfer_status;
  $routes = [
    ['name' => 'admin.request_transfer.index', 'value' => '新規申請', 'url' => route('admin.request_transfer.index', ['cast_transfer_status' => App\Enums\CastTransferStatus::PENDING]), 'cast_transfer_status' => App\Enums\CastTransferStatus::PENDING],
    ['name' => 'admin.request_transfer.index', 'value' => '一次審査通過', 'url' => route('admin.request_transfer.index', ['cast_transfer_status' => App\Enums\CastTransferStatus::VERIFIED_STEP_ONE]), 'cast_transfer_status' => App\Enums\CastTransferStatus::VERIFIED_STEP_ONE],
    ['name' => 'admin.request_transfer.index', 'value' => '見送り', 'url' => route('admin.request_transfer.index', ['cast_transfer_status' => App\Enums\CastTransferStatus::DENIED]), 'cast_transfer_status' => App\Enums\CastTransferStatus::DENIED],
  ];
@endphp

<div class="panel-heading">
  <ul class="nav nav-tabs pull-left" id="tabs">
    @foreach ($routes as $route)
      <li class="{{ $currentCastTransferStatus == $route['cast_transfer_status'] ? 'active' : '' }}">
        <a href="{{ $route['url'] }}">{{ $route['value'] }}</a>
      </li>
    @endforeach
  </ul>
</div>
