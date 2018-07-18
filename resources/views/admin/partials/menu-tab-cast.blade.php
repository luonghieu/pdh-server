@php
  $currentRouteName = Route::currentRouteName();
  $routes = [
    ['name' => 'admin.users.show', 'value' => '基本情報', 'url' => route('admin.users.show', ['user' => $user->id])],
    ['name' => '#', 'value' => '稼働・売上履歴', 'url' => '#'],
    ['name' => '#', 'value' => '評価', 'url' => '#'],
    ['name' => '#', 'value' => 'チャットルーム一覧', 'url' => '#'],
    ['name' => '#', 'value' => '振込口座情報', 'url' => '#'],
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
