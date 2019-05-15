@section('title', 'タイムライン')
@section('controller.id', 'time-line-show-controller')
@extends('layouts.web')
@section('web.extra')
  <div class="modal_wrap">
    <input id="delete-timeline" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger2" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
        <div class="text-box">
          <h2>投稿を削除しますか？</h2>
        </div>
        <div class="close_button-box">
          <div class="close_button-block">
            <label for="delete-timeline" class="close_button left">キャンセル</label>
          </div>
          <div class="close_button-block">
            <a href="javascript:void(0)" id="url-del-timeline"><label class="close_button right">削除</label></a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('web.content')
  <input type="hidden" id="nickname" value="{{ $user->nickname }}" />
  <input type="hidden" id="age" value="{{ $user->age }}" />
  <input type="hidden" id="avatar" value="{{ $user->avatars->first() ? $user->avatars[0]['path'] : '/assets/web/images/gm1/ic_default_avatar@3x.png' }}" />
  <input type="hidden" id="timeline-user-id" value="{{ $user->id }}" />
  <div class="page-header-timeline">
    <h1 class="text-bold">タイムライン</h1>
  </div>
  <div class="timeline">
    <section class="portlet">
      <div class="portlet-content--timeline">
        <div class="timeline-list">
          <div class="timeline-item">
            <div class="user-info">
              <div class="user-info__profile">
                @php $profileLink = ($timeline['user']['type'] == \App\Enums\UserType::GUEST) ? route('guest.show',
                ['id' => $timeline['user']['id']]) : route('cast.show', ['id' => $timeline['user']['id']])
                @endphp
                <a href="{{ $profileLink }}">
                  <img src="{{ $timeline['user']['avatars'] ? $timeline['user']['avatars'][0]['path'] : '/assets/web/images/gm1/ic_default_avatar@3x.png' }}" alt="">
                </a>
              </div>
              <div class="user-info__text">
                <div class="user-info__top">
                  <p>{{ $timeline['user']['nickname'] }}</p>
                  <p>{{ $timeline['user']['age'] }}歳</p>
                </div>
                <div class="user-info__bottom">
                  <p>{{ $timeline['location'] }}</p><p>{{ $timeline['location'] ? '・' : '' }}</p>
                  <p>{{ Carbon\Carbon::parse($timeline['created_at'])->format('m/d H:i') }}</p>
                </div>
              </div>
            </div>
            <div class="timeline-content">
              <div class="timeline-article">
                <div class="timeline-article__text init-text-justify">
                  {!! nl2br($timeline['content']) !!}
                </div>
              </div>
              <div class="timeline-images">
                <div class="timeline-images__list">
                  <div class="timeline-images__item">
                    @if ($timeline['image'])
                    <img src="{{ $timeline['image'] }}">
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="timeline-like-list">
              <div class="timeline-like-list__head">
                <div class="timeline-like">
                  <button class="timeline-like__icon">
                    <div id="heart-timeline" data-timeline-id="{{ $timeline['id'] }}" data-is-favorited-timeline="{{ $timeline['is_favourited'] }}" data-total-favorites-timeline="{{ $timeline['total_favorites'] }}">
                      @if($timeline['is_favourited'])
                      <img class="init-cursor" src="{{ asset('assets/web/images/common/like.svg ') }}" alt="">
                      @else
                      <img class="init-cursor" src="{{ asset('assets/web/images/common/unlike.svg ') }}" alt="">
                      @endif
                    </div>
                  </button>
                  <p class="timeline-like__sum" id="total-favorites">{{ $timeline['total_favorites'] }}</p>
                </div>
                <div class="user-info__del">
                  @if ($timeline['user']['id'] == Auth::user()->id)
                  <button onclick="document.getElementById('delete-timeline').click()" class="del-timeline" data-timeline-id="{{ $timeline['id'] }}">
                    <img  class="init-cursor" src="{{ asset('assets/web/images/common/timeline-like-button_del.svg') }}">
                  </button>
                  @endif
                </div>
              </div>
              <div class="timeline-like-list__content js-add-favorite">
                @include('web.timelines.load_more_favorites', compact('favorites', 'user'))
                <input type="hidden" id="next_page" value="{{ ($favorites['next_page_url']) ?  $favorites['next_page_url'] . '&is_ajax=1' : null  }}">
                <!-- loading_page -->
                @include('web.partials.loading_icon')
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
@endsection
@section('web.script')
<script>
  $(function () {
    var requesting = false;
    var windowHeight = $(window).height();

    function needToLoadmore() {
      return requesting == false && $(window).scrollTop() >= $(document).height() - windowHeight;
    }

    function handleOnLoadMore() {
      if (needToLoadmore()) {
        var url = $('#next_page').val();

        if (url) {
          // Hide page loading icon
          $('.js-loading').removeClass('css-loading-none');
          requesting = true;

          window.axios.get("<?php echo env('APP_URL') . '/timelines/favorites/load_more' ?>", {
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

<!-- Delete timeline -->
<script>
  $(function () {
    $('.del-timeline').on('click', function() {
      var id = $(this).attr('data-timeline-id');
      var oldURL = document.referrer;

      $('#url-del-timeline').on('click', function() {
        window.axios.delete('api/v1/timelines/' + id)
          .then(function(response) {
            window.location = oldURL;
          })
          .catch(function(error) {
            if (error.response.status == 401) {
              window.location = '/login/line';
            }
          });
      })
    })
  })
</script>
@stop
