$(document).ready(function () {
    $('#credit-method').on('click', function () {
        localStorage.setItem('payment_method', 1);
        window.location.href = '/purchase';
    });

    $('#transfer-method').on('click', function () {
        localStorage.setItem('payment_method', 2);
        var point = localStorage.getItem("buy_point")
        window.location.href = '/payment/transfer?point='+point;
    });
});