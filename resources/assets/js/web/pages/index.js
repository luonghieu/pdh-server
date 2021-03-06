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
    if ($(this).attr('data-user-status') == 0) {
        window.location.href = '/mypage';
        return false;
    }
    if (window.App.payment_service == 'telecom_credit') {
      $('#telecom-credit-form').submit();
    } else {
      window.location.href = '/credit_card';
    }
  });

  //save prefecture in my page

  if ($('#prefecture-id-mypage').length) {
    var prefectureId = $('#prefecture-id-mypage').val();

    if (localStorage.getItem("prefecture_id")) {
      $('#prefecture-id-mypage').val(localStorage.getItem("prefecture_id"));
      prefectureId = localStorage.getItem("prefecture_id");

      // display cast working today in mypape
      var params = {
        prefecture_id: prefectureId,
        working_today: 1,
        response_type: 'html'
      };

      window.axios.get('/api/v1/casts', {params})
        .then(function(response) {
          $('.cast-body').html(response['data']);
        })
        .catch(function(error) {
          console.log(error);
        });
    }

    if (!localStorage.getItem("prefecture_id")) {
      localStorage.setItem('prefecture_id', prefectureId);
    }
  }

  $('#prefecture-id-mypage').on('change', function () {
    var prefectureId = $('#prefecture-id-mypage').val();

    localStorage.setItem('prefecture_id', prefectureId);

    // display cast working today in mypape
    var params = {
      prefecture_id: prefectureId,
      working_today: 1,
      response_type: 'html'
    };

    window.axios.get('/api/v1/casts', {params})
      .then(function(response) {
        $('.cast-body').html(response['data']);
      })
      .catch(function(error) {
        console.log(error);
      });
  });
});
