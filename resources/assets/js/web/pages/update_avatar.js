$(document).ready(function() {
  $('#update-avatar').submit(function(e) {
    e.preventDefault();
    var id = $('#image-id').val();
    var params = {
      id: $('#image-id').val(),
    };

    window.axios.post('/api/v1/avatars/' + id, params)
      .then(function(response) {
        console.log(response);
      })
      .catch(function(error) {
        if (error.response.data.error) {
          var errors = error.response.data.error;
          console.log(errors.image[0]);

          $('#image').html(`<span>` + errors.image[0] + `</span>`);
        }
      });
  });
});
