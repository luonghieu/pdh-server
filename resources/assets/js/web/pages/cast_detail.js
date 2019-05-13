const helper = require('./helper');
$(document).ready(function() {
    function dayOfWeek() {
        return ['日', '月', '火', '水', '木', '金', '土'];
    }

    var checkApp = {
        isAppleDevice : function() {
            if (navigator.userAgent.match(/(iPhone|iPod|iPad)/) != null) {
                return true;
            }
            return false;
        }
    };

  $('#favorite-cast-detail').on('click', function(e) {
    var _this = $(this);
    id = _this.attr('data-user-id');
    is_favorited = _this.attr('data-is-favorited');

    window.axios.post('/api/v1/favorites/' + id)
      .then(function(response) {
        if (is_favorited == '0') {
          $('#favorite-cast-detail').html(`<img src="/assets/web/images/common/like.svg"><span class="text-color">イイネ済</span>`)
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

  $('.btn-order-nominee').on('click', function () {
      var castId = $('#cast-id').val();
      var dateShift = $(this).data('shift');
      var newdate = dateShift.split('-');
      var date = newdate[2];
      var month = newdate[1];
      var year = newdate[0];

      if (checkApp.isAppleDevice()) {
          var dateFolowDevice = new Date(month +'/' + date +'/'+ year);
      } else {
          var dateFolowDevice = new Date(year +'-' + month +'-'+ date);
      }

      var getDayOfWeek = dateFolowDevice.getDay();
      var dayOfWeekString = dayOfWeek()[getDayOfWeek];

      var paramShift = {
          date : date,
          month : month,
          year : year,
          dayOfWeekString : dayOfWeekString,
      }

      helper.updateLocalStorageKey('shifts', paramShift, castId);

      window.location = '/nominate?id='+castId;
  });

  // Like/unlike timeline
  $('.heart-timeline').on('click', function(e) {
    var id = $(this).attr('data-timeline-id');
    var _this = $('#heart-timeline-' + id);
    total_favorites = _this.attr('data-total-favorites-timeline');
    is_favorited_timeline = _this.attr('data-is-favorited-timeline');

    window.axios.post('/api/v1/timelines/' + id + '/favorites')
      .then(function(response) {
        var total = parseInt(total_favorites);
        if (is_favorited_timeline == 0) {
          var total = total + 1;
          _this.html(`<img src="/assets/web/images/common/like.svg">`);
          _this.attr('data-total-favorites-timeline', total);
        } else {
          var total = total - 1;
          _this.html(`<img src="/assets/web/images/common/unlike.svg">`)
          _this.attr('data-total-favorites-timeline', total);
        }

        $('#total-favorites-' + id).text(total);
        _this.attr('data-is-favorited-timeline', is_favorited_timeline == 1 ? 0 : 1);
      })
      .catch(function(error) {
        if (error.response.status == 401) {
          window.location = '/login/line';
        }
      });
  });
});


