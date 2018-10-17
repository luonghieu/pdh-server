@php
  $currentRouteName = Route::currentRouteName();
  $routes = [
    ['name' => 'admin.notification_schedules.index', 'value' => '全ユーザー', 'url' => route('admin.notification_schedules.index', ['type' => \App\Enums\NotificationScheduleType::ALL])],
    ['name' => 'admin.notification_schedules.index', 'value' => 'ゲスト', 'url' => route('admin.notification_schedules.index', ['type' => \App\Enums\NotificationScheduleType::GUEST])],
    ['name' => 'admin.notification_schedules.index', 'value' => 'キャスト', 'url' => route('admin.notification_schedules.index', ['type' => \App\Enums\NotificationScheduleType::CAST])],
  ];
@endphp

<div class="panel-heading">
  <ul class="nav nav-tabs pull-left" id="tabs">
    @foreach ($routes as $route)
      <li class="{{ \App\Enums\NotificationScheduleType::getDescription($type) == $route['value'] ? 'active' : '' }}">
        <a href="{{ $route['url'] }}">{{ $route['value'] }}</a>
      </li>
    @endforeach
  </ul>
</div>
