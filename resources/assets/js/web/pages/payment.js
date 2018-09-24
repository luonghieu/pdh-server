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
    alert('update payment order: ' + orderId);
});

$('#payment-form').on('submit', function (e) {
   e.preventDefault();
   const url = $(this).attr('action');

    window.axios.post(url).then(response => {
        const message = helper.getResponseMessage(response.data.message);
        $('#alert-payment-content').html(message);
        $('#alert-payment-label').trigger('click');
        setTimeout(() => {
            window.location.href = '/';
        }, 2000);
    }).catch(err => {
        $('#payment-failed').trigger('click');
    });
});