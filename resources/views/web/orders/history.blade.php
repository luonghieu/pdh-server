@section('title', 'Order History')
@section('controller.id', 'order-history-controller')
@section('screen.id', 'gl-3')
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
    <main id="gl3">
        <?php $orderStartTime = \Carbon\Carbon::parse($order->actual_started_at) ?>
        <?php $orderEndTime = \Carbon\Carbon::parse($order->actual_ended_at) ?>
        <?php $casts = $order->casts; ?>
            <div class="settlement-confirm">
            <section class="details-header">
                <div class="details-header__title">予約詳細</div>
                <ul class="details-header__list">
                    <li><i><img src="{{ asset('assets/web/images/common/date.svg') }}"></i>
                        <p>
                            <span class="details-header__date">{{ $orderStartTime->format('Y年m月d日') }}</span>
                            <span class="details-header__time">{{ $orderStartTime->format('H:i') . '~' .  $orderEndTime->format('H:i')
                            }}</span>
                    </li>
                    <li><i><img src="{{ asset('assets/web/images/common/map.svg') }}"></i>
                        <p>{{ $order->address }}</p></li>
                    <li><i><img src="{{ asset('assets/web/images/common/woman.svg') }}"></i>
                        <p>{{ $order->total_cast . '名' }}</p></li>
                </ul>
            </section>
            <?php $orderTotalPoint = 0; ?>
            @foreach($casts as $cast)
                <section class="details-list" id="cast-{{ $cast->id }}">
                    <div class="details-list__line"><p></p></div>
                    <div class="details-list__header">
                        <div class="details-list__thumbnail">
                            <a href="{{ route('cast.show', ['id' => $cast->id]) }}">
                                <img src="{{ $cast->avatars->first()->thumbnail }}" alt="Avatar">
                            </a>
                        </div>
                        <p class="details-list__name">
                            {{ $cast->nickname . '(' . \Carbon\Carbon::parse($cast->date_of_birth)->age . ')' }}
                        </p>
                        <span class="details-list__button" onclick="expandInfo('cast-{{ $cast->id }}', this)"></span>
                    </div>
                    <div class="details-list__content">
                        <ul class="details-info-list">
                            <li class="details-info-list__itme">
                                <p class="details-info-list__text">{{ '合流' . $order->duration * 60 . '分' }}</p>
                                <p class="details-info-list__marks">{{ number_format($cast->pivot->order_point) . 'P'
                                 }}</p>
                            </li>
                            <li class="details-info-list__itme">
                                <p class="details-info-list__text">{{ '延長' . $cast->pivot->extra_time . '分' }}</p>
                                <p class="details-info-list__marks">{{ number_format($cast->pivot->extra_point) . 'P'
                                }}</p>
                            </li>
                            <li class="details-info-list__itme">
                                <p class="details-info-list__text">指名料</p>
                                <p class="details-info-list__marks">{{ number_format($cast->pivot->fee_point) . 'P'
                                }}</p>
                            </li>
                            <li class="details-info-list__itme">
                                <p class="details-info-list__text">深夜手当</p>
                                <p class="details-info-list__marks">{{ number_format($cast->pivot->allowance_point) .
                                 'P'
                                }}</p>
                            </li>
                        </ul>
                        <ul class="">
                            <li class="details-info-list__itme">
                                <p class="details-info-list__text--subtotal">小計</p>
                                <p class="details-info-list__marks--subtotal point-fix-mt">
                                    <?php $castTotalPoint = $cast->pivot->total_point ? $cast->pivot->total_point : $cast->pivot->temp_point ?>
                                    <?php $orderTotalPoint += $castTotalPoint; ?>
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
                <span class="details-total-desc">1P=1.1円で決済が実行されます</span>
            </section>
            <form action="{{ route('point_settement.create', ['id' => $order->id]) }}" method="POST" id="payment-form">
                {{ csrf_field() }}
                @if ($order->payment_status == \App\Enums\OrderPaymentStatus::REQUESTING)
                <div class="action" style="width: 100%; text-align: center;">
                    <button class="btn-l" type="submit" id="payment-submit">決済を確定する</button>
                </div>

                    @if ($order->payment_status != \App\Enums\OrderPaymentStatus::EDIT_REQUESTING)
                        <a href="javascript:void(0)" class="point-fix"
                           onclick="openRequestUpdatePoint('{{ $order->id }}')">決済ポイントの修正依頼する</a>
                    @endif
                @endif

            </form>
        </div>
    </main>
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
