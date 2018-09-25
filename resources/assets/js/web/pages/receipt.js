$(document).ready(function() {
  $('.js-point').on('click', function() {
    var point_id = $(this).attr('point-id');

    $('#form-receipt').submit(function(e) {
        e.preventDefault();
      })
      .validate({
        rules: {
          name: {
            maxlength: 50,
          },
          content: {
            maxlength: 50,
          },
        },
        messages: {
          name: {
            maxlength: "50文字以内で入力してください。",
          },
          content: {
            maxlength: "50文字以内で入力してください。",
          },
        },

        submitHandler: function(form) {
          var params = {
            name: $('#name').val(),
            content: $('#content').val(),
            point_id: point_id,
          };

          $('.help-block').each(function() {
            $(this).html('');
          });

          window.axios.post('/api/v1/receipts', params)
            .then(function(response) {
              console.log(response);
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
              }
            });
        }
      });
  });

});
