@section('title', 'メッセージ一覧')
@section('screen.id', 'gg')
@section('controller.id', 'rooms')
@extends('layouts.web')
@section('web.content')
<div class="title">
  <div class="title-name">
    <span>メッセージ一覧</span>
  </div>
</div>
@if (!$rooms->data)
  @include('web.rooms.no_room')
@else
<div class="msg-input">
  <form action="" class="msg-input-form">
    <input type="hidden" id="auth" value="{{ Auth::user()->id }}">
    <input class="form-btn" type="image" alt="検索" width="14 height="15" src="/assets/web/images/gg1/search.png" />
    <input class="form-input search-box" id="search-box" type="text" placeholder="ニックネームで検索">
  </form>
</div>
<div id="list-room">
@include('web.rooms.content-room',compact('rooms'))
</div>
@endif
@endsection

@section('web.extra')
@if (Auth::check())
    @if(Auth::user()->is_guest && Carbon\Carbon::parse(Auth::user()->created_at)->lt(Carbon\Carbon::parse('2018/11/10 00:00')))
      @include('web.users.popup')
    @endif
  @endif
@endsection
