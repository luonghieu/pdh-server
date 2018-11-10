$(document).ready(function() {
  const helper = require('./helper');
  $('#cookie-link').on('click', function (e) {
    var checked = $('#input-cookie:checked').length;

    if(checked) {
      if(!getCookie('popup')) {
        helper.setCookie('popup',1);
      }
    }
  });
});
