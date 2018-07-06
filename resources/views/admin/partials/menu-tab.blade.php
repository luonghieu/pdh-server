@php
  $currentRouteName = Route::currentRouteName();
  $routes = [
    ['name' => 'admin.accounts.show', 'value' => '基本情報', 'url' => route('admin.accounts.show')],
    ['name' => '#', 'value' => '予約履歴', 'url' => '#'],
    ['name' => '#', 'value' => 'ポイント購入履歴', 'url' => '#'],
    ['name' => '#', 'value' => '領収書発行履歴', 'url' => '#'],
    ['name' => '#', 'value' => '評価', 'url' => '#'],
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
