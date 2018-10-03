$(document).ready(function() {
  $("#search-box").val(null);
  var userId = $('#auth').val();

  window.Echo.private('user.'+userId)
  .listen('MessageCreated', (e) => {
    var roomId = e.message.room_id;
    var message = '';

    if (e.message.message !== "") {
      message = e.message.message;
    } else {
      message = e.message.user.nickname+'さんが写真を送信しました';
    }

    var roomId = e.message.room_id;
    var unreadCount = $('#room_' + roomId).data('unread');

    $('#balloon_' + roomId).removeClass("balloon");
    $('#balloon_' + roomId).addClass("notyfi-msg");
    unreadCount = unreadCount + 1;

    $('#room_' + roomId).data('unread', unreadCount);
    $('#room_' + roomId).text(unreadCount);
    $('#latest-message_' + roomId).text(message);
  });

  $('.search-box').keydown(function(event) {
    if(event.keyCode == 13) {
      event.preventDefault();
    }
  });

  $('.search-box').keyup(function(event) {
    var keywork = $('.search-box').val();
    axios.get('api/v1/rooms',{
      'params': {
        nickname: keywork,
        response_type: 'html',
      }
    })
    .then(function (response) {
      $('#list-room').html(response.data);
    })
    .catch(function (error) {
      console.log(error);
    });
  });
});

let roomLoading = false;

$(window).scroll(function() {
    if($(document).height() - ($(window).scrollTop() + $(window).height()) <= 10  ) {
      var nextpage = $(".next-page:last").attr("data-url");
      if(!nextpage) {
        return false;
      }
        if (!roomLoading) {
          roomLoading = true;

          axios.get(nextpage,{
            'params': {
              response_type: 'html'
            }
          })
          .then(function (response) {
            $('#list-room').append(response['data']);

            roomLoading = false;
          })
          .catch(function (error) {
            console.log(error);
          });
        }
    }
});
