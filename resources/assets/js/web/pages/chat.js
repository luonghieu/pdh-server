$(document).ready(function() {
  var userAgent = navigator.userAgent || navigator.vendor || window.opera;

  // iOS detection
  if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
    $('#chat .msg').css('height','65%');
    $('#chat #message-box').css('height','100%');
    $('#chat .msg-input').css({
      'position' : 'absolute',
      'bottom':'0',
      'margin-bottom' : '-30px'
    });
  }

  function isValidImage(url, callback) {
    var image = new Image();
    image.src = url;
    image.onload = function () {
      callback(true);
    };

    image.onerror = function () {
      callback(false);
    };
  }

  var roomId = $("#room-id").val();
  var orderId = $("#order-id").val();
  var currentDate = new Date();
  var time = currentDate.getHours()+':'+currentDate.getMinutes();
  window.Echo.private('room.'+roomId)
    .listen('MessageCreated', (e) => {
      var message = e.message.message;
      var reg_exUrl = /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[\-;:&=\+\$,\w]+@)?[A-Za-z0-9\.\-]+|(?:www\.|[\-;:&=\+\$,\w]+@)[A-Za-z0-9\.\-]+)((?:\/[\+~%\/\.\w\-_]*)?\??(?:[\-\+=&;%@\.\w_]*)#?(?:[\.\!\/\\\w]*))?)/;
      var find = message.match(reg_exUrl);
      if (find) {
        message = message.replace(find[0], '<a href="'+find[0]+'" target="_blank">'+find[0]+'</a>');
      }

      var createdAt = e.message.created_at;
      var pattern = /([0-9]{2}):([0-9]{2}):/g;
      var result = pattern.exec(createdAt);
      var time = result[1]+':'+result[2];
      var avatar = e.message.user.avatars[0]['path'];

      isValidImage(avatar, function (isValid) {
        if (isValid) {
          avatar = avatar;
        } else {
          avatar = '/assets/web/images/gm1/ic_default_avatar@3x.png'
        }

        if(e.message.type == 2 || (e.message.type == 1 && e.message.system_type == 1) || e.message.type == 4) {
          $("#message-box").append(`
            <div class="msg-left msg-wrap">
            <figure>
              <a href=""><img src="`+avatar+`"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
            </figure>
            <div class="msg-left-text">
              <div class="text">
                <div class="text-wrapper">
                  <p>`+message.replace(/\n/g, "<br />")+`</p>
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
          $('.pic p img').promise().done(function(){
            $('img').load(function(){
              //android detection
              if (/android/i.test(userAgent)) {
                $(document).scrollTop($('#message-box')[0].scrollHeight);
              }


              // iOS detection
              if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                setTimeout(function(){
                  $('#message-box').scrollTop($('#message-box')[0].scrollHeight);
                });
              }
            });
          });
        }

        if(e.message.type == 1 && e.message.system_type == 2) {
          $("#message-box").append(`
            <div class="msg-alert">
              <h3><span>`+time+`</span><br>`+message.replace(/\n/g, "<br />")+`</h3>
            </div>
         `);
        }
      });

      //android detection
      if (/android/i.test(userAgent)) {
        $(document).scrollTop($('#message-box')[0].scrollHeight);
      }


      // iOS detection
      if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
        setTimeout(function(){
          $('#message-box').scrollTop($('#message-box')[0].scrollHeight);
        });
      }
    });

  $("#send-message").click(function(event) {
    var formData = new FormData();

    $('#content').focus();

    $(this).prop('disabled', true);

    var content = $("#content").val();
    if($.trim(content)) {

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

  $("#content").on('keydown', function(){
    $("#send-message").prop('disabled', false);
  });

  $("#image-camera").change(function(event) {
    var formData = new FormData();
    var filesCamera = $('#image-camera').prop('files');

    if(filesCamera.length > 0){
      formData.append('image', filesCamera[0]);
      formData.append('type', 3);
    }

    sendMessage(formData);
  });

  $("#image").change(function(event) {
    var formData = new FormData();
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
      isValidImage(avatar, function (isValid) {
        if (isValid) {
          avatar = avatar;
        } else {
          avatar = '/assets/web/images/gm1/ic_default_avatar@3x.png'
        }

        if(response.data.data.type == 2) {
          var message = response.data.data.message;
          var reg_exUrl = /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[\-;:&=\+\$,\w]+@)?[A-Za-z0-9\.\-]+|(?:www\.|[\-;:&=\+\$,\w]+@)[A-Za-z0-9\.\-]+)((?:\/[\+~%\/\.\w\-_]*)?\??(?:[\-\+=&;%@\.\w_]*)#?(?:[\.\!\/\\\w]*))?)/;
          var find = message.match(reg_exUrl);

          if (find) {
            message = message.replace(find[0], '<a href="'+find[0]+'" target="_blank">'+find[0]+'</a>');
          }

          $("#message-box").append(`
            <div class="msg-right msg-wrap">
            <figure>
              <a href=""><img src="`+avatar+`"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
            </figure>
            <div class="msg-right-text">
              <div class="text">
                <div class="text-wrapper">
                  <p>`+message.replace(/\n/g, "<br />")+`</p>
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

          $('.pic p img').promise().done(function(){
            $('img').load(function(){
              //android detection
              if (/android/i.test(userAgent)) {
                $(document).scrollTop($('#message-box')[0].scrollHeight);
              }


              // iOS detection
              if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                setTimeout(function(){
                  $('#message-box').scrollTop($('#message-box')[0].scrollHeight);
                });
              }
            });
          });
        }
      });

      $('body').on('load', '.pic p img', function(){
        $('#message-box').scrollTop($('#message-box')[0].scrollHeight);
      });

      //android detection
      if (/android/i.test(userAgent)) {
        $(document).scrollTop($('#message-box')[0].scrollHeight);
      }


      // iOS detection
      if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
        setTimeout(function(){
          $('#message-box').scrollTop($('#message-box')[0].scrollHeight);
        });
      }

      $("#content").val(null);
      $("#content").css('height','30px');
      $("#image-camera").val(null);
      $("#image").val(null);
    })
    .catch(function (error) {
      if (error.response.data.message) {
        var messageError = error.response.data.message;
      }
      if (error.response.data.error) {
        var messageError = error.response.data.error.image[0];
      }
      $('.alert-image-oversize .content-in h2').text(messageError);
      $('#alert-image-oversize').trigger('click');

      setTimeout(() => {
        $('.wrap-alert-image-oversize').css('display', 'none');
      }, 2000);
    });
  }

  $(document).on('scroll', function(e) {
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
    var text1 = content.substring(0, n);
    var text3 = content.substring(n+text2.length, content.length);
    var orderId = $(this).data('id');
    var result = text2.link('/history/'+ orderId);
    var newText = text1 + result + text3;
    $(this).html(newText.replace(/\n/g, "<br />"));
  } else {
    $(this).html(content.replace(/\n/g, "<br />"));
  }
});

jQuery(document).ready(function($) {
  if (window.history && window.history.pushState && $('#rooms').length) {
    window.history.pushState(null, null, null);

    $(window).on('popstate', function() {
      window.location.reload();
      window.location.href = "/message";
    });
  }
});
