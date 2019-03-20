$(document).ready(function() {
  var referrer = document.referrer;

  if (referrer) {
    $('.btn-back.header-item a').attr('href', referrer);
  } else {
    $('.btn-back.header-item a').attr('href', 'cheers://back');
  }

  $('#year-select')
  .find('option')
  .each(function (index, value) {
    $(value).text($(value).text() + '年');
  });
});
