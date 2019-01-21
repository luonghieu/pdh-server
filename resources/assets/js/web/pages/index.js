$(document).ready(function() {
  const helper = require('./helper');
  $('#campaign').trigger('click');

  $('#close-campaign').on('click', function () {
    helper.setCookie('campaign', true);
  });

  $('.modal-close-campaign').on('click', function () {
    helper.setCookie('campaign', true);
  });

  if (helper.getCookie('campaign') == 'true') {
    $('#hide-campaign').hide();
  }

  $('.tc-verification-link').on('click', function () {
    $('#telecom-credit-form').submit();
  });
});
