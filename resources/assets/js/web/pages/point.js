$('#buypoint-confirm').on('click', function() {
    const currentPoint = $('#current_point').val();
    const pointAmount = $('#point-amount').val();
    $('#buypoint-popup').trigger('click');
    window.axios.post('/api/v1/points', {amount: pointAmount}).then(function(response) {
        const newTotalPoint = Number(currentPoint) + Number(pointAmount);
        $('#current_point').val(newTotalPoint);
        $('#total_point').html(newTotalPoint.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        $('#buypoint-alert-content').html('ポイント購入完了しました！');
        $('#buypoint-alert-label').trigger('click');
        $('#buypoint-alert-label').addClass('auto-popup');
        setTimeout(() => {
            if ($('#buypoint-alert-label').hasClass('auto-popup')) {
                $('#buypoint-alert-label').trigger('click');
            }
        }, 5000);
    }).catch(err => {
        $('#popup-require-card').trigger('click');
    });
});
$('#buypoint-alert-label').on('click', function() {
    $('#buypoint-alert-label').removeClass('auto-popup');
});