const helper = require('./helper');
$('#request-buy-point-btn').on('click', function(e) {
    $('#request-buy-point').trigger('click');
    $('#payment-form').submit();
});

$('#payment-confirm-btn').on('click', function(e) {
    $('#payment-form').submit();
});

$('#payment-submit').on('click', function(e) {
    e.preventDefault();
    if (orderTotalPoint > guestTotalPoint) {
        const missingPoint = orderTotalPoint - guestTotalPoint;
        $('#request-buy-point').trigger('click');
        $('#request-buy-point-modal-title').html(missingPoint.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + 'Pが足りません');
    } else {
        $('#payment-confirm').trigger('click');
    }
});

$('#request-update-point-btn').on('click', function(e) {
    const url = `api/v1/guest/orders/${orderId}/payment_requests`;
    window.axios.patch(url).then(response => {
        $('#alert-payment-content').html('修正依頼しました');
        $('#request-update-point').trigger('click');
        $('#alert-payment-label').trigger('click');
        
        setTimeout(() => {
            window.location.href = '/mypage';
        }, 2000);
    }).catch(err => {
        $('#request-update-point').trigger('click');
        $('#payment-failed').trigger('click');
    });
});

$('#payment-form').on('submit', function (e) {
   e.preventDefault();
   const url = $(this).attr('action');

    window.axios.post(url).then(response => {
        const message = helper.getResponseMessage(response.data.message);
        $('#alert-payment-content').html(message);
        $('#alert-payment-label').trigger('click');
        setTimeout(() => {
            window.location.href = '/mypage';
        }, 2000);
    }).catch(err => {
        $('#payment-failed').trigger('click');
    });
});