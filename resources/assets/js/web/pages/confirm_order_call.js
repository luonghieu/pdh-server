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
      }

      if (orderCall.countIds) {
        $('.cast-numbers-call').text(orderCall.class_name + ' ' + orderCall.countIds + '名');
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

        if (orderCall.arrIds) {
          var castIds = orderCall.arrIds;
          var countIds = castIds.length;
          castIds = castIds.toString();
          if (countIds) {
            if (countIds == orderCall.countIds) {
              var type = 1;
            } else {
              var type = 4;
            }
          } else {
            var type = 2;
          }      
        } else {
          var castIds = '';
          type = 2;
        }

        var input = {
          nominee_ids : castIds,
        };

        window.axios.post('/api/v1/casts/list_casts',input)
        .then(function(response) {
          var data = response.data['data'];
          $('.total-nominated-call').text(data.length)
          if (data.length) {

            var show = '';
            data.forEach(function (val) {
              var avatars = val.avatars;
             if(avatars.length) {
                show +='<li> <img src= "' + avatars[0].thumbnail + '" </li>';
             } else {
                show +='<li> <img src= "' + avatarsDefault + '" </li>';
             }
            })

            $('.details-list-box__pic').html(show);
          }

        }).catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login/line';
          }
        });

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
          totalPoint = response.data['data'];

          var params = {
            total_point: totalPoint,
            type : type,
          };

          helper.updateLocalStorageValue('order_call', params);

          totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('.details-total__marks').text(totalPoint +'P~');
        }).catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login/line';
          }
        });
      }

      if (orderCall.tags) {
        var tags = orderCall.tags;
        var html = '';
        (tags).forEach(function (data) {
          html +='<li class="details-info-list_kibun">'+data+'</li>';
        })
        $('.details-info-list').html(html);
      }


      $('.sb-form-orders').on('click',function(){

        if(orderCall.tags) {
          tags = orderCall.tags.toString();
        } else {
          tags = '';
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
          tag : tags,
          temp_point : orderCall.total_point
        };

        console.log(params)
      });

    } else {
      window.location.href = '/mypage';
    }
  }
})