$(document).ready(function() {
  $('#upload-avatar').on('click', function(e) {
    $('#upload').trigger('click');
  });

  $('#upload').on('change', function(e) {
    var data = new FormData();
    data.append('image', document.getElementById('upload').files[0]);

    window.axios.post('/api/v1/avatars', data)
      .then(function(response) {
        window.location = '/profile/edit';
      })
      .catch(function(error) {
        if (error.response.status == 401) {
          window.location = '/login/line';
        }
      });
  });
});
