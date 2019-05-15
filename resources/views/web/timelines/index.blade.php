@section('title', 'タイムライン')
@section('controller.id', 'time-line-index-controller')

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

    <div class="modal_wrap">
        <input id="timeline-not-found" type="checkbox">
        <div class="modal_overlay">
          <label for="timeline-not-found" class="modal_trigger"></label>
          <div class="modal_content modal_content-btn1">
            <div class="text-box show-message-order-call">
              <p>タイムラインません</p>
            </div>
            <label for="timeline-not-found" class="close_button">OK</label>
          </div>
        </div>
    </div>

@endsection

@section('web.content')
    <div class="page-header-timeline">
        <h1 class="text-bold">タイムライン</h1>
    </div>
    <div class="timeline">
        <section class="portlet">
            <div class="portlet-content--timeline">
                <input type="hidden" value="{{ Auth::user()->id }}" id="user_id_login">
                @if(isset($userId))
                <input type="hidden" name="user_id" value="{{ $userId }}" id="user_id_timelines">
                @endif
                <div class="timeline-list" id="timeline-index">
                    @if(isset($timelines['data']))
                        @foreach($timelines['data'] as $timeline)
                        <div class="timeline-item" id="timeline-{{ $timeline['id'] }}">
                            <div class="user-info">
                                <div class="user-info__profile">
                                    @if(App\Enums\UserType::CAST == $timeline['user']['type'])
                                    <a href="{{ route('cast.show', ['id' => $timeline['user']['id']]) }}">
                                    @else
                                    <a href="{{ route('guest.show', ['id' => $timeline['user']['id']]) }}">
                                    @endif
                                    @if (@getimagesize($timeline['user']['avatars'][0]['thumbnail']))
                                        <img class="lazy" data-src="{{ $timeline['user']['avatars'][0]['thumbnail'] }}" alt="">
                                        @else
                                        <img class="lazy" data-src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
                                    @endif
                                    </a>
                                </div>
                                <a href="{{ route('web.timelines.show', ['id' => $timeline['id']]) }}">
                                    <div class="user-info__text">
                                        <div class="user-info__top">
                                            <p>{{ $timeline['user']['nickname'] }}</p>
                                            <p>{{ $timeline['user']['age'] }}歳</p>
                                        </div>
                                        <div class="user-info__bottom">
                                            @if( mb_strlen($timeline['location']) >= 18)
                                            <p style="font-size: 10px">
                                            @else
                                            <p>
                                            @endif
                                                {{ $timeline['location'] }} {{ $timeline['location'] ? '・' : '' }}
                                                {{ Carbon\Carbon::parse($timeline['created_at'])->format('m/d H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                              @if(Auth::user()->id == $timeline['user']['id'])
                              <div class="timeline-delete" data-id="{{ $timeline['id'] }}">
                                 <img src="{{ asset('assets/web/images/common/timeline-like-button_del.svg') }}" alt="">
                              </div>
                              @endif
                            </div>
                            <div class="timeline-content">
                                <a href="{{ route('web.timelines.show', ['id' => $timeline['id']]) }}">
                                    <div class="timeline-article">
                                        <div class="timeline-article__text">{!! nl2br($timeline['content']) !!}</div>
                                    </div>
                                    @if($timeline['image'])
                                    <div class="timeline-images">
                                        <div class="timeline-images__list">
                                            <div class="timeline-images__item">
                                                <img class="lazy" data-src="{{ $timeline['image'] }}" width="100%">
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </a>
                                <div class="timeline-like">
                                    <button class="timeline-like__icon" data-id="{{ $timeline['id'] }}">
                                    @if($timeline['is_favourited'])
                                      <img src="{{ asset('assets/web/images/common/like-icon_on.svg') }}" alt="">
                                    @else
                                      <img src="{{ asset('assets/web/images/common/like-icon.svg') }}" alt="">
                                    @endif
                                    </button>
                                    <p class="timeline-like__sum"><a href="{{ route('web.timelines.show', ['id' => $timeline['id']])
                                      }}">{{ $timeline['total_favorites'] }}</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <input type="hidden" id="next_page" value="{{ $timelines['next_page_url'] }}" />
                    @endif
                </div>
            </div>
        </section>
        <section class="timeline-button">
            <a href="">
                <button class="timeline-button__edit">
                    <a href="{{ route('web.timelines.create') }}">
                        <img src="{{ asset('assets/web/images/common/timeline-button_edit.svg') }}">
                    </a>
                </button>
            </a>
        </section>
    </div>
@endsection

@section('web.script')
    <script>
        var btnNotLike = "<?php echo asset('assets/web/images/common/like-icon.svg'); ?>";
        var btnLike = "<?php echo asset('assets/web/images/common/like-icon_on.svg'); ?>";
        var loadMoreTimelines = "<?php echo env('APP_URL') . '/timelines/load_more' ?>";
        var showDetail = "<?php echo env('APP_URL') . '/timelines' ?>";
    </script>
@endsection