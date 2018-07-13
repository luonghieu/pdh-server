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
            'submenu' => [],
        ],
    ],
    'ranking' => [
        [
            'name' => '',
            'value' => 'キャストランキング管理',
            'url' => '#',
            'submenu' => [],
        ],
    ],
    'call' => [
        [
            'name' => '',
            'value' => 'コール管理',
            'url' => '#',
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
            'value' => 'チャットルーム管理',
            'url' => '#',
            'submenu' => [],
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
