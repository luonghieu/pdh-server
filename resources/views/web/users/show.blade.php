@section('title', 'キャスト詳細')
@section('screen.id', 'gf2')
@section('controller.id', 'cast_profile_controller')
@extends('layouts.web')
@section('web.content')
@section('web.extra')

  <div class="modal_wrap modal-confirm">
    <input id="timeline-del" type="checkbox">
    <div class="modal_overlay">
      <label for="timeline-del" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
        <div class="text-box">
          <h2>投稿を削除しますか？</h2>
        </div>
        <div class="close_button-box">
          <div class="close_button-block">
            <label for="timeline-del" class="close_button  left ">キャンセル</label>
          </div>
          <div class="close_button-block">
            <label for="timeline-del" data-id='' class="close_button right" id="btn-del-timeline">削除</label>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
<div class="cast-call">
  @if($cast['is_new_user'])
  <img src="/assets/web/images/common/ic_new_user.png" class="ic-new-show-user" alt="">
  @endif
  <section class="cast-photo">
    <div class="slider cast-photo__show">
      @if($cast['avatars'])
        @foreach ($cast['avatars'] as $avatar)
          @if (($avatar['path']))
          <img data-lazy="{{ $avatar['path'] }}">
          @else
          <img data-lazy="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}">
          @endif
        @endforeach
      @else
        <img class="image-default" data-lazy="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}">
      @endif
    </div>
    @if ($cast['working_today'])
      <input type="hidden" id="working-today" value="{{ $cast['working_today'] }}">
      <span class="init-today text-bold">今日OK</span>
    @endif
    @if (isset($cast['is_online']) && $cast['last_active'])
      <input type="hidden" id="is-online" value="{{ $cast['is_online'] }}">
      <span class="init-status text-bold">
        <i class="{{ $cast['is_online'] ? 'online' : 'offline' }}"></i>
        {{ $cast['is_online'] ? 'オンライン' : $cast['last_active'] }}
      </span>
    @endif
    <img src="/assets/web/images/gf1/ic_pick_calendar.svg" alt="" class="ic_pick_calendar">
  </section>
  <div class="cast-set">
    <section class="cast-info">
      <ul class="cast-info__list">
        <li class="cast-info__item text-ellipsis text-nickname">{{ $cast['nickname'] }}</li>
        <li class="cast-info__item"><b class="text-bold">{{ (!$cast['age']) ? '' : ($cast['age'] . "歳") }}</b></li>
        @php
          $class = '';
          if (isset($cast['class_id'])) {
            switch ($cast['class_id']) {
                case 1:
                    $class = 'bronz';
                    break;
                case 2:
                    $class = 'platinum';
                    break;
                case 3:
                    $class = 'daiamond';
                    break;
            }
          }
        @endphp
        <li class="{{ $class }}">{{ isset($cast['class']) ? $cast['class'] : '未設定' }}</li>
      </ul>
      <p class="cast-info__signature">{{ $cast['job'] }}{{ (!$cast['job'] || !$cast['intro']) ? '' : ' | '}}{{ $cast['intro'] }}</p>
      <p class="cast-info__price">30分あたりの料金<span class="text-bold">{{ isset($cast['cost']) ? number_format($cast['cost']) : '未設定' }}P</span></p>
    </section>

    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">自己紹介</h2>
      </div>
      <div class="portlet-content">
        @if (!$cast['description'])
        <p class="portlet-header__title">自己紹介設定されていません</p>
        @else
        <p class="portlet-content__text">{!! strip_tags(nl2br($cast['description']), '<br>') !!}</p>
        @endif
      </div>
    </section>

    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">基本情報</h2>
      </div>
      @php
        switch ($cast['height']) {
            case '0':
                $height = '非公開';
                break;

            default:
                $height = (!$cast['height']) ? '' : $cast['height'] . 'cm';
                break;
        }
      @endphp
      <div class="portlet-content">
        <ul class="portlet-content__list">
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">身長</p>
            @if (!$height)
            <p class="portlet-content__text--list">未設定</p>
            @else
            <p class="portlet-content__value"><span>{{ $height }}</span></p>
            @endif
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">体型</p>
            @if (!$cast['body_type'])
            <p class="portlet-content__text--list">未設定</p>
            @else
            <p class="portlet-content__value">{{ $cast['body_type'] }}</p>
            @endif
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">稼働エリア</p>
            @if (!$cast['prefecture'])
            <p class="portlet-content__text--list">未設定</p>
            @else
            <p class="portlet-content__value">{{ $cast['prefecture'] }}</p>
            @endif
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">出身地</p>
            @if (!$cast['hometown'])
            <p class="portlet-content__text--list">未設定</p>
            @else
            <p class="portlet-content__value">{{ $cast['hometown'] }}</p>
            @endif
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">お仕事</p>
            @if (!$cast['job'])
            <p class="portlet-content__text--list">未設定</p>
            @else
            <p class="portlet-content__value">{{ $cast['job'] }}</p>
            @endif
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">お酒</p>
            @if (!$cast['drink_volume'])
            <p class="portlet-content__text--list">未設定</p>
            @else
            <p class="portlet-content__value">{{ $cast['drink_volume'] }}</p>
            @endif
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">同居人</p>
            @if (!$cast['cohabitant'])
            <p class="portlet-content__text--list">未設定</p>
            @else
            <p class="portlet-content__value">{{ $cast['cohabitant'] }}</p>
            @endif
          </li>
        </ul>
      </div>

    </section>

    <section class="portlet" id="list-shift-of-cast">
      <input type="hidden" id="cast-id" value="{{$cast['id']}}">
      <div class="portlet-header">
        <h2 class="portlet-header__title title-shifts">スケジュール</h2>
      </div>
      <div class="portlet-content">
        <ul class="portlet-content__list">
          @foreach($shifts as $shift)
          <li class="portlet-content__item">
            @php
            $classColorDay = '';
            if (\Carbon\Carbon::parse($shift['date'])->dayOfWeek == 0) {
              $classColorDay = 'color-day-shift-red';
            }

            if (\Carbon\Carbon::parse($shift['date'])->dayOfWeek == 6) {
              $classColorDay = 'color-day-shift-blue';
            }
            @endphp
            <p class="portlet-content__text--list color-shift">{{\Carbon\Carbon::parse($shift['date'])->format('m/d')}}&nbsp;&nbsp; <span class="{{$classColorDay}}">{{dayOfWeek()[\Carbon\Carbon::parse($shift['date'])->dayOfWeek]}}</span></p>
            <p class="portlet-content__text--list color-shift">
              @if($shift['users']['day_shift'])
              <span>OK</span>
              @endif
              @if($shift['users']['night_shift'])
              <span>{{$shift['users']['day_shift'] ? '・': ''}}深夜OK</span>
              @endif
              @if((!$shift['users']['day_shift'] && !$shift['users']['night_shift']) || $shift['users']['off_shift'])
              <span>-</span>
              @endif
            </p>
            <p class="portlet-content__value wrap-ic-shift">
              @if($shift['users']['off_shift'] == 0 && ($shift['users']['day_shift'] || $shift['users']['night_shift']))
                <img src="/assets/web/images/gf1/ic_shift.svg" alt="" class="btn-order-nominee" data-shift="{{$shift['date']}}">
              @endif
            </p>
          </li>
          @endforeach
        </ul>
      </div>
    </section>

    <!-- profile-word -->
  </div>
