@section('title', '予約詳細')
@section('controller.id', 'order-history-controller')
@section('screen.id', 'gl3')
@extends('layouts.web')
@section('web.extra')
    <div class="modal_wrap">
        <input id="request-update-point" type="checkbox">
        <div class="modal_overlay">
            <label for="request-update-point" class="modal_trigger"></label>
            <div class="modal_content modal_content-btn2">
                <div class="text-box">
                    <p>
                        本当に修正依頼しますか？
                    </p>
                    <p>
                        修正依頼をすると運営側から
                        <br>
                        キャストに事実確認を行います
                    </p>
                </div>
                <div class="close_button-box">
                    <div class="close_button-block">
                        <label for="request-update-point" class="close_button  left">いいえ</label>
                    </div>
                    <div class="close_button-block">
                        <label class="close_button" id="request-update-point-btn">修正依頼する</label>
                    </div>
                    <input type="hidden" id="request-update-cast-id">
                </div>
            </div>
        </div>
    </div>

    <div class="modal_wrap">
        <input id="request-buy-point" type="checkbox">
        <div class="modal_overlay">
            <label for="request-buy-point" class="modal_trigger"></label>
            <div class="modal_content modal_content-btn2">
                <div class="text-box">
                    <p id="request-buy-point-modal-title">
                    </p>
                    <p>
                        決済確定を実行すると
                        <br>
                        自動で購入されます
                    </p>
                </div>
                <div class="close_button-box">
                    <div class="close_button-block">
                        <label for="request-buy-point" class="close_button  left">キャンセル</label>
                    </div>
                    <div class="close_button-block">
                        <label class="close_button" id="request-buy-point-btn">決済を確定する</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal_wrap">
        <input id="payment-confirm" type="checkbox">
        <div class="modal_overlay">
            <label for="payment-confirm" class="modal_trigger"></label>
            <div class="modal_content modal_content-btn2">
                <div class="text-box">
                    <p>
                        決済を確定しますか？
                    </p>
                </div>
                <div class="close_button-box">
                    <div class="close_button-block">
                        <label for="payment-confirm" class="close_button  left">いいえ</label>
                    </div>
                    <div class="close_button-block">
                        <label class="close_button" id="payment-confirm-btn">はい</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal_wrap">
        <input id="payment-failed" type="checkbox">
        <div class="modal_overlay">
            <label for="payment-failed" class="modal_trigger"></label>
            <div class="modal_content modal_content-btn2">
                <div class="text-box">
                    <p>
                        決済に失敗しました
                    </p>
                    <p>
                        登録しているカード情報を
                        <br>
                        確認してください
                    </p>
                </div>
                <div class="close_button-box">
                    <div class="close_button-block w-100">
                        <label class="close_button"><a href="{{ route('credit_card.index') }}">カード情報を確認する</a></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal_wrap">
        <input id="alert-payment" type="checkbox">
        <div class="modal_overlay">
            <label for="alert-payment" class="modal_trigger" id="alert-payment-label"></label>
            <div class="modal_content modal_content-btn3">
                <div class="content-in">
                    <h2 id="alert-payment-content"></h2>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('web.content')
<a href="javascript:void(0)" id="payment-completed-gtm" class="gtm-hidden-btn" onclick="dataLayer.push({
    'userId': '<?php echo Auth::user()->id; ?>',
    'event': 'payment_complete'
});"></a>
@if ($order->status == \App\Enums\OrderStatus::CANCELED)
    <?php $orderStartTime = \Carbon\Carbon::parse($order->date . ' ' . $order->start_time)?>
    <?php $orderEndTime = $orderStartTime->copy()->addMinutes($order->duration * 60)?>
@else
    <?php $orderStartTime = \Carbon\Carbon::parse($order->actual_started_at)?>
    <?php $orderEndTime = \Carbon\Carbon::parse($order->actual_ended_at)?>
@endif

