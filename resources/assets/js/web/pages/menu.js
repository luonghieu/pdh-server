$(document).ready(function(){
  $('.logout-web').click(function(event) {
    var MenuAPI = $("#menu").data('mmenu');
    $('#confirm-logout').trigger('click');
    MenuAPI.close();
  });
});
