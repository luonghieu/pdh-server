$(document).ready(function () {
  // Like/unlike timeline in timeline detail
  $('body').on('click', '#heart-timeline', function(e) {
    var _this = $(this);
    var id = _this.attr('data-timeline-id');
    total_favorites = _this.attr('data-total-favorites-timeline');
    is_favorited_timeline = _this.attr('data-is-favorited-timeline');

    var nickname = $('#nickname').val();
    var age = $('#age').val();
    var avatar = $('#avatar').val();
    var userId = $('#timeline-user-id').val();

    window.axios.post('/api/v1/timelines/' + id + '/favorites')
      .then(function(response) {
        var total = parseInt(total_favorites);
        if (is_favorited_timeline == 0) {
          var total = total + 1;
          _this.html(`<img class="init-cursor" src="/assets/web/images/common/like.svg">`);
          _this.attr('data-total-favorites-timeline', total);

          var html = `<div class="timeline-like-item" id="user-` + userId + `">
              <div class="timeline-like-item__profile">
                <img src="` + avatar + `" alt="">
              </div>
              <div class="timeline-like-item__info">
                <p>` + nickname + `</p>
                <p>` + age + `歳</p>
              </div>
            </div>`;

          $('.js-add-favorite').before(html);
        } else {
          var total = total - 1;
          _this.html(`<img class="init-cursor" src="/assets/web/images/common/unlike.svg">`)
          _this.attr('data-total-favorites-timeline', total);

          $('#user-' + userId).remove();
        }

        $('#total-favorites').text(total);
        _this.attr('data-is-favorited-timeline', is_favorited_timeline == 1 ? 0 : 1);
      })
      .catch(function(error) {
        if (error.response.status == 401) {
          window.location = '/login/line';
        }
      });
  });
});
