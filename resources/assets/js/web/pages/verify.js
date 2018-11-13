$(document).ready(function() {
  var isVerify = null;
  var oldPhone = $('#old-phone').val();

  $('#profile-verify-code').submit(function(e) {
    e.preventDefault();
  }).validate({
    rules: {
      phone: {
        required: true,
        number: true,
        minlength: 10,
        maxlength: 11,
      },
    },
    messages: {
      phone: {
        required: '正しい電話番号を入力してください',
        number: '正しい電話番号を入力してください',
        minlength: '正しい電話番号を入力してください',
        maxlength: '正しい電話番号を入力してください',
      },
    },

    submitHandler: function(form) {
      var param = {
        phone: $('#phone').val(),
      };

      if (oldPhone == param['phone']) {
        $('.error-phone').html('入力された電話番号はすでに登録されています');
        $('.error-phone').css('display', '');
        return false;
      }

      $('.error-phone').each(function() {
        $(this).html('');
      });

      window.axios.post('/api/v1/auth/verify_code', param)
        .then(function(response) {
          window.location.href = '/verify/code';
        })
        .catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login/line';
          }

          if (error.response.data.error) {
            var errors = error.response.data.error;

            Object.keys(errors).forEach(function(field) {
              $(`[data-field="${field}"].help-block`).html(errors[field][0]);
              $(`[data-field="${field}"].help-block`).css('display', '');
            });
          };
        });
    }
  });

  $('#code-number-1').focus();

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
    if ($('#send-number').attr('class') == 'number-phone-verify-correct') {
      var formData = new FormData();
      var phone = $('#phone-number-verify').val();
      formData.append('phone', phone);

      window.axios.post(`/api/v1/auth/verify_code`, formData)
      .then(function (response) {
        window.location = '/verify/code';
      })
      .catch(function (error) {
        if(error.response.status == 500) {
          var err = 'この操作は実行できません';
        } else {
          if (error.response.data.error.phone) {
            var err = error.response.data.error.phone[0];
          }
        }
        $('.phone-number-incorrect h2').text(err);
        $('#triggerPhoneNumberIncorrect').trigger('click');
      });
    }
  });

  $('#resend-code').click(function() {
    window.axios.post(`/api/v1/auth/resend_code`)
    .then(function (response) {
      window.location = '/verify/code';
    })
    .catch(function (error) {
      console.log(error);
    });
  });

  $('#code-number-1').on('keyup', function(event) {
    if (event.keyCode != 8 && event.keyCode != 32) {
      $('#code-number-2').focus();
    }
  });

  $('#code-number-2').on('keyup', function(event) {
    if (event.keyCode != 8 && event.keyCode != 32) {
      $('#code-number-3').focus();
    }
  });

  $('#code-number-3').on('keyup', function(event) {
    if (event.keyCode != 8 && event.keyCode != 32) {
      $('#code-number-4').focus();
    }
  });

  $('#code-number-4').on('keyup', function() {
    var isVerify = $('#is-verify').val();

    var formData = new FormData();
    var code = $('#code-number-1').val()+$('#code-number-2').val()+$('#code-number-3').val()+$('#code-number-4').val();
    if (code.length == 4) {
      formData.append('code', code);

      window.axios.post(`/api/v1/auth/verify`, formData)
      .then(function (response) {
        $('#verify-success').trigger('click');

        if (isVerify != 0) {
          setTimeout(() => {
            window.location.href = '/profile';
          }, 3000);
        } else {
          setTimeout(() => {
            window.location.href = '/mypage';
          }, 3000);
        }
      })
      .catch(function (error) {
        $('#code-number-4').blur();
        $('#accept-resend-code').css({
          display: 'block',
        });
        $('#triggerVerifyIncorrect').trigger('click');
      });
    }
  });

  $('#code-verify .enter-number input').blur(function(event) {
    $('#code-verify footer').css({
      display: 'none',
    });
  });

  $('#code-number-1').on('click', function(event) {
    $('#code-verify footer').css({
      display: 'none',
    });
  });

  $('#alert-code-wrong').click(function() {
    $('#triggerAcceptResenCode').trigger('click');
  });

  $('#request-resend-code').click(function(event) {
    $('#triggerAcceptResenCode').trigger('click');
  });

  $('#deny-resend').click(function(event) {
    $('#accept-resend-code').css({
      display: 'none',
    });
    location.reload();
  });
});
