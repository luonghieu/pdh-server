@section('title', 'キャスト一覧')
@section('screen.id', 'gf1')
@extends('layouts.web')
@section('web.extra_css')
  <link rel="stylesheet" href="{{ mix('assets/web/css/gf_1.min.css') }}">
@endsection

@section('web.extra')
@if (Auth::check())
  @php
    $campaignFrom = Carbon\Carbon::parse('2018-11-28');
    $campaignTo = Carbon\Carbon::parse('2018-11-30 23:59:59');
  @endphp
  @if(Auth::user()->is_guest && Auth::user()->is_verified && !Auth::user()->campaign_participated && now()->between($campaignFrom, $campaignTo))
    @include('web.users.popup')
  @endif
@endif
@endsection
@section('web.content')
  <form id="search" method="GET" action="{{ route('cast.favorite') }}">
    @foreach (request()->all() as $key => $value)
    <input type="hidden" name="{{ $key }}" value="{{ $value }}" id="{{ $key }}">
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

  <!-- schedule -->
  @php $today = Carbon\Carbon::today(); @endphp
  <div class="cast-list init-scroll-x pb-2">
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

  @if (!$casts['data'])
  <div class="no-cast">
    <figure><img src="{{ asset('assets/web/images/common/woman2.svg') }}"></figure>
    <figcaption>キャストが見つかりません</figcaption>
  </div>
  @else
  <div class="cast-list">
    @include('web.users.load_more_list_casts', compact('casts'))
    <input type="hidden" id="next_page" value="{{ $casts['next_page_url'] }}">
    <!-- loading_page -->
    @include('web.partials.loading_icon')
  </div> <!-- /list_wrap -->
  @endif
@endsection
@section('web.script')
<!-- Change favorite -->
<script>
  $(function () {
    $('#heart_off').click(function (e) {
      e.preventDefault();

      $('#search').submit();
    });
  });
</script>

<!-- Load more list cast -->
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

          window.axios.get("<?php echo env('APP_URL') . '/cast/list/more' ?>", {
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

<!-- Js schedule -->
<script>
  $(function () {
    $('input[name=schedule_date]').click(function(event) {
      $('#gf1 label.button--green.js-schedule').removeClass('active');
      $(this).parent().addClass('active');

      params = {
        schedule: $(this).val(),
        prefecture_id: $('#prefecture_id').val(),
        class_id: $('#class_id').val(),
        point: $('#point').val(),
      };

      window.axios.get('/api/v1/casts', {params})
        .then(function(response) {
          var link = '/cast?schedule=' + params.schedule + '&prefecture_id=' + params.prefecture_id + '&class_id=' + params.class_id + '&point=' + params.point;
          window.location.href = link;
        })
        .catch(function(error) {
          console.log(error);
        });
    });
  });
</script><!-- /Js schedule -->
@endsection
