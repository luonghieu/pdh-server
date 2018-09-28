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
    localStorage.setItem("text_area", $(this).val());
  });

  //area
  var area = $("input:radio[name='nomination_area']");
  area.on("change",function(){
    var areaNomination = $("input:radio[name='nomination_area']:checked").val();
    localStorage.setItem("select_area", areaNomination);
  })

  //

  //duration
  var timeSet = $("input:radio[name='time_set_nomination']");
  timeSet.on("change",function(){
    var time = $("input:radio[name='time_join_nomination']:checked").val();

    var duration = $("input:radio[name='time_set_nomination']:checked").val();
    localStorage.setItem("current_duration", duration);

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
          totalPoint = response.data['data'];
          totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          localStorage.setItem("current_total_point", totalPoint);
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
    localStorage.setItem("select_duration", duration);

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
          localStorage.setItem("current_total_point", totalPoint);
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
      var duration = $("input:radio[name='time_set_nomination']:checked").val();
      if('other_time_set' == duration) {
        duration = $('.select-duration option:selected').val();
      }

      var time = $("input:radio[name='time_join_nomination']:checked").val();
      var cost = $('.cost-order').val();

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
      localStorage.setItem("current_date", day);
      localStorage.setItem("current_month", month);

      var date = year+'-'+month+'-'+day;
      var time = hour+':'+minute;
      localStorage.setItem("current_time", time);
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

          localStorage.setItem("current_date", day);
          localStorage.setItem("current_month", month);

          var date = year+'-'+month+'-'+day;
          var time = hour+':'+minute;
          localStorage.setItem("current_time", time);
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
          localStorage.setItem("current_total_point", totalPoint);
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
      var duration = $("input:radio[name='time_set_nomination']:checked").val();
      if('other_time_set' == duration) {
        duration = $('.select-duration option:selected').val();
      }

      var time = $("input:radio[name='time_join_nomination']:checked").val();
      var cost = $('.cost-order').val();
      localStorage.setItem("current_time_set", time);
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
          localStorage.setItem("current_total_point", totalPoint);
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

  if(localStorage.getItem("current_date")){
      $('.sp-date').text(localStorage.getItem("current_date") +'日');
  }else {
  }

  if(localStorage.getItem("current_month")){
      $('.sp-month').text(localStorage.getItem("current_month") +'月');
  }

  if(localStorage.getItem("current_time")){
      $('.sp-time').text(localStorage.getItem("current_time"));
  }

  if(localStorage.getItem("current_total_point")){
      $('.total-point').text(localStorage.getItem("current_total_point") +'P~');
  }

  if(localStorage.getItem("other_time_set")){
      $('.time-input-nomination').css('display','flex');
      $("input:radio[name='time_set_nomination']:checked").parent().toggleClass("active");
  }

  //area
  if(localStorage.getItem("select_area")){

   if('その他'== localStorage.getItem("select_area")){
      $('.area-nomination').css('display', 'flex')
      $("input:text[name='other_area_nomination']").val(localStorage.getItem("text_area"));
    }

    const inputArea = $(".input-area");
    $.each(inputArea,function(index,val){
      if (val.value == localStorage.getItem("select_area")) {
        $(this).prop('checked', true);
        $(this).parent().addClass('active');
      }
    })
  }

  //duration
  var cost = $('.cost-order').val();
  if(localStorage.getItem("current_duration")){
      if('other_time_set' == localStorage.getItem("current_duration")) {
        var chooseDuration = localStorage.getItem("select_duration");
        $('.time-input-nomination').css('display','flex');
      } else {
        var chooseDuration = localStorage.getItem("current_duration");
      }

      $('.reservation-total__text').text('内訳：'+cost+ '(キャストP/30分)✖'+chooseDuration+'時間');

      const inputDuration = $(".input-duration");

      $.each(inputDuration,function(index,val){
        if (val.value == localStorage.getItem("current_duration")) {
          $(this).prop('checked', true);
          $(this).parent().addClass('active');
        }
      })

      if(localStorage.getItem("select_duration")) {
        const inputDuration = $('select[name=sl_duration_nominition] option');
        $.each(inputDuration,function(index,val){
          if(val.value == localStorage.getItem("select_duration")) {
            $(this).prop('selected',true);
          }
        })
      }
  }

  if(localStorage.getItem("other_time_set")){
      $('.time-input-nomination').css('display','flex');
      $("input:radio[name='time_set_nomination']:checked").parent().toggleClass("active");
  }


//current_time_set

if(localStorage.getItem("current_time_set")){
  if('other_time'== localStorage.getItem("current_time_set")){
    $('.date-input-nomination').css('display', 'flex')
  }

  const inputTimeSet = $(".input-time-join");
  $.each(inputTimeSet,function(index,val){
    if (val.value == localStorage.getItem("current_time_set")) {
      $(this).prop('checked', true);
      $(this).parent().addClass('active');
    }
  })
}

  if($("label").hasClass("status-code")){
    $('.status-code').click();
  }

})
