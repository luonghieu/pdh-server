@section('title', 'お気に入りキャスト')
@section('screen.id', 'gf1')
@extends('layouts.web')
@section('web.extra_css')
  <link rel="stylesheet" href="{{ mix('assets/web/css/gf_1.min.css') }}">
@endsection
@section('web.content')
  <form id="search" method="GET" action="{{ route('cast.list_casts') }}">
    @foreach (request()->all() as $key => $value)
    <input type="hidden" name="{{ $key }}" value="{{ $value }}" id="{{ $key }}">
    @endforeach
  </form>
  <div class="page-header">
    @php
      $urlSearch = route('cast.search') . '?schedule=' . request()->schedule . '&prefecture_id=' . request()->prefecture_id . '&class_id=' . request()->class_id . '&point=' . request()->point;
    @endphp
    <a href="{{ $urlSearch }}" class="search"><i><img src="{{ asset('assets/web/images/common/search.svg') }}" alt=""></i></a>
    <div class="header-right__menu">
      <a href="{{ route('cast.list_casts') }}" class="heart" id="heart_on"><i><img src="{{ asset('assets/web/images/common/like.svg') }}" alt=""></i></a>
      <a href="{{ route('cast_rank') }}" class="crown"><i><img src="{{ asset('assets/web/images/common/crown.svg') }}" alt=""></i></a>
    </div>
    <h1 class="text-bold">お気に入りキャスト</h1>
  </div>

  <!-- schedule -->
  @php $today = Carbon\Carbon::today(); @endphp
  <div class="cast-list init-scroll-x pb-2 js-scroll">
    <label class="button button--green js-schedule {{ (request()->schedule == null) ? 'active' : '' }}">
      <input type="radio" name="schedule_date" value="" {{ (request()->schedule == null) ? 'checked' : '' }}>全て
    </label>
    <label class="button button--green js-schedule {{ (request()->schedule == $today->format('Y-m-d')) ? 'active' : '' }}">
      <input type="radio" name="schedule_date" value="{{ $today->format('Y-m-d') }}" {{ (request()->schedule == $today->format('Y-m-d')) ? 'checked' : '' }}>今日OK
    </label>
    @for($i = 1; $i <= 6; $i++)
    @php $date = $today->copy()->addDays($i); @endphp
    <label class="button button--green js-schedule {{ (request()->schedule == $date->format('Y-m-d')) ? 'active' : '' }}">
      <input type="radio" name="schedule_date" value="{{ $date->format('Y-m-d') }}" {{ (request()->schedule == $date->format('Y-m-d')) ? 'checked' : '' }}>
      {{ $date->format('m/d') }} ({{ dayOfWeek()[$date->dayOfWeek] }})
    </label>
    @endfor
    <input type="hidden" name="schedule" value="{{ request()->schedule }}" id="schedule" />
  </div><!-- /schedule -->
  <div id="cast-list-wrapper">
    @if (!count($casts['data']))
      <div class="no-cast">
        <figure><img src="{{ asset('assets/web/images/common/woman2.svg') }}"></figure>
        <figcaption>キャストが見つかりません</figcaption>
      </div>
    @else
      <div class="cast-list" id="cast-list">
        @include('web.users.load_more_list_casts_favorite', compact('casts'))
        <input type="hidden" id="next_page" value="{{ ($casts['next_page_url']) ? $casts['next_page_url'] .
        '&is_ajax=1' : null }}">
        <!-- loading_page -->
        @include('web.partials.loading_icon')
      </div> <!-- /list_wrap -->
    @endif
  </div>
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
      return requesting == false && $(window).scrollTop() >= $(document).height() - windowHeight - 500;
    }

    function handleOnLoadMore() {
      // Improve load list image
      $('.lazy').lazy({
          placeholder: "data:image/gif;base64,R0lGODlhEALAPQAPzl5uLr9Nrl8e7..."
      });

      if (needToLoadmore()) {
        var url = $('#next_page').val();

        if (url) {
          // Hide page loading icon
          $('.js-loading').removeClass('css-loading-none');
          requesting = true;

          window.axios.get("<?php echo env('APP_URL')  . '/cast/favorite/more' ?>", {
            params: { next_page: url },
          }).then(function (res) {
            res = res.data;
            $('#next_page').val(res.next_page || '');
            $('#next_page').before(res.view);

            requesting = false;
            // Add page loading icon
            $('.js-loading').addClass('css-loading-none');
          }).catch(function () {
            requesting = false;
            // Add page loading icon
            $('.js-loading').addClass('css-loading-none');
          });
        }
      }
    }

    $(document).on('scroll', handleOnLoadMore);
    $(document).ready(handleOnLoadMore);
  });
</script>

<!-- Scroll center -->
<script>
  $(function () {
    jQuery.fn.scrollCenter = function(elem, speed) {
      var active = jQuery(this).find(elem);
      var activeWidth = active.width() / 2;
      
      var pos = active.position().left + activeWidth;
      var elpos = jQuery(this).scrollLeft();
      var elW = jQuery(this).width();
      pos = pos + elpos - elW / 2;

      jQuery(this).animate({
        scrollLeft: pos
      }, speed == undefined ? 1000 : speed);
      return this;
    };
    $('.js-scroll').scrollCenter(".active", 300);
  });
</script><!-- /Scroll center -->

<!-- Js schedule -->
<script>
  $(function () {
    $('input[name=schedule_date]').click(function(event) {
      $('#gf1 label.button--green.js-schedule').removeClass('active');
      $(this).parent().addClass('active');

      schedule = '';
      prefectureId = '';
      classId = '';
      point = '';

      if ($(this).val()) {
        schedule = $(this).val();
      }

      if ($('#prefecture_id').val()) {
        prefectureId = $('#prefecture_id').val();
      }

      if ($('#class_id').val()) {
        classId = $('#class_id').val();
      }

      if ($('#point').val()) {
        point = $('#point').val();
      }

      params = {
        schedule: schedule,
        prefecture_id: prefectureId,
        class_id: classId,
        point: point,
      };

      // link = '/cast/favorite?schedule=' + params.schedule + '&prefecture_id=' + params.prefecture_id + '&class_id=' + params.class_id + '&point=' + params.point;
      // window.location.href = link;

      params = {
          schedule: schedule,
          prefecture_id: prefectureId,
          class_id: classId,
          point: point,
          response_type: 'list-cast',
          favorited: 1
      };
      Object.keys(params).forEach((key) => (params[key] == '' || params[key] == null) && delete params[key]);

      axios.get('api/v1/casts', { params: params }).then(result => {
          $('#cast-list-wrapper #cast-list').remove();
          $('#cast-list-wrapper').append(result.data);
      });
    });
  });
</script><!-- /Js schedule -->
@endsection
