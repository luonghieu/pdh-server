@php
  $currentRouteName = Route::currentRouteName();
  $routes = [
    ['name' => 'admin.resigns.index', 'value' => '退会申請', 'url' => route('admin.resigns.index', ['resign_status' =>
    \App\Enums\ResignStatus::PENDING]), 'text' => '退会申請'],
    ['name' => 'admin.resigns.index', 'value' => '退会済み', 'url' => route('admin.resigns.index', ['resign_status' => \App\Enums\ResignStatus::APPROVED]), 'text' => '退会済み'],];
@endphp

<div class="panel-heading">
  <ul class="nav nav-tabs pull-left" id="tabs">
    @foreach ($routes as $route)
      <li class="{{ \App\Enums\ResignStatus::getDescription(request()->resign_status) == $route['text'] ? 'active' : ''
      }}">
        <a href="{{ $route['url'] }}">{{ $route['value'] }}</a>
      </li>
    @endforeach
  </ul>
</div>
