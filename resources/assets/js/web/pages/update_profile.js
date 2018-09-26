$(document).ready(function() {
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
      },
      intro: {
        maxlength: 30,
      },
      description: {
        maxlength: 1000,
      }
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
      }
    },

    submitHandler: function(form) {
      if ($('.profile-photo__list ul li.profile-photo__item').length < 1) {
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

      $('.help-block').each(function() {
        $(this).html('');
      });

      window.axios.post('/api/v1/auth/update', params)
        .then(function(response) {
          window.location = '/profile';
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