</div>
@if(env('ENABLE_TIMELINE'))
<div class="timeline">
  <section class="portlet">
    <div class="portlet-header">
      <h2 class="portlet-header__title title-shifts">タイムライン</h2>
    </div>
    <div class="portlet-content--timeline">
      @foreach($timelines as $key => $timeline)
      @if ($key < 5)
      <div class="timeline-list">
        <div class="timeline-item">
          <div class="user-info">
            <div class="user-info__profile">
              <img src="{{ $timeline['user']['avatars'] ? $timeline['user']['avatars'][0]['path'] : '/assets/web/images/gm1/ic_default_avatar@3x.png' }}" alt="">
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
            @if ($timeline['user']['id'] == Auth::user()->id)
              <div class="timeline-delete" data-id="{{ $timeline['id'] }}">
                <img src="{{ asset('assets/web/images/common/timeline-like-button_del.svg') }}" alt="">
              </div>
            @endif
          </div>
          <div class="timeline-content">
            <a href="{{ route('web.timelines.show', ['timeline' => $timeline['id']]) }}" class="init-text-color">
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
            </a>
            <div class="timeline-like heart-timeline" data-timeline-id="{{ $timeline['id'] }}">
              <button class="timeline-like__icon">
                <div id="heart-timeline-{{ $timeline['id'] }}" data-is-favorited-timeline="{{ $timeline['is_favourited'] }}" data-total-favorites-timeline="{{ $timeline['total_favorites'] }}">
                  @if($timeline['is_favourited'])
                  <img src="{{ asset('assets/web/images/common/like.svg ') }}" alt="">
                  @else
                  <img src="{{ asset('assets/web/images/common/unlike.svg ') }}" alt="">
                  @endif
                </div>
              </button>
              <p class="timeline-like__sum" id="total-favorites-{{ $timeline['id'] }}">{{ $timeline['total_favorites'] }}</p>
            </div>
          </div>
        </div>
      </div>
      @endif
      @endforeach
      @if (count($timelines) > 5)
      <div class="timeline-more">
        <a href="{{ route('web.timelines.index', ['user_id' => $cast['id']]) }}"><p>さらに見る</p></a>
      </div>
      @endif
    </div>
  </section>
