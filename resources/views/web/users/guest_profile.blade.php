@section('title', 'ゲスト詳細')
@section('screen.id', 'gf2')
@section('controller.id', 'guest_profile_controller')
@extends('layouts.web')
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
@section('web.content')
<div class="cast-call">
    @if($guest['is_new_user'])
        <img src="/assets/web/images/common/ic_new_user.png" class="ic-new-show-user" alt="">
    @endif
    <section class="cast-photo">
        <div class="slider cast-photo__show">
            @if($guest['avatars'])
                @foreach ($guest['avatars'] as $avatar)
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
        @if (isset($guest['is_online']) && $guest['last_active'])
            <input type="hidden" id="is-online" value="{{ $guest['is_online'] }}">
            <span class="init-status text-bold init-last">
        <i class="{{ $guest['is_online'] ? 'online' : 'offline' }}"></i>
                {{ $guest['is_online'] ? 'オンライン' : $guest['last_active'] }}
      </span>
        @endif
    </section>
    <div class="cast-set">
        <section class="cast-info">
            <ul class="cast-info__list">
                <li class="cast-info__item text-ellipsis text-nickname">{{ $guest['nickname'] }}</li>
                <li class="cast-info__item"><b class="text-bold">{{ (!$guest['age']) ? '' : ($guest['age'] . "歳")
                }}</b>
                </li>
                @if(!Auth::user()->is_guest)
                <li>
                    <div class="guest-star-rating">
                        <?php $ratingScore = ($guest['rating_score']) ? $guest['rating_score'] * 100 / 5 : 0?>
                        <span style="width: {{ $ratingScore }}"></span>
                    </div>
                </li>
                @endif
                <li><span class="text-bold">{{ $guest['rating_score'] }}</span></li>
            </ul>
            <p class="cast-info__signature">{{ $guest['job'] }} &nbsp;&nbsp; {{ $guest['salary'] }}</p>
        </section>
        <section class="portlet">
            <div class="portlet-header">
                <h2 class="portlet-header__title">自己紹介</h2>
            </div>
            <div class="portlet-content">
                <p class="portlet-content__text">{{ $guest['intro'] }}</p>
            </div>
        </section>
        <section class="portlet">
            <div class="portlet-header">
                <h2 class="portlet-header__title">基本情報</h2>
            </div>
            <div class="portlet-content">
                <ul class="portlet-content__list">
                    <li class="portlet-content__item">
                        <p class="portlet-content__text--list">身長</p>
                        <p class="portlet-content__value"><span>{{ $guest['height'] . 'cm' }}</span>
                        </p>
                    </li>
                    <li class="portlet-content__item">
                        <p class="portlet-content__text--list">体型</p>
                        <p class="portlet-content__value">{{ $guest['body_type'] }}</p>
                    </li>
                    <li class="portlet-content__item">
                        <p class="portlet-content__text--list">ご利用エリア</p>
                        <p class="portlet-content__value">{{ $guest['prefecture'] }}</p>
                    </li>
                    <li class="portlet-content__item">
                        <p class="portlet-content__text--list">出身地</p>
                        <p class="portlet-content__value">{{ $guest['hometown'] }}</p>
                    </li>
                    <li class="portlet-content__item">
                        <p class="portlet-content__text--list">お仕事</p>
                        <p class="portlet-content__value">{{ $guest['job'] }}</p>
                    </li>
                    <li class="portlet-content__item">
                        <p class="portlet-content__text--list">お酒</p>
                        <p class="portlet-content__value">{{ $guest['drink_volume'] }}</p>
                    </li>
                    <li class="portlet-content__item">
                        <p class="portlet-content__text--list">タバコ</p>
                        <p class="portlet-content__value">{{ $guest['smoking'] }}</p>
                    </li>
                    <li class="portlet-content__item">
                        <p class="portlet-content__text--list">兄弟</p>
                        <p class="portlet-content__value">{{ $guest['siblings'] }}</p>
                    </li>
                    <li class="portlet-content__item">
                        <p class="portlet-content__text--list">同居人</p>
                        <p class="portlet-content__value">{{ $guest['cohabitant'] }}</p>
                    </li>
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
                                    <p>{{ $timeline['user']['age'] . '歳' }}</p>
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
                    <a href="{{ route('web.timelines.index', ['user_id' => $guest['id']]) }}"><p>さらに見る</p></a>
                </div>
            @endif
        </div>
    </section>
</div>
@endif
@endsection
@section('web.extra_js')
    <script>
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
@endsection
@section('web.extra_css')
    <style>
        footer {
            height: 12%;
            padding-top: 0;
            padding-bottom: 15px;
        }
    </style>
@stop
