$(document).ready(function() {
  $('#favorite-cast-detail').on('click', function(e) {
    var _this = $(this);
    id = _this.attr('data-user-id');
    is_favorited = _this.attr('data-is-favorited');

    window.axios.post('/api/v1/favorites/' + id)
      .then(function(response) {
        if (is_favorited == '0') {
          $('#favorite-cast-detail').html(`<img id="like" src="/assets/web/images/common/like.svg"><span class="text-color">イイネ済</span>`)
        } else {
          $('#favorite-cast-detail').html(`<img src="/assets/web/images/common/unlike.svg"><span class="text-color">イイネ</span>`)
        }

        _this.attr('data-is-favorited', is_favorited == 1 ? 0 : 1);
      })
      .catch(function(error) {
        if (error.response.status == 401) {
          window.location = '/login/line';
        }
      });
  });
});
