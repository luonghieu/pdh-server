$(document).ready(function(){
  const helper = require('./helper');

  $(".checked-order").on("change",function(event){
    if ($(this).is(':checked')) {
      var time = $("input:radio[name='time_join_nomination']:checked").val();
      var area = $("input:radio[name='nomination_area']:checked").val();
      var duration = $("input:radio[name='time_set_nomination']:checked").val();
      var date = $('.sp-date').text();
      var cancel=$("input:checkbox[name='confrim_order_nomination']:checked").length;
      var otherArea = $("input:text[name='other_area_nomination']").val();

      if((!area || (area=='その他' && !otherArea)) || !time ||
       (!duration || (duration<1 && 'other_time_set' != duration)) || (time=='other_time' && !date)) {

        $('#confirm-orders-nomination').addClass("disable");
        $(this).prop('checked', false);
        $('#confirm-orders-nomination').prop('disabled', true);
        $('#sp-cancel').addClass("sp-disable");
      } else {
        $('#confirm-orders-nomination').removeClass('disable');
        $(this).prop('checked', true);
        $('#confirm-orders-nomination').prop('disabled', false);
        $('#sp-cancel').removeClass('sp-disable');
      }
    } else {
        $(this).prop('checked', false);
        $('#confirm-orders-nomination').addClass("disable");
        $('#confirm-orders-nomination').prop('disabled', true);
        $('#sp-cancel').addClass("sp-disable");
    }
  });

  //textArea
  $("input:text[name='other_area_nomination']").on('change', function(e) {
    var params = {
      text_area: $(this).val(),
    };
    helper.updateLocalStorageValue('order_params', params);
  });

  //area
  var area = $("input:radio[name='nomination_area']");
  area.on("change",function(){
    var areaNomination = $("input:radio[name='nomination_area']:checked").val();

    if('その他'== areaNomination){
      if(localStorage.getItem("order_params")){
        var orderParams = JSON.parse(localStorage.getItem("order_params"));
      }

      if(orderParams.text_area){
        $("input:text[name='other_area_nomination']").val(orderParams.text_area);
      }
    }

    var params = {
      select_area: areaNomination,
    };

    helper.updateLocalStorageValue('order_params', params);
  })

  //duration
  var timeSet = $("input:radio[name='time_set_nomination']");
  timeSet.on("change",function(){
    var time = $("input:radio[name='time_join_nomination']:checked").val();

    var duration = $("input:radio[name='time_set_nomination']:checked").val();

    var params = {
      current_duration: duration,
    };

    helper.updateLocalStorageValue('order_params', params);

    if('other_time_set' == duration) {
      duration = $('.select-duration option:selected').val();
    }

    var date = $('.sp-date').text();
    var cancel=$("input:checkbox[name='confrim_order_nomination']:checked").length;

    var cost = $('.cost-order').val();
    var totalPoint=cost*(duration*6)/3;

    cost = parseInt(cost).toLocaleString(undefined,{ minimumFractionDigits: 0 });

    $('.reservation-total__text').text('内訳：'+cost+ '(キャストP/30分)✖'+(duration)+'時間');

    if(time){
      var currentDate = new Date();
      var year = currentDate.getFullYear();
      if ((time == 'other_time')) {
        var month = $('.select-month').val();
        var checkMonth = currentDate.getMonth();

        if (month <= checkMonth) {
          var year = currentDate.getFullYear() + 1;
        }

        if(month<10) {
          month = '0'+month;
        }

        var day = $('.select-date').val();

        if(day<10) {
          day = '0'+day;
        }

        var hour = $('.select-hour').val();

        if(hour<10) {
          hour = '0'+hour;
        }

        var minute = $('.select-minute').val();
        if(minute<10) {
          minute = '0'+minute;
        }

        var date = year+'-'+month+'-'+day;
        var time = hour+':'+minute;
      } else{
          utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
          nd = new Date(utc + (3600000*9));

          var add_minutes =  function (dt, minutes) {
            return new Date(dt.getTime() + minutes*60000);
          }

          var selectDate = add_minutes(nd,time);

          if (add_minutes(nd, 30) > selectDate) {
            selectDate = add_minutes(nd, 30);
          }

          var day = selectDate.getDate();
          if(day<10) {
            day = '0'+day;
          }

          var month = selectDate.getMonth() +1;
          if(month<10) {
            month = '0'+month;
          }
          var hour = selectDate.getHours();
          if(hour<10) {
            hour = '0'+hour;
          }

          var minute = selectDate.getMinutes();
          if(minute<10) {
            minute = '0'+minute;
          }
          var date = year+'-'+month+'-'+day;
          var time = hour+':'+minute;
      }

      $castId = $('.cast-id').val();
      var params = {
        date : date,
        start_time : time,
        type :3,
        duration :duration,
        total_cast :1,
        nominee_ids : $castId
      };

      window.axios.post('/api/v1/orders/price',params)
        .then(function(response) {
          totalPoint = response.data['data'];
          totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('.total-point').text(totalPoint +'P~');

          var params = {
            current_total_point: totalPoint,
          };

          helper.updateLocalStorageValue('order_params', params);
        }).catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login/line';
          }
        });
      } else {
        totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        $('.total-point').text(totalPoint +'P~');

        var params = {
            current_total_point: totalPoint,
          };

        helper.updateLocalStorageValue('order_params', params);
      }
  })

  $('.select-duration').on("change",function(){
    var time = $("input:radio[name='time_join_nomination']:checked").val();
    var duration = $('.select-duration option:selected').val();

    var params = {
        select_duration: duration,
      };

    helper.updateLocalStorageValue('order_params', params);

    var cost = $('.cost-order').val();
    var totalPoint=cost*(duration*6)/3;
    if(time) {
      var currentDate = new Date();
      var year = currentDate.getFullYear();

      if (time == 'other_time') {
        var month = $('.select-month').val();
        var checkMonth = currentDate.getMonth();

        if (month <= checkMonth) {
          var year = currentDate.getFullYear() + 1;
        }

        if(month<10) {
          month = '0'+month;
        }

        var day = $('.select-date').val();

        if(day<10) {
          day = '0'+day;
        }

        var hour = $('.select-hour').val();

        if(hour<10) {
          hour = '0'+hour;
        }

        var minute = $('.select-minute').val();
        if(minute<10) {
          minute = '0'+minute;
        }

        var date = year+'-'+month+'-'+day;
        var time = hour+':'+minute;
      } else{
          utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
          nd = new Date(utc + (3600000*9));

          var add_minutes =  function (dt, minutes) {
            return new Date(dt.getTime() + minutes*60000);
          }
          var selectDate = add_minutes(nd,time);

          if (add_minutes(nd, 30) > selectDate) {
            selectDate = add_minutes(nd, 30);
          }

          var day = selectDate.getDate();
          if(day<10) {
            day = '0'+day;
          }

          var month = selectDate.getMonth() +1;
          if(month<10) {
            month = '0'+month;
          }
          var hour = selectDate.getHours();
          if(hour<10) {
            hour = '0'+hour;
          }

          var minute = selectDate.getMinutes();
          if(minute<10) {
            minute = '0'+minute;
          }
          var date = year+'-'+month+'-'+day;
          var time = hour+':'+minute;

      }

      $castId = $('.cast-id').val();
      var params = {
        date : date,
        start_time : time,
        type :3,
        duration :duration,
        total_cast :1,
        nominee_ids : $castId
      };

      window.axios.post('/api/v1/orders/price',params)
        .then(function(response) {
          totalPoint = response.data['data']
          totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('.total-point').text(totalPoint +'P~');

          var params = {
              current_total_point: totalPoint,
            };

          helper.updateLocalStorageValue('order_params', params);
        }).catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login/line';
          }
        });
      } else {
        totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        $('.total-point').text(totalPoint +'P~');

        var params = {
            current_total_point: totalPoint,
          };

        helper.updateLocalStorageValue('order_params', params);
      }

    cost = parseInt(cost).toLocaleString(undefined,{ minimumFractionDigits: 0 });

    $('.reservation-total__text').text('内訳：'+cost+ '(キャストP/30分)✖'+(duration)+'時間')
  })

  $('.choose-time').on("click",function(){
    var cost = $('.cost-order').val();
    var time = $("input:radio[name='time_join_nomination']:checked").val();

      var currentDate = new Date();
      var year = currentDate.getFullYear();
      if ((time == 'other_time')) {
        var month = $('.select-month').val();
        var checkMonth = currentDate.getMonth();

        if (month <= checkMonth) {
          var year = currentDate.getFullYear() + 1;
        }

        if(month<10) {
          month = '0'+month;
        }

        var day = $('.select-date').val();

        if(day<10) {
          day = '0'+day;
        }

        var hour = $('.select-hour').val();

        if(hour<10) {
          hour = '0'+hour;
        }

        var minute = $('.select-minute').val();
        if(minute<10) {
          minute = '0'+minute;
        }

      var updateOtherTime = {
          current_month: month,
          current_date: day,
          current_hour: hour,
          current_minute: minute,
        };

      helper.updateLocalStorageValue('order_params', updateOtherTime);

      var date = year+'-'+month+'-'+day;
      var time = hour+':'+minute;
      } else{
        utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
        nd = new Date(utc + (3600000*9));

        var add_minutes =  function (dt, minutes) {
          return new Date(dt.getTime() + minutes*60000);
        }

        var selectDate = add_minutes(nd,time);

        if (add_minutes(nd, 30) > selectDate) {
          selectDate = add_minutes(nd, 30);
        }

        var day = selectDate.getDate();
        if(day<10) {
          day = '0'+day;
        }

        var month = selectDate.getMonth() +1;
        if(month<10) {
          month = '0'+month;
        }
        var hour = selectDate.getHours();
        if(hour<10) {
          hour = '0'+hour;
        }

        var minute = selectDate.getMinutes();
        if(minute<10) {
          minute = '0'+minute;
        }

        var date = year+'-'+month+'-'+day;
        var time = hour+':'+minute;

        var updateSelectedDate = {
          current_date: day,
          current_month: month,
          current_time: time,
        };

        helper.updateLocalStorageValue('order_params', updateSelectedDate);
    }

    if ($("input:radio[name='time_set_nomination']:checked").length) {
      var duration = $("input:radio[name='time_set_nomination']:checked").val();

      if('other_time_set' == duration) {
        duration = $('.select-duration option:selected').val();
      }

      $castId = $('.cast-id').val();
      var params = {
        date : date,
        start_time : time,
        type :3,
        duration :duration,
        total_cast :1,
        nominee_ids : $castId
      };

      window.axios.post('/api/v1/orders/price',params)
        .then(function(response) {
          var totalPoint=cost*(duration*6)/3;
          totalPoint = response.data['data'];
          totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('.total-point').text(totalPoint +'P~');

          var params = {
            current_total_point: totalPoint,
          };

          helper.updateLocalStorageValue('order_params', params);
        }).catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login/line';
          }
      });
    }
  })

