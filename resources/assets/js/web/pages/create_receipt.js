$(document).ready(function() {
  $('#create-receipt').submit(function(e) {
    e.preventDefault();
    var params = {
      name: $('#name').val(),
      content: $('#content').val(),
    };

    $('.help-block').each(function () {
      $(this).html('');
    })
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

          Object.keys(errors).forEach(function (field) {
            $(`[data-field="${field}"].help-block`).html(errors[field][0]);
          });
        }
      });
  });
});
