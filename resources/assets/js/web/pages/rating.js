const helper = require('./helper');
$('#rating-submit-btn').on('click', function () {
    $('#rating-confirm-label').click();
});

$('#rating-confirm-btn').on('click', function () {
    $('#rating-confirm-label').click();
    $('#rating-create').submit();
});

$('#rating-create').submit(function (e) {
    e.preventDefault();
    const formData = helper.getFormData(this);
    window.axios.post($(this).attr('action'), formData).then((response) => {
        const message = helper.getResponseMessage(response.data.message);
        $('#rating-alert-content').html(message);
        $('#rating-alert').trigger('click');
        setTimeout(() => {
            window.location.href = `/evaluation?order_id=${formData.order_id}`;
        }, 1500);
    }).catch(err => {
        const message = helper.getResponseMessage(err.response.data.error);
        $('#rating-alert-content').html(message);
        $('#rating-alert').trigger('click');
    });
});

$('#rating-comment').on('keyup', function(e) {
    if ($(this).val().length) {
        $('#rating-submit-btn').prop("disabled", false);
    } else {
        $('#rating-submit-btn').prop("disabled", true);
    }
});
