@php
  $currentRouteName = Route::currentRouteName();
  $routes = [
    ['name' => 'admin.favorites.guest', 'value' => 'ゲスト', 'url' => route('admin.favorites.guest')],
    ['name' => 'admin.favorites.cast', 'value' => 'キャスト', 'url' => route('admin.favorites.cast')],
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
