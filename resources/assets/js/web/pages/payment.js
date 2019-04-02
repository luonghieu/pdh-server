const helper = require('./helper');
$('#gl3 #payment-failed-popup').on('click', function(event) {
  $('#payment-failed').trigger('click');
});

$('#request-buy-point-btn').on('click', function(e) {
    $('#request-buy-point').trigger('click');
    $('#payment-form').submit();
});

$('#payment-confirm-btn').on('click', function(e) {
  $('#payment-form').submit();
});

$('#payment-submit').on('click', function(e) {
    var checkMultiPaymentMethod = $('#is-multi-payment-method').val();
    if (checkMultiPaymentMethod != true) {
        e.preventDefault();
        if (orderTotalPoint > guestTotalPoint) {
            const missingPoint = orderTotalPoint - guestTotalPoint;
            $('#request-buy-point').trigger('click');
            $('#request-buy-point-modal-title').html(missingPoint.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + 'Pが足りません');
        } else {
            $('#payment-confirm').trigger('click');
        }
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
    var checkMultiPaymentMethod = $('#is-multi-payment-method').val();

    window.axios.get('/api/v1/guest/points_used')
        .then(function(response) {
            if (checkMultiPaymentMethod == true && (response.data.data > guestTotalPoint)) {
                window.location.href = '/payment/transfer?point='+response.data.data;
            } else {
                const url = $(this).attr('action');

                window.axios.post(url).then(response => {
                    const message = helper.getResponseMessage(response.data.message);
                    $('#alert-payment-content').html(message);
                    $('#alert-payment-label').trigger('click');
                    document.getElementById('payment-completed-gtm').click();
                    setTimeout(() => {
                        window.location.href = '/mypage';
                    }, 2000);
                }).catch(err => {
                    $('#payment-failed').trigger('click');
                });
            }

        }).catch(function(error) {
        console.log(error);
        });

});