<?php $casts = $order->casts;?>
    <div class="settlement-confirm">
    <section class="details-header">
        <div class="details-header__title">予約詳細</div>
        <ul class="details-header__list">
            <li><i><img src="{{ asset('assets/web/images/common/date.svg') }}"></i>
                <p>
                    <span class="details-header__date">{{ $orderStartTime->format('Y年m月d日') }}</span>
                    @if ($order->status == \App\Enums\OrderStatus::CANCELED)
                        <span class="details-header__time">{{ $orderStartTime->format('H:i') . '~' . $orderEndTime->format('H:i') }}</span>
                    @else
                        <span class="details-header__time">{{ $orderStartTime->format('H:i') . '~' .
                        $orderEndTime->format('H:i') }}</span>
                    @endif
            </li>
            <li><i><img src="{{ asset('assets/web/images/common/map.svg') }}"></i>
                <p class="text-ellipsis text-address">{{ $order->address }}</p></li>
            <li><i><img src="{{ asset('assets/web/images/common/woman.svg') }}"></i>
                <p>{{ $order->total_cast . '名' }}</p></li>
        </ul>
    </section>
    <?php $orderTotalPoint = 0;?>
    @foreach($casts as $cast)
        <section class="details-list" id="cast-{{ $cast->id }}">
            <div class="details-list__line"><p></p></div>
            @if ($order->status == \App\Enums\OrderStatus::CANCELED)
                <h3 class="order-cancel-header">※キャンセル料が発生します</h3>
            @endif
            <div class="details-list__header">
                <div class="details-list__thumbnail init-height">
                    <a href="{{ route('cast.show', ['id' => $cast->id]) }}">
                        <img src="{{ @getimagesize($cast->avatars[0]->thumbnail) ? $cast->avatars[0]->thumbnail : '/assets/web/images/gm1/ic_default_avatar@3x.png' }}"
                             alt="Avatar">
                    </a>
                </div>
                <p class="details-list__name text-ellipsis text-nickname">{{ $cast->nickname }}</p>
                <b class="text-bold">{{ '(' . \Carbon\Carbon::parse($cast->date_of_birth)->age . ')' }}</b>
                <span class="details-list__button collapse" onclick="expandInfo('cast-{{ $cast->id }}', this)"></span>
            </div>
            <div class="details-list__content">
                <ul class="details-info-list">
                    <li class="details-info-list__itme">
                        <p class="details-info-list__text">{{ '合流' . $order->duration * 60 . '分' }}</p>
                        <p class="details-info-list__marks">
                            @if ($order->status == \App\Enums\OrderStatus::CANCELED)
                                0P
                            @else
                                {{ number_format($cast->cast_order->order_point) .'P' }}
                            @endif
                            </p>


                    </li>
                    <li class="details-info-list__itme">
                        <p class="details-info-list__text">{{ '延長' . $cast->cast_order->extra_time . '分' }}</p>
                        <p class="details-info-list__marks">{{ number_format(($cast->cast_order->extra_point) ?
                        $cast->cast_order->extra_point : 0) .
                         'P'
                        }}</p>
                    </li>
                    <li class="details-info-list__itme">
                        <p class="details-info-list__text">指名料</p>
                        <p class="details-info-list__marks">{{ number_format(($cast->cast_order->fee_point) ?
                        $cast->cast_order->fee_point : 0) . 'P'
                        }}</p>
                    </li>
                    <li class="details-info-list__itme">
                        <p class="details-info-list__text">深夜手当</p>
                        <p class="details-info-list__marks">{{ number_format(($cast->cast_order->allowance_point) ?
                        $cast->cast_order->allowance_point : 0) .
                         'P'
                        }}</p>
                    </li>
                    @if ($order->status == \App\Enums\OrderStatus::CANCELED)
                        <li class="details-info-list__itme">
                            <p class="details-info-list__text">{{ "キャンセル料($order->cancel_fee_percent%)" }}</p>
                            <p class="details-info-list__marks">{{ number_format($cast->cast_order->temp_point *
                            $order->cancel_fee_percent / 100
                            ) . 'P' }}</p>
                        </li>
                    @endif
                </ul>
                <ul class="">
                    <li class="details-info-list__itme">
                        <p class="details-info-list__text--subtotal">小計</p>
                        <p class="details-info-list__marks--subtotal point-fix-mt">
                            <?php $castTotalPoint = $cast->cast_order->total_point ? $cast->cast_order->total_point : ($cast->cast_order->temp_point * $order->cancel_fee_percent / 100)?>
                            <?php $orderTotalPoint += $castTotalPoint;?>
                            {{ number_format($castTotalPoint) . 'P' }}
                        </p>
                    </li>
                </ul>
            </div>
        </section>
    @endforeach

    <section class="details-total">
        <div class="details-list__line"><p></p></div>
        <div class="details-total__content">
            <div class="details-total__text">合計</div>
            <div class="details-total__marks">{{ number_format($orderTotalPoint) . 'P' }}</div>
        </div>
        <span class="details-total-desc">❉1P=1.1円で決済が実行されます</span>
    </section>
    <form action="{{ route('point_settement.create', ['id' => $order->id]) }}" method="POST" id="payment-form">
        {{ csrf_field() }}
        @if ($order->payment_status == \App\Enums\OrderPaymentStatus::REQUESTING)
        <div class="action" style="width: 100%; text-align: center;">
            <button class="btn-l" type="submit" id="payment-submit">決済を確定する</button>
        </div>

            @if ($order->payment_status != \App\Enums\OrderPaymentStatus::EDIT_REQUESTING)
                <a href="javascript:void(0)" class="point-fix"
                   onclick="openRequestUpdatePoint('{{ $order->id }}')">決済ポイントの修正を依頼する場合はこちら</a>
            @endif
        @endif

    </form>
</div>
@endsection

@section('web.extra_js')
    <script>
        const orderTotalPoint = parseInt('<?php echo $orderTotalPoint ?>');
        const guestTotalPoint = parseInt('<?php echo $user->point ?>');

        const orderId = '<?php echo $order->id; ?>';
        function expandInfo(selector, ele) {
            $(ele).toggleClass('collapse');
            $('#' + selector  + ' .details-list__content').toggleClass('show');
        }

        function openRequestUpdatePoint() {
            $('#request-update-point').trigger('click');
        }
    </script>

@endsection
