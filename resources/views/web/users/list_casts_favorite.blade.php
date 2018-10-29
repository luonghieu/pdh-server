@section('title', 'お気に入りキャスト')
@section('screen.id', 'gf1')
@extends('layouts.web')
@section('web.extra_css')
  <link rel="stylesheet" href="{{ asset('assets/web/css/gf_1.css') }}">
@endsection
@section('web.content')
  <form id="search" method="GET" action="{{ route('casts.list_casts') }}">
    @foreach (request()->all() as $key => $value)
    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
  </form>
  <div class="page-header">
    <a href="{{ route('casts.search') }}" class="search"><i><img src="{{ asset('assets/web/images/common/search.svg') }}" alt=""></i></a>
    <div class="header-right__menu">
      <a href="{{ route('casts.list_casts') }}" class="heart" id="heart_on"><i><img src="{{ asset('assets/web/images/common/like.svg') }}" alt=""></i></a>
      <a href="#" class="crown"><i><img src="{{ asset('assets/web/images/common/crown.svg') }}" alt=""></i></a>
    </div>
    <h1>お気に入りキャスト</h1>
  </div>

  @if (!$favorites['data'])
  <div class="no-cast">
    <figure><img src="{{ asset('assets/web/images/common/woman.svg') }}"></figure>
    <figcaption>キャストが見つかりません</figcaption>
  </div>
  @else
  <div class="cast-list">
    @include('web.users.load_more_list_casts_favorite', compact('favorites'))
    <input type="hidden" id="next_page_favorite" value="{{ $favorites['next_page_url'] }}">
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
    $(document).on('scroll', function () {
      if ($(window).scrollTop() + $(window).height() == $(document).height() && requesting == false) {
        var url = $('#next_page').val();

        if (url) {
          requesting = true;
          window.axios.get("<?php echo env('APP_URL')  . '/casts/favorite/more' ?>", {
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
