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
  var userAuthId = $("#user-id").val();
  window.Echo.private('room.'+roomId)
    .listen('MessageCreated', (e) => {
      var message = e.message.message;
      var reg_exUrl = /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[\-;:&=\+\$,\w]+@)?[A-Za-z0-9\.\-]+|(?:www\.|[\-;:&=\+\$,\w]+@)[A-Za-z0-9\.\-]+)((?:\/[\+~%\/\.\w\-_]*)?\??(?:[\-\+=&;%@\.\w_]*)#?(?:[\.\!\/\\\w]*))?)/g;
          message = message.replace(reg_exUrl, '<a href="$1" target="_blank">$1</a>')

      var createdAt = e.message.created_at;
      var pattern = /([0-9]{2}):([0-9]{2}):/g;
      var result = pattern.exec(createdAt);
      var time = result[1]+':'+result[2];
      var avatar = e.message.user.avatars[0]['path'];
      var userId = e.message.user.id;
      var classMsg = '';
      if (userAuthId == userId) {
        classMsg = 'msg-right';
      } else {
        classMsg = 'msg-left';
      }

      isValidImage(avatar, function (isValid) {
        if (isValid) {
          avatar = avatar;
        } else {
          avatar = '/assets/web/images/gm1/ic_default_avatar@3x.png'
        }

        if(e.message.type == 2 || (e.message.type == 1 && e.message.system_type == 1) || e.message.type == 4 || e.message.type == 6) {
          $("#message-box").append(`
            <div class="`+classMsg+` msg-wrap">
            <figure>
              <a href=""><img src="`+avatar+`"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
            </figure>
            <div class="`+classMsg+`-text">
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
            <div class="`+classMsg+` msg-wrap">
            <figure>
             <a href=""><img src="`+avatar+`"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
            </figure>
            <div class="`+classMsg+`-text">
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

  var resize = null;

  function readURL(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload =  function(e) {
        $('#my-image').attr('src', e.target.result);
        const oj = {
          enableExif: true,
          viewport: {
            width: $('.wrap-croppie-image').width() - 10,
            height: $('.wrap-croppie-image').width()
          },
          enableOrientation: true,
        };

        if (resize) {
          resize.bind({ url : e.target.result });
          $('#croppie-image-modal').trigger('click')

        } else {
          resize = new Croppie($('#my-image')[0], oj);
          $('#croppie-image-modal').trigger('click')
        }

        $('#crop-image-btn-accept').fadeIn();

      };

      reader.readAsDataURL(input.files[0]);
      $(input.files[0]).val(null);
    }
  }

  $('#crop-image-btn-accept').on('click', function() {
    var formData = new FormData();
    resize.result('canvas').then(function(dataImg) {
      fetch(dataImg)
        .then(res => res.blob())
        .then(blob => {
            formData.append('image', blob);
            formData.append('type', 3);
        });
    });

    setTimeout(() => {
      sendMessage(formData);
    }, 200);
  });

  $("#image-camera").change(function(event) {
    readURL(this);
  });

  $("#image").change(function() {
    readURL(this);
  });

  function sendMessage(formData) {
    axios.post(`/api/v1/rooms/${roomId}/messages`, formData)
    .then(function (response) {
      var currentDate = new Date();
      if (currentDate.getMinutes() < 10) {
        var minute = '0'+currentDate.getMinutes();
      } else {
        var minute = currentDate.getMinutes();
      }
      var time = currentDate.getHours()+':'+minute;
      var avatar = response.data.data.user.avatars[0]['path'];

      isValidImage(avatar, function (isValid) {
        if (isValid) {
          avatar = avatar;
        } else {
          avatar = '/assets/web/images/gm1/ic_default_avatar@3x.png'
        }

        if(response.data.data.type == 2) {
          var message = response.data.data.message;
          var reg_exUrl = /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[\-;:&=\+\$,\w]+@)?[A-Za-z0-9\.\-]+|(?:www\.|[\-;:&=\+\$,\w]+@)[A-Za-z0-9\.\-]+)((?:\/[\+~%\/\.\w\-_]*)?\??(?:[\-\+=&;%@\.\w_]*)#?(?:[\.\!\/\\\w]*))?)/g;
          message = message.replace(reg_exUrl, '<a href="$1" target="_blank">$1</a>')

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
          const firstElement = $('.messages').eq(0);
          $('#message-box').prepend(response.data);
          let prevEle = $('#message-' + firstElement.attr('data-message-id')).prev();
          while(!prevEle.attr('id')) {
              prevEle = prevEle.prev();
          }
          window.location.hash = '#message-' + prevEle.attr('data-message-id');
      })
      .catch(function (error) {
        console.log(error);
      });
    }
  });

  $('.cancel-order').click(function(event) {
    var currentDate = new Date();
    if (currentDate.getMinutes() < 10) {
      var minute = '0'+currentDate.getMinutes();
    } else {
      var minute = currentDate.getMinutes();
    }
    var time = currentDate.getHours()+':'+minute;

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
  var missingPoint = $(this).data('missing-point');
  var text2 = 'コチラ';
  var n = content.search(text2);
  if(n >= 0) {
    var text1 = content.substring(0, n);
    var text3 = content.substring(n+text2.length, content.length);
    var orderId = $(this).data('id');
    if (missingPoint) {
      var result = text2.link('/payment/transfer?point='+ parseInt(missingPoint));
    } else {
      var result = text2.link('/history/'+ orderId);
    }
    var newText = text1 + result + text3;
    $(this).html(newText.replace(/\n/g, "<br />"));
  } else {
    $(this).html(content.replace(/\n/g, "<br />"));
  }
});
