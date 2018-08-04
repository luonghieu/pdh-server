@php
  $currentRouteName = Route::currentRouteName();
  $routes = [
    ['name' => 'admin.users.show', 'value' => '基本情報', 'url' => route('admin.users.show', ['user' => $user->id])],
    ['name' => 'admin.users.orders_history', 'value' => '予約履歴', 'url' => route('admin.users.orders_history', ['user' => $user->id])],
    ['name' => 'admin.users.points_history', 'value' => 'ポイント購入履歴', 'url' => route('admin.users.points_history', ['user' => $user->id])],
    ['name' => '#', 'value' => '領収書発行履歴', 'url' => '#'],
    ['name' => 'admin.users.cast_ratings', 'value' => '評価', 'url' => route('admin.users.cast_ratings', ['user' => $user->id])],
    ['name' => '#', 'value' => 'チャットルーム一覧', 'url' => '#'],
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
