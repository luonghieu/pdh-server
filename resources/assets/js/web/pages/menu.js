$(document).ready(function () {
  $('.logout-web').click(function (event) {
    var MenuAPI = $("#menu").data('mmenu');

    MenuAPI.close();

    setTimeout(function () {
      $('#confirm-logout').trigger('click');
    }, 500)
  });
});
