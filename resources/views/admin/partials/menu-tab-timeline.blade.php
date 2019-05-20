@php
  $currentRouteName = Route::currentRouteName();
  $routes = [
    ['name' => 'admin.timelines.index', 'value' => '投稿一覧', 'url' => route('admin.timelines.index', ['hidden' => \App\Enums\TimelineStatus::PUBLIC]), 'text' => '公開'],
    ['name' => 'admin.timelines.index', 'value' => '非公開' . PHP_EOL . '投稿一覧', 'url' => route('admin.timelines.index', ['hidden' => \App\Enums\TimelineStatus::PRIVATE]), 'text' => '非公開'],
  ];
@endphp

<div class="panel-heading">
  <ul class="nav nav-tabs pull-left" id="tabs">
    @foreach ($routes as $route)
      <li class="{{ \App\Enums\TimelineStatus::getDescription($hidden) == $route['text'] ? 'active' : '' }}">
        <a href="{{ $route['url'] }}">{{ $route['value'] }}</a>
      </li>
    @endforeach
  </ul>
</div>
