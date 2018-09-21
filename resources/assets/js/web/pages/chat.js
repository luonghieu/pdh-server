$(document).ready(function() {
  var roomId = $("#room-id").val();
  window.Echo.private('room.'+roomId)
    .listen('MessageCreated', (e) => {
      var message = e.message.message;
      var createdAt = e.message.created_at;
      var avatar = e.message.user.avatars[0]['path'];
      var date = new Date(createdAt);
      var time = date.getHours()+':'+date.getMinutes();
      if(e.message.type == 2 || (e.message.type == 1 && e.message.system_type == 1)) {
        $("#message-box").append(`
          <div class="msg-left msg-wrap">
          <figure>
            <a href="https://www.yahoo.co.jp/"><img src="`+avatar+`"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
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
           <a href="https://www.yahoo.co.jp/"><img src="`+avatar+`"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
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

  $("#send-message").click(function() {
    var formData = new FormData();
    var content = $("#content").val();
    var files = $('#image').prop('files');
    var filesCamera = $('#image-camera').prop('files');
   var currentDate = new Date();
    var time = currentDate.getHours()+':'+currentDate.getMinutes();

    if (files.length > 0) {
        formData.append('image', files[0]);
        formData.append('type', 3);
    }
    if(filesCamera.length > 0){
        formData.append('image', filesCamera[0]);
        formData.append('type', 3);
      }
    if (files.length == 0 && filesCamera.length == 0) {
      formData.append('message', content);
      formData.append('type', 2);
    }

    axios.post(`/api/v1/rooms/${roomId}/messages`, formData)
    .then(function (response) {
      var avatar = response.data.data.user.avatars[0]['path'];
      if(response.data.data.type == 2) {
        $("#message-box").append(`
          <div class="msg-right msg-wrap">
          <figure>
            <a href="https://www.yahoo.co.jp/"><img src="`+avatar+`"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
          </figure>
          <div class="msg-right-text">
            <div class="text">
              <div class="text-wrapper">
                <p>`+content+`</p>
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
            <a href="https://www.yahoo.co.jp/"><img src="`+avatar+`"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
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
  });

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
});
