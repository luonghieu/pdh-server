@section('title', 'ポイント購入')
@section('controller.id', 'index-point-controller')
@section('screen.id', 'gl4')
@extends('layouts.web')
@section('web.extra')
<div class="modal_wrap">
    <input id="buypoint-popup" type="checkbox">
    <div class="modal_overlay">
        <label for="buypoint-popup" class="modal_trigger"></label>
        <div class="modal_content modal_content-btn2">
            <div class="text-box">
                <h2 id="popup-amount"></h2>
                <h2>購入しますか？</h2>
            </div>
            <div class="close_button-box">
                <div class="close_button-block">
                    <label for="buypoint-popup" class="close_button  left">キャンセル</label>
                </div>
                <div class="close_button-block" id="buypoint-confirm">
                    <label class="close_button right">購入する</label>
                    <input type="hidden" id="point-amount">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal_wrap">
    <input id="popup-require-card" type="checkbox">
    <div class="modal_overlay">
        <label for="popup-require-card" class="modal_trigger" id="popup-require-card-label"></label>
        <div class="modal_content modal_content-btn1">
            <div class="text-box">
                <h2>クレジットカードを <br>登録してください</h2>
                <p>※キャストと合流するまで <br>料金は発生しません</p>
            </div>
            <a class="close_button tc-verification-link" href="#">クレジットカードを登録する</a>
        </div>
    </div>
</div>

<div class="modal_wrap">
    <input id="buypoint-alert" type="checkbox">
    <div class="modal_overlay">
        <label for="buypoint-alert" class="modal_trigger" id="buypoint-alert-label"></label>
        <div class="modal_content modal_content-btn3">
            <div class="content-in">
                <h2 id="buypoint-alert-content"></h2>
            </div>
        </div>
    </div>
</div>

@endsection
@section('web.content')
<div class="list_wrap">
    <div class="current_point">
        <div class="point_inner"><h2 class="">現在の保有ポイント</h2></div>
        <div class="point-border"></div>
        <div class="current_point-box point_inner">
            <figure><img src="{{ asset('assets/web/images/gl4/coin.svg') }}" alt="画像"></figure>
            <p id="total_point">{{ number_format($user->point) }}</p>
            <input type="hidden" id="current_point" value="{{ $user->point }}">
            <input type="hidden" id="is_multi_payment_method" value="{{ $user->is_multi_payment_method }}">
            <input type="hidden" id="path_select_payment_method" value="{{ URL::previous() }}">
        </div>
    </div>
    <div class="point_list_wrap">
        <div class="list_item">
            <div class="item_left">
                <h3 class="point_amount">1,000P</h3>
            </div>
            <div class="item_right">
                <div class="btn-m"><a href="javascript:void(0)" onclick="buyPoint(1000)">¥1,100</a></div>
            </div>
        </div>
        <div class="list_item">
            <div class="item_left">
                <h3 class="point_amount">3,000P</h3>
            </div>
            <div class="item_right">
                <div class="btn-m"><a href="javascript:void(0)" onclick="buyPoint(3000)">¥3,300</a></div>
            </div>
        </div>
        <div class="list_item">
            <div class="item_left">
                <h3 class="point_amount">5,000P</h3>
            </div>
            <div class="item_right">
                <div class="btn-m"><a href="javascript:void(0)" onclick="buyPoint(5000)">¥5,500</a></div>
            </div>
        </div>
        <div class="list_item">
            <div class="item_left">
                <h3 class="point_amount">10,000P</h3>
            </div>
            <div class="item_right">
                <div class="btn-m"><a href="javascript:void(0)" onclick="buyPoint(10000)">¥11,000</a></div>
            </div>
        </div>
        <div class="list_item">
            <div class="item_left">
                <h3 class="point_amount">50,000P</h3>
            </div>
            <div class="item_right">
                <div class="btn-m"><a href="javascript:void(0)" onclick="buyPoint(50000)">¥55,000</a></div>
            </div>
        </div>
        <div class="list_item">
            <div class="item_left">
                <h3 class="point_amount">100,000P</h3>
            </div>
            <div class="item_right">
                <div class="btn-m"><a href="javascript:void(0)" onclick="buyPoint(100000)">¥110,000</a></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('web.extra_js')
    <script>
        $(document).ready(function () {
            var hasCard = '{!! $user->is_card_registered ? 1 : 0 !!}';
            var backUrl = $('#path_select_payment_method').val();

            if (backUrl.match('/purchase/select_payment_methods')) {
                var point = localStorage.getItem("buy_point")

                if (localStorage.getItem("payment_method") && (localStorage.getItem("payment_method") == 2)) {
                    window.location.href = '/payment/transfer?point='+point;
                } else {
                    if (!hasCard) {
                        document.getElementById('popup-require-card').click();
                        return false;
                    }

                    $('#buypoint-popup').click();
                    $('#popup-amount').html(point.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + 'P');
                    $('#point-amount').val(point);
                }
            }

        });

        function buyPoint(point) {
            if (localStorage.getItem("payment_method")) {
                localStorage.removeItem("payment_method");
            }

            localStorage.setItem('buy_point', point);

            return window.location.href = '/purchase/select_payment_methods';
        }
    </script>
@endsection
