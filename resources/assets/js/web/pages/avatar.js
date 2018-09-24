$(document).ready(function() {
  $('.img').on('click', function() {
    id = $(this).attr('id');

    $('#set-default-avatar').on('click', function(e) {
      window.axios.patch('/api/v1/avatars/' + id)
        .then(function(response) {
          window.location = '/profile';
        })
        .catch(function(error) {
          if (error.response.status == 401) {
            window.location = '/login/line';
          }
        });
    });

    $('#delete-avatar').on('click', function(e) {
      window.axios.delete('/api/v1/avatars/' + id)
        .then(function(response) {
          window.location = '/profile';
        })
        .catch(function(error) {
          if (error.response.status == 401) {
            window.location = '/login/line';
          }
        });
    });

    $('#update-avatar').on('click', function(e) {
      $('#upload-btn').trigger('click');
    });

    $('#upload-btn').on('change', function(e) {
      var data = new FormData();
      data.append('image', document.getElementById('upload-btn').files[0]);

      window.axios.post('/api/v1/avatars/' + id, data)
        .then(function(response) {
          window.location = '/profile';
        })
        .catch(function(error) {
          if (error.response.status == 401) {
            window.location = '/login/line';
          }
        });
     });
    });
});
