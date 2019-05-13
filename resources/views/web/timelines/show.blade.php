@section('title', 'Timeline details')
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
                        <a href="javascript:void(0)"><label class="close_button right">削除</label></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('web.content')
    <div class="timeline">
            <section class="portlet">
                <div class="portlet-content--timeline">
                    <div class="timeline-list">
                        <div class="timeline-item">
                            <div class="user-info">
                                <div class="user-info__profile">
                                    <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg" alt="">
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
                                <div class="user-info__del">
                                    <button onclick="document.getElementById('delete-timeline').click()">
                                        <img src="{{ asset('assets/web/images/common/timeline-like-button_del.svg') }}">
                                    </button>
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
                                            <img src="{{ asset('assets/web/images/timeline/article-img_002.jpg') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-like-list">
                                <div class="timeline-like-list__head">
                                    <div class="timeline-like">
                                        <button class="timeline-like__icon">
                                            <img src="{{ asset('assets/web/images/common/like-icon.svg') }}" alt="">
                                        </button>
                                        <p class="timeline-like__sum">113</p>
                                    </div>
                                </div>
                                <div class="timeline-like-list__content">
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>
                                    <div class="timeline-like-item">
                                        <div class="timeline-like-item__profile">
                                            <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                                        </div>
                                        <div class="timeline-like-item__info">
                                            <p>タケシ</p>
                                            <p>32歳</p>
                                        </div>
                                        <div class="timeline-like-item__chat">
                                            <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
</div>
@endsection