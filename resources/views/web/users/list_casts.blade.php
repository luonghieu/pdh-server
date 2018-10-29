@section('title', 'キャスト一覧')
@section('screen.id', 'gf1')
@extends('layouts.web')
@section('web.extra_css')
  <link rel="stylesheet" href="{{ asset('assets/web/css/gf_1.css') }}">
@endsection
@section('web.content')
  <form id="search" method="GET" action="{{ route('cast.favorite') }}">
    @foreach (request()->all() as $key => $value)
    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
  </form>
  <div class="page-header">
    <a href="{{ route('cast.search') }}" class="search"><i><img src="{{ asset('assets/web/images/common/search.svg') }}" alt=""></i></a>
    <div class="header-right__menu">
      <a href="{{ route('cast.favorite') }}" class="heart" id="heart_off"><i><img src="{{ asset('assets/web/images/common/unlike.svg') }}" alt=""></i></a>
      <a href="{{ route('cast_rank') }}" class="crown"><i><img src="{{ asset('assets/web/images/common/crown.svg') }}" alt=""></i></a>
    </div>
    <h1 class="text-bold">キャスト一覧</h1>
  </div>

  @if (!$casts['data'])
  <div class="no-cast">
    <figure><img src="{{ asset('assets/web/images/common/woman.svg') }}"></figure>
    <figcaption>キャストが見つかりません</figcaption>
  </div>
  @else
  <div class="cast-list">
    @include('web.users.load_more_list_casts', compact('casts'))
    <input type="hidden" id="next_page" value="{{ $casts['next_page_url'] }}">
  </div> <!-- /list_wrap -->
  @endif
@endsection
@section('web.script')
<script>
  $(function () {
    $('#heart_off').click(function (e) {
      e.preventDefault();

      $('#search').submit();
    });
  });
</script>

<script>
  $(function () {
    var requesting = false;
    $(document).on('scroll', function () {
      if ($(window).scrollTop() + $(window).height() == $(document).height() && requesting == false) {
        var url = $('#next_page').val();

        if (url) {
          requesting = true;
          window.axios.get("<?php echo env('APP_URL')  . '/cast/list/more' ?>", {
            params: { next_page: url },
          }).then(function (res) {
            res = res.data;
            $('#next_page').val(res.next_page || '');
            $('#next_page').before(res.view);
            requesting = false;
          }).catch(function () {
            requesting = false;
          });
        }
      }
    });
  });
</script>
@endsection
