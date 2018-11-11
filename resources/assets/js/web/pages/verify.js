$(document).ready(function() {
  $('#code-number-1').focus();
  $('.js-img').on('click', function() {
    id = $(this).attr('id');
    });

  $('#phone-number-verify').on('keyup', function() {
    var phoneNumber = $('#phone-number-verify').val();
    var phoneNumberLen = phoneNumber.length;
    if (phoneNumberLen == 11 || phoneNumberLen == 10)
      {
        $('#send-number').removeClass('number-phone-verify-wrong');
        $('#send-number').addClass('number-phone-verify-correct');
      } else {
        $('#send-number').removeClass('number-phone-verify-correct');
        $('#send-number').addClass('number-phone-verify-wrong');
      }
  });

  $('#send-number').click(function(event) {
    var formData = new FormData();
    var phone = $('#phone-number-verify').val();
    formData.append('phone', phone);

    axios.post(`/api/v1/auth/verify_code`, formData)
    .then(function (response) {
      window.location = '/verify/code';
    })
    .catch(function (error) {
      var err = error.response.data.error.phone[0];
      $('.phone-number-incorrect h2').text(err);
      $('#triggerPhoneNumberIncorrect').trigger('click');
    });
  });

  $('.resend-code').click(function() {
    axios.post(`/api/v1/auth/resend_code`)
    .then(function (response) {
      window.location = '/verify/code';
    })
    .catch(function (error) {
      console.log(error);
    });
  });

  $('#code-number-1').on('keyup', function() {
    var codeNumber1 = $(this).val();
    var codeNumber1Len = codeNumber1.length;
    if (codeNumber1Len == 1) {
      $('#code-number-2').focus();
    }
  });

  $('#code-number-1').on('keypress', function() {
    $('#code-number-2').focus();
  });

  $('#code-number-2').on('keypress', function() {
    $('#code-number-3').focus();
  });

  $('#code-number-3').on('keypress', function() {
    $('#code-number-4').focus();
  });

  $('#code-number-4').on('keyup', function() {
    var formData = new FormData();
    var code = $('#code-number-1').val()+$('#code-number-2').val()+$('#code-number-3').val()+$('#code-number-4').val();
    if(code.length == 4) {
      formData.append('code', code);

      axios.post(`/api/v1/auth/verify`, formData)
      .then(function (response) {
        $('#verify-success').trigger('click');
        window.location = '/mypage';
      })
      .catch(function (error) {
        $('#triggerVerifyIncorrect').trigger('click');
        console.log(error);
      });
    }
  });

  $('#alert-code-wrong').click(function() {
    $('#triggerAcceptResenCode').trigger('click');
  });
});

