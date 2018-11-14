@section('title', 'お気に入りキャスト')
@section('screen.id', 'gf1')
@extends('layouts.web')
@section('web.extra_css')
  <link rel="stylesheet" href="{{ mix('assets/web/css/gf_1.min.css') }}">
@endsection
@section('web.content')
  <form id="search" method="GET" action="{{ route('cast.list_casts') }}">
    @foreach (request()->all() as $key => $value)
    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
  </form>
  <div class="page-header">
    <a href="{{ route('cast.search') }}" class="search"><i><img src="{{ asset('assets/web/images/common/search.svg') }}" alt=""></i></a>
    <div class="header-right__menu">
      <a href="{{ route('cast.list_casts') }}" class="heart" id="heart_on"><i><img src="{{ asset('assets/web/images/common/like.svg') }}" alt=""></i></a>
      <a href="{{ route('cast_rank') }}" class="crown"><i><img src="{{ asset('assets/web/images/common/crown.svg') }}" alt=""></i></a>
    </div>
    <h1 class="text-bold">お気に入りキャスト</h1>
  </div>

  @if (!$favorites['data'])
  <div class="no-cast">
    <figure><img src="{{ asset('assets/web/images/common/woman2.svg') }}"></figure>
    <figcaption>キャストが見つかりません</figcaption>
  </div>
  @else
  <div class="cast-list">
    @include('web.users.load_more_list_casts_favorite', compact('favorites'))
    <input type="hidden" id="next_page" value="{{ $favorites['next_page_url'] }}">
  </div> <!-- /list_wrap -->
  </div>
  @endif
@endsection
@section('web.script')
<script>
  $(function () {
    $('#heart_on').click(function (e) {
      e.preventDefault();

      $('#search').submit();
    });
  });
</script>

<script>
  $(function () {
    var requesting = false;
    var windowHeight = $(window).height();

    function needToLoadmore() {
      return requesting == false && $(window).scrollTop() >= $(document).height() - windowHeight - 300;
    }

    function handleOnLoadMore() {
      // Improve load list image
      $('.lazy').lazy({
          placeholder: "data:image/gif;base64,R0lGODlhEALAPQAPzl5uLr9Nrl8e7..."
      });

      if (needToLoadmore()) {
        var url = $('#next_page').val();

        if (url) {
          requesting = true;
          window.axios.get("<?php echo env('APP_URL')  . '/cast/favorite/more' ?>", {
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
    }

    $(document).on('scroll', handleOnLoadMore);
    $(document).ready(handleOnLoadMore);
  });
</script>
@endsection
