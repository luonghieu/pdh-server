@php
  $currentRouteName = Route::currentRouteName();
  $routes = [
    ['name' => '#', 'value' => 'キャスト一覧', 'url' => '#'],
    ['name' => 'admin.rank_schedules.index', 'value' => 'クラス設定', 'url' => route('admin.rank_schedules.index')],
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
