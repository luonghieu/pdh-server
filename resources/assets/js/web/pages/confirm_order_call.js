$(document).ready(function(){
  const helper = require('./helper');
  if($('#btn-confirm-orders').length) {
    if(localStorage.getItem("order_call")){
      var orderCall = JSON.parse(localStorage.getItem("order_call"));

      if (orderCall.select_area) {
        var area = orderCall.select_area;

        if('その他' == area) {
          area = orderCall.text_area;
        }

        $('.word18').text(area);
      } else {
        window.location.href = '/mypage';
      }

      if (orderCall.countIds) {
        $('.cast-numbers-call').text(orderCall.class_name + ' ' + orderCall.countIds + '名');
      } else {
        window.location.href = '/mypage';
      }

      if(orderCall.current_time_set) {
        var currentTime = orderCall.current_time_set;

        if (orderCall.current_duration) {
          var duration = orderCall.current_duration;

          if('other_duration' == duration) {
            duration = orderCall.select_duration;
          }

          $('.duration-call').text(duration + '時間')
        }

        if ('other_time' == currentTime) {
          var currentDate = orderCall.current_date;
          var time = orderCall.current_time;

          var day = currentDate.split('-');

          var year = day[0];
          var month = day[1];
          var date = day[2];

          $('.time-detail-call').text(year +'年' + month + '月' + date + '日' + ' ' + time);
        } else {
          now = new Date();

          utc = now.getTime() + (now.getTimezoneOffset() * 60000);
          nd = new Date(utc + (3600000*9));
          var day = helper.add_minutes(nd, currentTime);

          var year = day.getFullYear();

          var date = day.getDate();
          if(date<10) {
            date = '0'+date;
          }

          var month = day.getMonth() +1;
          if(month<10) {
            month = '0'+month;
          }

          var hour = day.getHours();
            if(hour<10) {
            hour = '0' +hour;
          }

          var minute = day.getMinutes();
          if(minute<10) {
            minute = '0' +minute;
          }

          var currentDate = year + '-' + month + '-' + date;
          var time = hour + ':' + minute;

          $('.time-detail-call').text(currentTime + '分後');

        }

        // if (orderCall.arrIds) {
        //   var castIds = orderCall.arrIds;
        //   var countIds = castIds.length;
        //   castIds = castIds.toString();
        //   if (countIds) {
        //     if (countIds == orderCall.countIds) {
        //       var type = 1;
        //     } else {
        //       var type = 4;
        //     }
        //   } else {
        //     var type = 2;
        //   }      
        // } else {
        //   var castIds = '';
        //   type = 2;
        // }

        var castIds = '';
        var type = 2;
        // var input = {
        //   nominee_ids : castIds,
        // };

        // window.axios.post('/api/v1/casts/list_casts',input)
        // .then(function(response) {
        //   var data = response.data['data'];
        //   $('.total-nominated-call').text(data.length)
        //   if (data.length) {

        //     data.forEach(function (val) {
        //       var avatars = val.avatars;
        //       if(avatars.length) {
        //         if (avatars[0].thumbnail) {
        //           $('.details-list-box__pic').append('<li> <img src= "' + avatars[0].thumbnail + '" class="img-detail-cast" /> </li>');
        //         } else {
        //           $('.details-list-box__pic').append('<li> <img src= "' + avatarsDefault + '" class="img-detail-cast" /> </li>');
        //         }
        //       } else {
        //         $('.details-list-box__pic').append('<li> <img src= "' + avatarsDefault + '" class="img-detail-cast" /> </li>');
        //       }
        //     })

        //     $('.img-detail-cast').error(function(){
        //       $(this).attr("src", avatarsDefault);
        //     });
        //   }

        // }).catch(function(error) {
        //   console.log(error);
        //   if (error.response.status == 401) {
        //     window.location = '/login';
        //   }
        // });

        var params = {
          date : currentDate,
          start_time : time,
          type :type,
          class_id : orderCall.cast_class ,
          duration : duration,
          total_cast :orderCall.countIds,
          nominee_ids : castIds,
        };

        window.axios.post('/api/v1/orders/price',params)
        .then(function(response) {
          var tempPoint = response.data['data'];
          $('#temp_point_order_call').val(tempPoint);

          tempPoint = parseInt(tempPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('.details-total__marks').text(tempPoint +'P~');
        }).catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login';
          }
        });
      } else {
        window.location.href = '/mypage';
      }

      if (orderCall.tags) {
        var tags = orderCall.tags;
        (tags).forEach(function (data) {
          $('.details-info-list').append('<li class="details-info-list_kibun">'+data+'</li>');
        })
      }

      $('.sb-form-orders').on('click',function(){
        $('.modal-confirm').css('display', 'none');
        $('#btn-confirm-orders').prop('disabled', true);

        document.getElementById('confirm-order-submit').click();

        if(orderCall.tags) {
          tags = orderCall.tags.toString();
        } else {
          tags = '';
        }

        if('other_time' != currentTime) {
          now = new Date();

          utc = now.getTime() + (now.getTimezoneOffset() * 60000);
          nd = new Date(utc + (3600000*9));
          var day = helper.add_minutes(nd, currentTime);

          var year = day.getFullYear();

          var date = day.getDate();
          if(date<10) {
            date = '0'+date;
          }

          var month = day.getMonth() +1;
          if(month<10) {
            month = '0'+month;
          }

          var hour = day.getHours();
            if(hour<10) {
            hour = '0' +hour;
          }

          var minute = day.getMinutes();
          if(minute<10) {
            minute = '0' +minute;
          }

          currentDate = year + '-' + month + '-' + date;
          time = hour + ':' + minute;
        }

        var params = {
          prefecture_id : 13,
          address : area,
          class_id : orderCall.cast_class ,
          duration : duration,
          nominee_ids : castIds,
          date : currentDate,
          start_time : time,
          type :type,
          total_cast :orderCall.countIds,
          tags : tags,
          temp_point : $('#temp_point_order_call').val()
        };

        window.axios.post('/api/v1/orders', params)
        .then(function(response) {
          $('#orders').prop('checked',false);
          $('#order-done').prop('checked',true);
        })
        .catch(function(error) {
          $('.modal-confirm').css('display', 'inline-block');
          $('#btn-confirm-orders').prop('disabled', false);
          $('#order-call-popup').prop('checked',false);
            if (error.response.status == 401) {
              window.location = '/login';
            } else {
              if(error.response.status == 404) {
                $('#md-require-card').prop('checked',true);
              } else {
                if(error.response.status == 406) {
                  $('.card-expired h2').text('');
                  var content = '予約日までにクレジットカードの <br> 1有効期限が切れます  <br> <br> 予約を完了するには  <br> カード情報を更新してください';
                  $('.card-expired p').html(content);
                  $('.lable-register-card').text('クレジットカード情報を更新する');
                  $('#md-require-card').prop('checked',true);
                } else {
                  if (error.response.status == 400) {
                    var title = '開始時間は現在時刻から30分以降の時間を選択してください';
                  }

                  if(error.response.status == 422) {
                  var title = 'この操作は実行できません';
                  }

                  if(error.response.status == 500) {
                  var title = 'サーバーエラーが発生しました';
                  }

                  if(error.response.status == 409) {
                    var title = 'すでに予約があります';
                  }

                  $('.show-message-order-call h2').html(title);

                  $('#order-call-popup').prop('checked',true);
                }
              }
            }
        })
      });
    } else {
      window.location.href = '/mypage';
    }
  }
})