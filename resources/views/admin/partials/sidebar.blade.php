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
                'admin.users.show', 'admin.users.orders_history','admin.users.cast_ratings', 'admin.users.points_history'
            ],
        ],
    ],
    'cast' => [
        [
            'name' => 'admin.casts.index',
            'value' => 'キャスト管理',
            'url' => route('admin.casts.index'),
            'submenu' => [
              'admin.casts.register','admin.casts.confirm','admin.casts.save','admin.casts.guest_ratings'
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
            'submenu' => [
              'admin.orders.nominees', 'admin.orders.candidates','admin.orders.call'
              'admin.orders.nominees', 'admin.orders.candidates','admin.orders.call', 'admin.orders.casts_matching', 'admin.orders.order_nominee'
            ],
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
                'admin.rooms.messages_by_room',
                'admin.rooms.members'
            ],
        ],
    ],

    'chat' => [
        [
            'name' => '',
            'value' => '運営者専用チャット',
            'url' => route('admin.chat.index'),
            'submenu' => [],
        ],
    ],

    'sale' => [
        [
            'name' => 'admin.sales.index',
            'value' => '売上管理',
            'url' =>  route('admin.sales.index'),
            'submenu' => [],
        ],
    ],
    'point' => [
        [
            'name' => 'admin.points.index',
            'value' => 'ポイント管理',
            'url' => route('admin.points.index'),
            'submenu' => [
                'admin.points.index',
                'admin.points.transaction_history',
                'admin.points.point_users',
            ],
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
