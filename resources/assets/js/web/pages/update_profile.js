$(document).ready(function() {
  var userAgent = navigator.userAgent || navigator.vendor || window.opera;

  // iOS detection
  if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
    $('#gm1.gm1-edit .phone.arrow:after').css({
      '-webkit-transform': 'translateY(-60%) translateX(-2px) rotate(404deg)',
      '-ms-transform': 'translateY(-60%) translateX(-2px) rotate(404deg)',
      'transform': 'translateY(-60%) translateX(-2px) rotate(404deg)',
    });
  }

  var maxYear = $('#date-of-birth').attr('max');

  $("#date-of-birth").on("change", function() {
    var today = (new Date()) / 1000;
    var date = (new Date($(this).val())) / 1000;

    var range = (today - date) / (24 * 60 * 60 * 365);
    var age = Math.floor(range);

    $('#age').html(age + '歳 ');

    this.setAttribute(
      "data-date",
      moment(this.value, "YYYY-MM-DD")
      .format( this.getAttribute("data-date-format") )
    )
  }).trigger("change");

  $('.hidden').hide();

  $('#update-profile').submit(function(e) {
    e.preventDefault();
  }).validate({
    rules: {
      nickname: {
        required: true,
        maxlength: 20,
      },
      date_of_birth: {
        required: true,
        max: maxYear,
      },
      intro: {
        maxlength: 30,
      },
      description: {
        maxlength: 1000,
      },
    },
    messages: {
      date_of_birth: {
        required: "生年月日は、必ず指定してください。",
        max: '年齢は20歳以上で入力してください。',
      },
      nickname: {
        required: "ニックネームは、必ず指定してください。",
        maxlength: "20文字以内で入力してください。",
      },
      intro: {
        maxlength: "30文字以内で入力してください。",
      },
      description: {
        maxlength: "1000文字以内で入力してください。",
      },
    },

    submitHandler: function(form) {
      if ($('.css-img #valid').length < 1) {
        if (document.getElementById('upload').files.length <= 0) {
          $('.image-error').html('imageには、画像を指定してください。');
          return false;
        }
      }

      var params = {
        nickname: $('#nickname').val(),
        date_of_birth: $('#date-of-birth').val(),
        gender: $('#gender').val(),
        intro: $('#intro').val(),
        description: $('#description').val(),
        prefecture_id: $('#prefecture-id').val(),
        cost: $('#cost').val(),
        salary_id: $('#salary-id').val(),
        height: $('#height').val(),
        body_type_id: $('#body-type-id').val(),
        hometown_id: $('#hometown-id').val(),
        job_id: $('#job-id').val(),
        drink_volume_type: $('#drink-volume-type').val(),
        smoking_type: $('#smoking-type').val(),
        siblings_type: $('#siblings-type').val(),
        cohabitant_type: $('#cohabitant-type').val(),
      };

      const name = $('#name').val();
      const day = $('#day').val();
      const img = $('#img').val();

      Object.keys(params).forEach(function(key) {
        if (!params[key]) {
          delete params[key];
        }
      });

      $('.help-block').each(function() {
        $(this).html('');
      });

      window.axios.post('/api/v1/auth/update', params)
        .then(function(response) {
          if (!name || !day) {
            window.sessionStorage.setItem('popup_mypage', 'プロフィール登録が完了しました');
            window.location.href = '/mypage';
          } else {
            window.sessionStorage.setItem('popup_profile', 'プロフィール登録が完了しました')
            window.location.href = '/profile';
          }
        })
        .catch(function(error) {
          if (error.response.status == 401) {
            window.location = '/login/line';
          }

          if (error.response.data.error) {
            var errors = error.response.data.error;

            Object.keys(errors).forEach(function(field) {
              $(`[data-field="${field}"].help-block`).html(errors[field][0]);
            });
          };
        });
    }
  });

  $('body').on('change', "#prefecture-id",function(){
    $(this).css('color', 'black');
  })

  $('body').on('change', "#date-of-birth",function(){
    $('.show-message-error').css('display','none');
  })

  $('#update-date-of-birth').submit(function(e) {
    e.preventDefault();
  }).validate({
    rules: {
      date_of_birth: {
        required: true,
        max: maxYear,
      },

      prefecture_id: {
        required: true,
      },
    },

    messages: {
      date_of_birth: {
        required: "生年月日を入力してください",
        max: '20歳未満の方は、ご利用いただけません',
      },
      prefecture_id: {
        required: "ご利用エリアを入力してください",
      },
    },

    submitHandler: function(form) {
      var param = {
        date_of_birth: $('#date-of-birth').val(),
        prefecture_id: $('#prefecture-id').val(),
        invite_code: $('#input_invite-code').val(),
      };

      if (!param['date_of_birth']) {
        delete param['date_of_birth'];
      }

      if (!param['prefecture_id']) {
        delete param['prefecture_id'];
      }

      $('.help-block').each(function() {
        $(this).html('');
      });

      window.axios.post('/api/v1/auth/update', param)
        .then(function(response) {
          window.sessionStorage.setItem('popup_mypage', '登録が完了しました');
          window.location.href = '/mypage';
        })
        .catch(function(error) {
          if (error.response.status == 401) {
            window.location = '/login/line';
          }

          if(error.response.status == 404) {
            $('#invite-code-error').prop('checked',true);
          }

          if(error.response.status == 409) {
            $('#date-of-birth-error').prop('checked',true);
          }
        });
    }
  });
});
