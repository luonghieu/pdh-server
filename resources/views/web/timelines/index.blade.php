@section('title', 'Timeline details')
@section('controller.id', 'time-line-index-controller')

@extends('layouts.web')
@section('web.extra')

@endsection
@section('web.content')
    <div class="timeline">
        <section class="portlet">
            <div class="portlet-content--timeline">
                <div class="timeline-list">
                    <div class="timeline-item">
                        <div class="user-info">
                            <div class="user-info__profile">
                                <img src="/assets/web/images/timeline/timeline-profile-img_001.jpg" alt="">
                            </div>
                            <div class="user-info__text">
                                <div class="user-info__top">
                                    <p>Ayaka</p>
                                    <p>22</p>
                                </div>
                                <div class="user-info__bottom">
                                    <p>新宿</p>
                                    <p>16時間前</p>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-article">
                                <div class="timeline-article__text">今日今から一緒に飲めるかた！
                                    <br>可愛いまどかちゃんと一緒にいます❤︎
                                    <br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした 事がある。</div>
                            </div>
                            <div class="timeline-images">
                                <div class="timeline-images__list">
                                    <div class="timeline-images__item">
                                        <img src="/assets/web/images/timeline/article-img_001.jpg" width="100%">
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-like">
                                <button class="timeline-like__icon">
                                    <img src="./assets/web/images/common/like-icon.svg" alt="">
                                </button>
                                <p class="timeline-like__sum"><a href="{{ route('web.timelines.show', ['id' => 1])
                                }}">113</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="user-info">
                            <div class="user-info__profile">
                                <img src="/assets/web/images/timeline/timeline-profile-img_003.jpg" alt="">
                            </div>
                            <div class="user-info__text">
                                <div class="user-info__top">
                                    <p>Ayaka</p>
                                    <p>22</p>
                                </div>
                                <div class="user-info__bottom">
                                    <p>新宿</p>
                                    <p>昨日</p>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-article">
                                <div class="timeline-article__text">一緒に楽しく飲みましょう！と思える人がいいな-</div>
                            </div>
                            <div class="timeline-like">
                                <button class="timeline-like__icon active">
                                    <img src="./assets/web/images/common/like-icon_on.svg" alt="">
                                </button>
                                <p class="timeline-like__sum"><a href="{{ route('web.timelines.show', ['id' => 1])
                                }}">54</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="user-info">
                            <div class="user-info__profile">
                                <img src="assets/web/images/timeline/timeline-profile-img_002.jpg" alt="">
                            </div>
                            <div class="user-info__text">
                                <div class="user-info__top">
                                    <p>タケシ</p>
                                    <p>32歳</p>
                                </div>
                                <div class="user-info__bottom">
                                    <p>新宿</p>
                                    <p>3月5日</p>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-article">
                                <div class="timeline-article__text">今日今から一緒に飲めるかた！
                                    <br>可愛いまどかちゃんと一緒にいます❤︎
                                    <br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした 事がある。</div>
                            </div>
                            <div class="timeline-images">
                                <div class="timeline-images__list">
                                    <div class="timeline-images__item">
                                        <img src="assets/web/images/timeline/article-img_002.jpg">
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-like">
                                <button class="timeline-like__icon">
                                    <img src="./assets/web/images/common/like-icon.svg" alt="">
                                </button>
                                <p class="timeline-like__sum"><a href="{{ route('web.timelines.show', ['id' => 1])
                                }}">407</a>
                                </p>
                            </div>
                        </div>
                    </div>
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
