$(document).ready(function() {
  var point_id = null;
  $('body').on('click', '.popup-create-receipt', function() {
    point_id = $(this).attr('point-id');

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
              var img_file = response.data.data.img_file;

              $('#img-pdf').attr('src', img_file);
              $('#img-download').attr('href', img_file);
              $('#send-mail').attr('img-file', img_file);

              $('#popup-create-receipt').trigger('click');
              $('#popup-receipt').trigger('click');

              var btn = '#point-' + response.data.data.point_id + '-btn';
              var label = '<label for="popup-receipt" class="btn-bg popup-receipt" img-file="' + img_file +'">領収書を再発行</label>'
              $(btn).html(label);
              $('#name').val('');
              $('#content').val('');
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

  $('body').on('click', '.popup-receipt', function() {
    var img_file = $(this).attr('img-file');

    $('#img-pdf').attr('src', img_file);
    $('#img-download').attr('href', img_file);
    $('#send-mail').attr('img-file', img_file);
  });

  $('#send-mail').on('click', function() {
    img_file = $(this).attr('img-file') ? $(this).attr('img-file') : $('.popup-receipt').attr('img-file');

    window.location = 'mailto:?body=' + img_file;
  });
});
