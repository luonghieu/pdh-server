@php
  $currentRouteName = Route::currentRouteName();
  $routes = [
    ['name' => 'admin.transfers.non_transfers', 'value' => '未振込', 'url' => route('admin.transfers.non_transfers')],
    ['name' => '#', 'value' => '振込済み', 'url' => '#'],
  ];
@endphp

<div class="panel-heading">
  <ul class="nav nav-tabs pull-left" id="tabs">
    @foreach ($routes as $route)
      <li class="{{ $currentRouteName == $route['name'] ? 'active' : '' }}">
        <a href="{{ $route['url'] }}">{{ $route['value'] }}</a>
      </li>
    @endforeach
  </ul>
</div>