//timejoin
  $("input:radio[name='time_join_nomination']").on("change",function(){
    var time = $("input:radio[name='time_join_nomination']:checked").val();
    var duration = $("input:radio[name='time_set_nomination']:checked").val();

    var updateTime = {
          current_time_set: time,
        };

    helper.updateLocalStorageValue('order_params', updateTime);

    if('other_time' == time) {
      if(localStorage.getItem("order_params")){
        var orderParams = JSON.parse(localStorage.getItem("order_params"));
      }

      if(orderParams){
        if('other_time'== orderParams.current_time_set){
          if(orderParams.current_month){
           const inputMonth = $('select[name=sl_month_nomination] option');
            $.each(inputMonth,function(index,val){
              if(val.value == orderParams.current_month) {
                $(this).prop('selected',true);
              }
            })

            $('.month-nomination').text(orderParams.current_month +'月');
          }

          if(orderParams.current_date){
            const inputDate = $('select[name=sl_date_nomination] option');
            $.each(inputDate,function(index,val){
              if(val.value == orderParams.current_date) {
                $(this).prop('selected',true);
              }
            })
            $('.date-nomination').text(orderParams.current_date +'日');
          }

          if(orderParams.current_hour) {
            const inputHour = $('select[name=sl_hour_nomination] option');
            $.each(inputHour,function(index,val){
              if(val.value == orderParams.current_hour) {
                $(this).prop('selected',true);
              }
            })

            const inputMinute = $('select[name=sl_minute_nomination] option');
            $.each(inputMinute,function(index,val){
              if(val.value == orderParams.current_minute) {
                $(this).prop('selected',true);
              }
            })

            var currentTime =orderParams.current_hour + ":" + orderParams.current_minute;
          }

          $('.time-nomination').text(currentTime);
        }
      }
    }

    if ($("input:radio[name='time_set_nomination']:checked").length) {
      if('other_time_set' == duration) {
        duration = $('.select-duration option:selected').val();
      }

      var cost = $('.cost-order').val();

      var currentDate = new Date();
      var year = currentDate.getFullYear();
      if (time == 'other_time') {
        var month = $('.select-month').val();
        var checkMonth = currentDate.getMonth();

        if (month <= checkMonth) {
          var year = currentDate.getFullYear() + 1;
        }

        if(month<10) {
          month = '0'+month;
        }

        var day = $('.select-date').val();

        if(day<10) {
          day = '0'+day;
        }

        var hour = $('.select-hour').val();

        if(hour<10) {
          hour = '0'+hour;
        }

        var minute = $('.select-minute').val();
        if(minute<10) {
          minute = '0'+minute;
        }

        var date = year+'-'+month+'-'+day;
        var time = hour+':'+minute;
      } else{
          utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
          nd = new Date(utc + (3600000*9));

          var add_minutes =  function (dt, minutes) {
            return new Date(dt.getTime() + minutes*60000);
          }
          var selectDate = add_minutes(nd,time);

          if (add_minutes(nd, 30) > selectDate) {
            selectDate = add_minutes(nd, 30);
          }

          var day = selectDate.getDate();
          if(day<10) {
            day = '0'+day;
          }

          var month = selectDate.getMonth() +1;
          if(month<10) {
            month = '0'+month;
          }
          var hour = selectDate.getHours();
          if(hour<10) {
            hour = '0'+hour;
          }

          var minute = selectDate.getMinutes();
          if(minute<10) {
            minute = '0'+minute;
          }

          var date = year+'-'+month+'-'+day;
          var time = hour+':'+minute;
      }

      $castId = $('.cast-id').val();
      var params = {
        date : date,
        start_time : time,
        type :3,
        duration :duration,
        total_cast :1,
        nominee_ids : $castId
      };

      window.axios.post('/api/v1/orders/price',params)
        .then(function(response) {
          var totalPoint=cost*(duration*6)/3;
          totalPoint = response.data['data'];
          totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('.total-point').text(totalPoint +'P~');

          var params = {
            current_total_point: totalPoint,
          };

          helper.updateLocalStorageValue('order_params', params);
        }).catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login/line';
          }
        });
    }
  })

  $('#confirm-orders-nomination').on('click',function(){
    $('.lb-orders-nominate').click();
  });

  $('.cf-orders-nominate').on('click',function(){
      if($('#md-require-card').length){
        $('#md-require-card').click();
      }else {
        document.getElementById('confirm-order-nomination-submit').click();
        $('#create-nomination-form').submit();
      }
  });

  if ($('#create-nomination-form').length) {
    if(localStorage.getItem("order_params")){
      var orderParams = JSON.parse(localStorage.getItem("order_params"));
    }

    if(orderParams){
      if(orderParams.current_total_point){
          $('.total-point').text(orderParams.current_total_point +'P~');
      }

        //area
      if(orderParams.select_area){
       if('その他'== orderParams.select_area){
          $('.area-nomination').css('display', 'flex')
          $("input:text[name='other_area_nomination']").val(orderParams.text_area);
        }

        const inputArea = $(".input-area");
        $.each(inputArea,function(index,val){
          if (val.value == orderParams.select_area) {
            $(this).prop('checked', true);
            $(this).parent().addClass('active');
          }
        })
      }

        //duration
      var cost = $('.cost-order').val();
      if(orderParams.current_duration){
        if('other_time_set' == orderParams.current_duration) {
          var chooseDuration = orderParams.select_duration;
          $('.time-input-nomination').css('display','flex');
        } else {
          var chooseDuration = orderParams.current_duration;
        }

        cost = parseInt(cost).toLocaleString(undefined,{ minimumFractionDigits: 0 });

        $('.reservation-total__text').text('内訳：'+cost+ '(キャストP/30分)✖'+chooseDuration+'時間');

        const inputDuration = $(".input-duration");

        $.each(inputDuration,function(index,val){
          if (val.value == orderParams.current_duration) {
            $(this).prop('checked', true);
            $(this).parent().addClass('active');
          }
        })

        if(orderParams.select_duration) {
          const inputDuration = $('select[name=sl_duration_nominition] option');
          $.each(inputDuration,function(index,val){
            if(val.value == orderParams.select_duration) {
              $(this).prop('selected',true);
            }
          })
        }
      }

      //current_time_set
      if(orderParams.current_time_set){
        $(".input-time-join").parent().removeClass('active');
        if('other_time'== orderParams.current_time_set){
          $('.date-input-nomination').css('display', 'flex')

          if(orderParams.current_month){
            var month = parseInt(orderParams.current_month);

            window.axios.post('/api/v1/get_day', {month})
              .then(function(response) {
                var html = '';
                Object.keys(response.data).forEach(function (key) {
                  if(key!='debug') {
                  html +='<option value="'+key+'">'+ response.data[key] +'</option>';
                  }
                })
              $('.select-date').html(html);
              if(orderParams.current_date){
                var currentDate = parseInt(orderParams.current_date);
                const inputDate = $('select[name=sl_date_nomination] option');

                $.each(inputDate,function(index,val){
                  if(val.value == currentDate) {
                    $(this).prop('selected',true);
                  }
                })

                $('.date-nomination').text(currentDate +'日');
              }
              })
              .catch(function (error) {
                console.log(error);
              });

            const inputMonth = $('select[name=sl_month_nomination] option');
            $.each(inputMonth,function(index,val){
              if(val.value == month) {
                $(this).prop('selected',true);
              }
            })

            $('.month-nomination').text(month +'月');
          }

          if(orderParams.current_hour) {
            var currentHour = parseInt(orderParams.current_hour);
            var currentMinute = parseInt(orderParams.current_minute);

            const inputHour = $('select[name=sl_hour_nomination] option');
            $.each(inputHour,function(index,val){
              if(val.value == currentHour) {
                $(this).prop('selected',true);
              }
            })

            const inputMinute = $('select[name=sl_minute_nomination] option');
            $.each(inputMinute,function(index,val){
              if(val.value == currentMinute) {
                $(this).prop('selected',true);
              }
            })

            var currentTime =orderParams.current_hour + ":" + orderParams.current_minute;
          }

          $('.time-nomination').text(currentTime);
        }

        const inputTimeSet = $(".input-time-join");
        $.each(inputTimeSet,function(index,val){
          if (val.value == orderParams.current_time_set) {
            $(this).prop('checked', true);
            $(this).parent().addClass('active');
          }
        })
      }
    }
  }

  if($("label").hasClass("status-code-nomination")){
    $('.status-code-nomination').click();
  }

})
