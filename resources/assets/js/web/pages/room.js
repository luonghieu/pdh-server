$(document).ready(function() {
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
