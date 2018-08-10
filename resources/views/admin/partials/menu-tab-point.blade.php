@php
  $currentRouteName = Route::currentRouteName();
  $routes = [
    ['name' => '', 'value' => '購入履歴', 'url' => '#'],
    ['name' => 'admin.points.point_users', 'value' => 'ユーザー', 'url' => route('admin.points.point_users')],
    ['name' => '#', 'value' => '取引', 'url' => '#'],
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
