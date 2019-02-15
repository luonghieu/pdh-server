@php
  $currentRouteName = Route::currentRouteName();
  $routes = [
    ['name' => 'admin.rank_schedules.casts', 'value' => 'キャスト一覧', 'url' => route('admin.rank_schedules.casts')],
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
