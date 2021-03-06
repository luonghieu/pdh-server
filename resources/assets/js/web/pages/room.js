let roomLoading = false;
$(document).ready(function() {
  $("#search-box").val(null);
  var userId = $('#auth').val();
  window.Echo.private('user.'+userId).listen('MessageCreated', (e) => {
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

    if (unreadCount > 99) {
      unreadCount = '99+';
    }

    $('#room_' + roomId).text(unreadCount);
    $('#latest-message_' + roomId).text(message);

    var count = 0;
    $('.msg').each(function(index, val) {
      var id = $(this).data('id');
      if (id == roomId) {
        count++;
      }
    });

    if (count > 0) {
      $('#list-room').prepend($('#msg_'+roomId));
    } else {
      window.location.reload();
    }

  });

  $('.search-box').keydown(function(event) {
    if(event.keyCode == 13) {
      event.preventDefault();
    }
  });

  $('.search-box').keyup(function(event) {
    var keywork = $('.search-box').val();
    axios.get('api/v1/rooms/list_room',{
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
