$(document).ready(function () {
    $('#credit-method').on('click', function () {
        localStorage.setItem('payment_method', 1);
        window.location.href = '/purchase';
    });

    $('#transfer-method').on('click', function () {
        localStorage.setItem('payment_method', 2);
        window.location.href = '/purchase';
    });
});