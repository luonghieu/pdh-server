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
    }).catch(err => {
        $('#popup-require-card').trigger('click');
    });
});
