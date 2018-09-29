$(document).ready(function() {
  var roomId = $("#room-id").val();
  var orderId = $("#order-id").val();
  var formData = new FormData();
  var currentDate = new Date();
  var time = currentDate.getHours()+':'+currentDate.getMinutes();
  window.Echo.private('room.'+roomId)
    .listen('MessageCreated', (e) => {
      var message = e.message.message;
      var createdAt = e.message.created_at;
      var pattern = /([0-9]{2}):([0-9]{2}):/g;
      var result = pattern.exec(createdAt);
      var time = result[1]+':'+result[2];
      var avatar = e.message.user.avatars[0]['path'];
      if(e.message.type == 2 || (e.message.type == 1 && e.message.system_type == 1) || e.message.type == 4) {
        $("#message-box").append(`
          <div class="msg-left msg-wrap">
          <figure>
            <a href=""><img src="`+avatar+`"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
          </figure>
          <div class="msg-left-text">
            <div class="text">
              <div class="text-wrapper">
                <p>`+message+`</p>
              </div>
            </div>
            <div class="time"><p>`+time+`</p></div>
          </div>
        </div>
        `);
      }

      if(e.message.type == 3) {
        $("#message-box").append(`
          <div class="msg-left msg-wrap">
          <figure>
           <a href=""><img src="`+avatar+`"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
          </figure>
          <div class="msg-left-text">
            <div class="pic">
              <p>
                <img src="`+e.message.image+`"  alt="" title="" class="">
              </p>
           </div>
            <div class="time"><p>`+time+`</p></div>
          </div>
        </div>
        `);
      }

      if(e.message.type == 1 && e.message.system_type == 2) {
        $("#message-box").append(`
          <div class="msg-alert">
            <h3><span>`+time+`</span><br>`+message+`</h3>
          </div>
       `);
      }
    });

  $('#send-message, #content').keydown(function(event) {
    if(event.keyCode == 13) {
      event.preventDefault();
    }
  });

  $("#send-message").click(function(event) {
    $(this).prop('disabled', true);

    if($.trim($("#content").val())) {
      var content = $("#content").val();

      formData.append('message', content);
      formData.append('type', 2);

      sendMessage(formData);
    } else {
      return false;
    }

    event.preventDefault();
  });

  $("#content").click(function(event) {
    $("#send-message").prop('disabled', false);
  });

  $("#image-camera").change(function(event) {
    var filesCamera = $('#image-camera').prop('files');
    if(filesCamera.length > 0){
      formData.append('image', filesCamera[0]);
      formData.append('type', 3);
    }

    sendMessage(formData);
  });

  $("#image").change(function(event) {
    var files = $('#image').prop('files');

    if (files.length > 0) {
      formData.append('image', files[0]);
      formData.append('type', 3);
    }

    sendMessage(formData);
  });

  function sendMessage(formData) {
    axios.post(`/api/v1/rooms/${roomId}/messages`, formData)
    .then(function (response) {
      var avatar = response.data.data.user.avatars[0]['path'];
      if(response.data.data.type == 2) {
        var message = response.data.data.message;
        $("#message-box").append(`
          <div class="msg-right msg-wrap">
          <figure>
            <a href=""><img src="`+avatar+`"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
          </figure>
          <div class="msg-right-text">
            <div class="text">
              <div class="text-wrapper">
                <p>`+message+`</p>
              </div>
            </div>
            <div class="time"><p>`+time+`</p></div>
          </div>
        </div>
        `);
      }
      if(response.data.data.type == 3) {
        $("#message-box").append(`
          <div class="msg-right msg-wrap">
          <figure>
            <a href=""><img src="`+avatar+`"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
          </figure>
          <div class="msg-right-text">
            <div class="pic">
              <p>
              <img src="`+response.data.data.image+`"  alt="" title="" class="">
              </p>
            </div>
            <div class="time"><p>`+time+`</p></div>
          </div>
        </div>
        `);
      }
      $("#content").val(null);
      $("#image-camera").val(null);
      $("#image").val(null);
    })
    .catch(function (error) {
      console.log(error);
    });
  }

  window.addEventListener('scroll', function(e) {
    if(!$(".next-page").attr("data-url")) {
      return false;
    }

    if($(this).scrollTop() == 0) {
      var nextpage = $(".next-page").attr("data-url");

      axios.get(nextpage,{
        'params': {
          response_type: 'html'
        }
      })
      .then(function (response) {
        $('#message-box').prepend(response.data);
      })
      .catch(function (error) {
        console.log(error);
      });
    }
  });

  $('.cancel-order').click(function(event) {
    axios.post(`/api/v1/orders/`+orderId+`/cancel`)
    .then(function (response) {
      var message = response.data.message;

      $("#message-box").append(`
        <div class="msg-alert">
          <h3><span>`+time+`</span><br>`+message+`</h3>
        </div>
      `);

      $(".msg-head").html(`
        <h2><span class="mitei msg-head-ttl">日程未定</span>キャストに予約リクエストしよう！</h2>
      `);
    })
    .catch(function (error) {
      console.log(error);
    });
  });
});

$('.msg-system').each(function(index, val) {
  var content = $(this).text();
  var text2 = 'コチラ';
  var n = content.search(text2);
  if(n >= 0) {
    var text1 = content.substring(0, n).replace(/\n/g, "<br />");
    var text3 = content.substring(n+text2.length, content.length).replace(/\n/g, "<br />");
    var orderId = $(this).data('id');
    var result = text2.link('/history/'+ orderId);
    $(this).html(text1 + result + text3);
  } else {
    $(this).html(content.replace(/\n/g, "<br />"));
  }
});
