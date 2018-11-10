$(document).ready(function() {
  const helper = require('./helper');
  $('#cookie-link').on('click', function (e) {
    var checked = $('#input-cookie:checked').length;

    if(checked) {
      if(!helper.getCookie('popup')) {
        helper.setCookie('popup',1);
      }
    }
  });

  if($('#cookie-popup').length) {
    if(!helper.getCookie('popup')) {
      $('#cookie-popup').click();
    }else {
        $('.modal_overlay-popup').css('display','none');
    }
  }

});
