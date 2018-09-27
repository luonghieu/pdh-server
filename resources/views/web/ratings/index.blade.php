@section('title', 'Cheers Rating')
@section('controller.id', 'index-rating-controller')
@section('screen.id', 'go1')
@extends('layouts.web')
@section('web.extra')
    <div class="modal_wrap">
        <input id="rating-confirm-popup" type="checkbox">
        <div class="modal_overlay">
            <label for="rating-confirm-popup" class="modal_trigger" id="rating-confirm-label"></label>
            <div class="modal_content modal_content-btn2">
                <div class="text-box">
                    <p>この内容で評価しますか？</p>
                </div>
                <div class="close_button-box">
                    <div class="close_button-block">
                        <label for="rating-confirm-popup" class="close_button  left">キャンセル</label>
                    </div>
                    <div class="close_button-block" id="rating-confirm-btn">
                        <label class="close_button">評価する</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal_wrap">
        <input id="rating-alert" type="checkbox">
        <div class="modal_overlay">
            <label for="rating-alert" class="modal_trigger" id="rating-alert-label"></label>
            <div class="modal_content modal_content-btn3">
                <div class="content-in">
                    <h2 id="rating-alert-content"></h2>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('web.content')
    <?php
    $orderStartTime = \Carbon\Carbon::parse($order->date . ' ' . $order->start_time);
    $orderEndTime = $orderStartTime->copy()->addMinutes($order->duration * 60);
    ?>
    <h1 class="big-title">キャスト評価</h1>
    <div class="cast-profile">
        <section class="profile-photo">
            <div class="profile-photo_top"><img src="{{ $castUnrate->avatars->first()->thumbnail }}" alt=""></div>
            <h2>{{ $castUnrate->nickname . '(' . \Carbon\Carbon::parse($castUnrate->date_of_birth)->age . ')'}}</h2>
            <p>{{ $orderStartTime->format('Y年m月d日') . '(' . dayOfWeek()[$orderStartTime->dayOfWeek] . ')' }}</p>
            <p>{{ $orderStartTime->format('H:i') }}〜{{ $orderEndTime->format('H:i') }}</p>
        </section>
    </div>
    <form action="{{ route('create_rating') }}" method="POST" id="rating-create">
        <section class="evaluation-box">
            <ul class="">
                <li><p class="label1">満足度</p>
                    <span class="star-rating">
                <input type="radio" name="satisfaction" value="1"><i></i>
                <input type="radio" name="satisfaction" value="2" checked><i></i>
                <input type="radio" name="satisfaction" value="3"><i></i>
                <input type="radio" name="satisfaction" value="4"><i></i>
                <input type="radio" name="satisfaction" value="5"><i></i>
              </span>
                </li>
                <li><p class="label2">ルックス・<br>身だしなみ</p>
                    <span class="star-rating">
                <input type="radio" name="appearance" value="1" ><i></i>
                <input type="radio" name="appearance" value="2" checked><i></i>
                <input type="radio" name="appearance" value="3"><i></i>
                <input type="radio" name="appearance" value="4"><i></i>
                <input type="radio" name="appearance" value="5"><i></i>
              </span>
                </li>
                <li><p class="label3">愛想・気遣い</p>
                    <span class="star-rating">
                <input type="radio" name="friendliness" value="1" ><i></i>
                <input type="radio" name="friendliness" value="2" checked><i></i>
                <input type="radio" name="friendliness" value="3"><i></i>
                <input type="radio" name="friendliness" value="4"><i></i>
                <input type="radio" name="friendliness" value="5"><i></i>
              </span>
                </li>
            </ul>
            <div>
                <textarea class="form" name="comment" cols="50" rows="10" wrap="soft"
                          placeholder="よろしければ評価内容をご入力ください" id="rating-comment"></textarea>
            </div>
        </section>

        <section class="settlement-confirm">
            <input type="hidden" name="order_id" value="{{ request()->order_id }}">
            <input type="hidden" name="rated_id" value="{{ $castUnrate->id }}">
            <button type="button" class="button button-settlement" id="rating-submit-btn" disabled>評価する
                {{ ($totalRated != -1 || $order->total_cast != 1) ? $totalRated . '/' . $order->total_cast  : ''
                }}</button>
            <input type="hidden" id="next-rating-cast" value="{{ ($nextCast) ? $nextCast->id : '-1' }}">
        </section>
    </form>
@endsection