</div>
@endif
<div class="cast-call-btn">
  <a class="heart" id="favorite-cast-detail" data-user-id="{{ $cast['id'] }}" data-is-favorited="{{ $cast['is_favorited'] }}">
    @if ($cast['is_favorited'])
    <img src="{{ asset('assets/web/images/common/like.svg') }}"><span class="text-color">イイネ済</span>
    @else
    <img src="{{ asset('assets/web/images/common/unlike.svg') }}"><span class="text-color">イイネ</span>
    @endif
  </a>
  <a class="msg" id="create-room" data-user-id="{{ $cast['id'] }}">
    <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
    <span class="text-color">メッセージ</span>
  </a>
  <div class="btn-m"><a href="{{ route('guest.orders.nominate',['id' => $cast['id'] ]) }}">指名予約する</a></div>
</div>
@endsection
@section('web.script')
<script>
 if(localStorage.getItem("order_params")){
    localStorage.removeItem("order_params");
  }
</script>
<script>
  $(function () {
    workingToday = $('#working-today').val();
    isOnline = $('#is-online').val();

    if (!workingToday && isOnline) {
      $('.init-status').addClass('init-last');
    }

    $('.ic_pick_calendar').on('click', function () {
        $("html, body").animate({scrollTop: $('#list-shift-of-cast').offset().top }, 1000);
    });
  });

  $('body').on('click', ".timeline-delete", function(){
      var id = $(this).data("id");
      $('#btn-del-timeline').data('id', '');
      $('#btn-del-timeline').data('id', id);

      $('#timeline-del').prop('checked', true);

  });

  $('body').on('click', "#btn-del-timeline", function(){
      var id = $(this).data("id");
      if(id) {
          window.axios.delete('/api/v1/timelines/' + id)
              .then(function(response) {
                  window.location.reload();
              })
              .catch(function (error) {
                  console.log(error);
                  if (error.response.status == 401) {
                      window.location = '/login';
                  }

                  if (error.response.status == 404) {
                      $('#timeline-not-found').prop('checked', true);
                  }
              });
      } else {
          window.location = '/login';
      }
  })
</script>

@stop

@section('web.extra_css')
<style>
  footer {
    height: 12%;
    padding-top: 0;
    padding-bottom: 15px;
  }
</style>
@stop
