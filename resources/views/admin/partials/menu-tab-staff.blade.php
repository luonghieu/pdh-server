@php
  $currentRouteName = Route::currentRouteName();
  $routes = [
    ['name' => 'admin.staffs.show', 'value' => '基本情報', 'url' => route('admin.staffs.show',['id' => $staff->id ])],
    ['name' => 'admin.staffs.order_histories', 'value' => '仕事履歴', 'url' => route('admin.staffs.order_histories', ['staff' => $staff->id])],
    ['name' => 'admin.staffs.ratings', 'value' => '口コミ履歴', 'url' => route('admin.staffs.ratings', ['id' => $staff->id ])],
    ['name' => 'admin.staffs.rates', 'value' => $staff->full_name . 'さんの評価', 'url' => route('admin.staffs.rates', ['id' => $staff->id ])],
    ['name' => 'admin.staffs.notifications', 'value' => 'プッシュ通知受信履歴', 'url' => route('admin.staffs.notifications', ['id' => $staff->id])],
    ['name' => '#notify', 'value' => '振込履歴', 'url' => '#notify'],
    ['name' => 'admin.staffs.staff_designs', 'value' => '投稿写真', 'url' => route('admin.staffs.staff_designs',['staff' => $staff->id ])],
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
