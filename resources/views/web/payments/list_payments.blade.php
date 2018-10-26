@foreach($payments['data'] as $payment)
    <div class="list_item list-payment">
        <div class="item_left">
            <span>{{ Carbon\Carbon::parse($payment['transfered_at'])->format('Y年m月d日') }}</span>
        </div>
        <div class="item_right">
            <div class="btn-m">
                <span class="amount">¥{{ number_format($payment['amount']) }}</span>
            </div>
        </div>
    </div>
@endforeach
