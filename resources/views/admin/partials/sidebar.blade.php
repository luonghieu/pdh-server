<!-- start: Main Menu -->
@php
$currentRouteName = Route::currentRouteName();
$routes = [
    'account' => [
        [
            'name' => 'admin.users.index',
            'value' => '全アカウント管理',
            'url' => route('admin.users.index'),
            'submenu' => [
                'admin.users.show'
            ],
        ],
    ],
    'cast' => [
        [
            'name' => 'admin.casts.index',
            'value' => 'キャスト管理',
            'url' => route('admin.casts.index'),
            'submenu' => [
              'admin.casts.register','admin.casts.confirm','admin.casts.save'
            ],
        ],
    ],
    'ranking' => [
        [
            'name' => '',
            'value' => 'キャストランキング管理',
            'url' => route('admin.cast_rankings.index'),
            'submenu' => [],
        ],
    ],
    'call' => [
        [
            'name' => 'admin.orders.index',
            'value' => '予約管理',
            'url' => route('admin.orders.index'),
            'submenu' => [],
        ],
    ],
    'order' => [
        [
            'name' => '',
            'value' => '個人予約管理',
            'url' => '#',
            'submenu' => [],
        ],
    ],
    'chatroom' => [
        [
            'name' => '',
            'value' => '運営者専用チャット',
            'url' => route('admin.rooms.index'),
            'submenu' => [
                'admin.rooms.messages_by_room'
            ],
        ],
    ],
    'sale' => [
        [
            'name' => '',
            'value' => '全体売上管理',
            'url' => '#',
            'submenu' => [],
        ],
    ],
];
@endphp

<div class="sidebar col-md-2 col-sm-1">
  <div class="sidebar-collapse collapse">
    @foreach($routes as $title => $route)
    <ul class="nav nav-sidebar">
      @foreach($route as $index => $value)
      <li>
        <a href="{{ $value['url']}}" class="{{ $currentRouteName == $value['name'] || in_array($currentRouteName, $value['submenu']) ? 'active' : '' }}"><span class="hidden-sm text">{{$value['value']}}</span></a>
      </li>
      @endforeach
    </ul>
    @endforeach
  </div>
</div>
<!-- end: Main Menu -->
