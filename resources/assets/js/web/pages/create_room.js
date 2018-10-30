$(document).ready(function() {
  $('#create-room').on('click', function(e) {
    var _this = $(this);
    var params = {
      user_id: _this.attr('data-user-id'),
    }

    window.axios.post('/api/v1/rooms', params)
      .then(function(response) {
        id = response.data.data.id;
        window.location = '/message/' + id;
      })
      .catch(function(error) {
        if (error.response.status == 401) {
          window.location = '/login/line';
        }
      });
  });
});
