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
@section('web.script')
  <script>
      let roomLoading = false;
      let previousPageOldest = localStorage.setItem('previousPageOldest', localStorage.getItem('prev_page_older'));
      let previousPageOlder = localStorage.setItem('prev_page_older', localStorage.getItem('prev_page'));
      let previousPage = localStorage.setItem('prev_page', localStorage.getItem('current_page'));
      let currentPage = localStorage.setItem('current_page', window.location.pathname);

      jQuery(document).ready(function($) {
        if (window.history && window.history.pushState) {
          window.history.pushState(null, null, null);

          $(window).on('popstate', function() {
            if (localStorage.getItem('current_page').match(/message\/\d/)) {
              window.location.href = "/message";
            }

            if (localStorage.getItem('current_page')== '/message') {
              window.location.href = localStorage.getItem('previousPageOldest');
            }
          });
        }
      });
  </script>
@endsection

