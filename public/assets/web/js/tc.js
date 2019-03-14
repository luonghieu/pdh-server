$(document).ready(function() {
  var referrer = document.referrer;

  if (referrer) {
    $('.btn-back.header-item a').attr('href', referrer);
  } else {
    $('.btn-back.header-item a').attr('href', 'cheers://back');
  }
});
