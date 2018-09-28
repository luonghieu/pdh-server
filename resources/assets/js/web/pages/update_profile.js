$(document).ready(function() {
  var maxYear = $('#date-of-birth').attr('max');

  $('#date-of-birth').on('change', function() {
    var today = (new Date()) / 1000;
    var date = (new Date($(this).val())) / 1000;

    var range = (today - date) / (24 * 60 * 60 * 365);
    var age = Math.floor(range);

    $('#age').html(age + '歳 ');
  });

  $('#date-display').on('click', function() {
    $('input[type="date"]').trigger('click');
  });

  $('#date-of-birth').on('change', function() {
    date = new Date($('#date-of-birth').val());

    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();

    if (month < 10) {
      month = '0' + month;
    }

    if (day < 10) {
      day = '0' + day;
    }

    $('#date-display').val(year + '年' + month + '月' + day + '日');
  });

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
      const nickname = $('#nickname').val();
      const day_of_birth = $('#day-of-birth').val();
      const avatars = $('#avatars').val();

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
          $('#profile-popup').trigger('click');
          $('#profile-message h2').html('情報の更新に成功しました。');

          if (!nickname || !day_of_birth || !avatars) {
            setTimeout(() => {
              window.location.href = '/mypage';
            }, 1500);
          }

          setTimeout(() => {
            window.location.href = '/profile';
          }, 1500);

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
});
