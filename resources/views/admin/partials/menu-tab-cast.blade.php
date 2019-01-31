@php
  $currentRouteName = Route::currentRouteName();
  $routes = [
    ['name' => 'admin.users.show', 'value' => '基本情報', 'url' => route('admin.users.show', ['user' => $user->id])],
    ['name' => 'admin.casts.operation_history', 'value' => '稼働・売上履歴', 'url' => route('admin.casts.operation_history', ['user' => $user->id])],
    ['name' => 'admin.casts.guest_ratings', 'value' => '評価', 'url' => route('admin.casts.guest_ratings', ['user' => $user->id])],
    ['name' => '#', 'value' => 'チャットルーム一覧', 'url' => '#'],
    ['name' => 'admin.casts.bank_account', 'value' => '振込口座情報', 'url' => route('admin.casts.bank_account', ['user' => $user->id])],
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
