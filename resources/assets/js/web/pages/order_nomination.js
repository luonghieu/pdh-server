$(document).ready(function(){
  //checkbox
  $(".checked-order").on("change",function(event){
    if ($(this).is(':checked')) {
      var time = $("input:radio[name='time_join_nomination']:checked").val();
      var area = $("input:radio[name='nomination_area']:checked").val();
      var duration = $("input:radio[name='time_set_nomination']:checked").val();
      var date = $('.sp-date').text();
      var cancel=$("input:checkbox[name='confrim_order_nomination']:checked").length;
      var otherArea = $("input:text[name='other_area_nomination']").val();

      if((!area || (area=='その他' && !otherArea)) || !time ||
       (!duration || duration<1) || (time=='other_time' && !date)) {

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

  //duration
  var timeSet = $("input:radio[name='time_set_nomination']");
  timeSet.on("change",function(){
    var time = $("input:radio[name='time_join_nomination']:checked").val();
    var duration = $("input:radio[name='time_set_nomination']:checked").val();
    var date = $('.sp-date').text();
    var cancel=$("input:checkbox[name='confrim_order_nomination']:checked").length;

    var cost = $('.cost-order').val();
    var totalPoint=cost*(duration*6)/3;

    cost = parseInt(cost).toLocaleString(undefined,{ minimumFractionDigits: 0 });

    $('.reservation-total__text').text('内訳：'+cost+ '(キャストP/30分)✖'+(duration*6)/3+'時間');

    if(time){
      var currentDate = new Date();
      var year = currentDate.getFullYear();
      if ((time=='other_time')) {
        var month = $('.select-month').val();

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
    console.log(params);
      window.axios.post('/api/v1/orders/price',params)
        .then(function(response) {
          totalPoint = response.data['data'];
          totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('.total-point').text(totalPoint +'P~');
        }).catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login/line';
          }
        });
      } else {
        totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        $('.total-point').text(totalPoint +'P~');
      }
  })

  $('.select-duration').on("change",function(){
    var time = $("input:radio[name='time_join_nomination']:checked").val();
    var duration = $('.select-duration option:selected').val();
    var cost = $('.cost-order').val();
    var totalPoint=cost*(duration*6)/3;

    if(time) {
      var currentDate = new Date();
      var year = currentDate.getFullYear();
      if ((time=='other_time')) {
        var month = $('.select-month').val();

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
        }).catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login/line';
          }
        });
      } else {
        totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        $('.total-point').text(totalPoint +'P~');
      }


    cost = parseInt(cost).toLocaleString(undefined,{ minimumFractionDigits: 0 });

    $('.reservation-total__text').text('内訳：'+cost+ '(キャストP/30分)✖'+(duration*6)/3+'時間')
  })

  $('.choose-time').on("click",function(){
    if ($("input:radio[name='time_set_nomination']:checked").length) {
      var selectDuration = $('.select-duration option:selected').val();
      var checkedDuration = $("input:radio[name='time_set_nomination']:checked").val();
      var time = $("input:radio[name='time_join_nomination']:checked").val();
      var cost = $('.cost-order').val();

      duration = selectDuration;

      if(checkedDuration) {
        duration = checkedDuration;
      }

      var currentDate = new Date();
      var year = currentDate.getFullYear();
      if ((time=='other_time')) {
        var month = $('.select-month').val();

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
        }).catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login/line';
          }
        });
    }
  })

  $("input:radio[name='time_join_nomination']").on("change",function(){
    if ($("input:radio[name='time_set_nomination']:checked").length) {
      var selectDuration = $('.select-duration option:selected').val();
      var checkedDuration = $("input:radio[name='time_set_nomination']:checked").val();
      var time = $("input:radio[name='time_join_nomination']:checked").val();
      var cost = $('.cost-order').val();

      duration = selectDuration;

      if(checkedDuration) {
        duration = checkedDuration;
      }

      var currentDate = new Date();
      var year = currentDate.getFullYear();
      if ((time=='other_time')) {
        var month = $('.select-month').val();

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
      $('#create-nomination-form').submit();
    }
});
});


