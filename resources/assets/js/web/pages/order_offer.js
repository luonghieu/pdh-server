$(document).ready(function(){
  const helper = require('./helper');
  $(".checked-order-offer").on("change",function(event){
    if ($(this).is(':checked')) {
      var area = $("input:radio[name='offer_area']:checked").val();
      var otherArea = $("input:text[name='other_area_offer']").val();

      if((!area || (area=='その他' && !otherArea))) {
        $('#confirm-orders-offer').addClass("disable");
        $(this).prop('checked', false);
        $('#confirm-orders-offer').prop('disabled', true);
        $('#sp-cancel').addClass("sp-disable");
      } else {
        $('#confirm-orders-offer').removeClass('disable');
        $(this).prop('checked', true);
        $('#confirm-orders-offer').prop('disabled', false);
        $('#sp-cancel').removeClass('sp-disable');
      }
    } else {
        $(this).prop('checked', false);
        $('#confirm-orders-offer').addClass("disable");
        $('#confirm-orders-offer').prop('disabled', true);
        $('#sp-cancel').addClass("sp-disable");
    }
  });

  //textArea
  $("input:text[name='other_area_offer']").on('change', function(e) {
    var params = {
      text_area: $(this).val(),
    };
    helper.updateLocalStorageValue('order_offer', params);
  });

  //area
  var area = $("input:radio[name='offer_area']");
  area.on("change",function(){
    var areaOffer = $("input:radio[name='offer_area']:checked").val();

    if('その他'== areaOffer){
      if(localStorage.getItem("order_offer")){
        var orderOffer = JSON.parse(localStorage.getItem("order_offer"));
      }

      if(orderOffer.text_area){
        $("input:text[name='other_area_offer']").val(orderOffer.text_area);
      }
    }

    var params = {
      select_area: areaOffer,
    };

    helper.updateLocalStorageValue('order_offer', params);
  })

  //duration
  var timeSet = $("input:radio[name='time_set_offer']");
  timeSet.on("change",function(){
    var hour = $(".select-hour-offer option:selected").val();
    var minute = $(".select-minute-offer option:selected").val();
    var duration = $("input:radio[name='time_set_offer']:checked").val();

    var params = {
      current_duration: duration,
    };
    helper.updateLocalStorageValue('order_offer', params);


    if('other_time_set' == duration) {
      var selectDuration = {
        select_duration: $('.select-duration-offer option:selected').val(),
      };

      duration = $('.select-duration-offer option:selected').val();

      helper.updateLocalStorageValue('order_offer', selectDuration);
    }

    var castIds = $('#current-cast-id-offer').val();
    var totalCast = castIds.split(',');
    var date = $('#current-date-offer').val();
    var time = hour + ':' + minute;
    var classId = $('#current-class-id-offer').val();

    var input = {
      date : date,
      start_time : time,
      type :2,
      duration :duration,
      total_cast :totalCast.length,
      nominee_ids : castIds,
      class_id :classId
    };

    window.axios.post('/api/v1/orders/price',input)
      .then(function(response) {
        totalPoint = response.data['data'];
        totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        $('.total-amount').text(totalPoint +'P~');

        var params = {
          current_total_point: totalPoint,
        };

        helper.updateLocalStorageValue('order_offer', params);
      }).catch(function(error) {
        console.log(error);
        if (error.response.status == 401) {
          window.location = '/login/line';
        }
      });
  })

  //other-duration
  $('.select-duration-offer').on("change",function(){
    var hour = $(".select-hour-offer option:selected").val();
    var minute = $(".select-minute-offer option:selected").val();
    var duration = $(this).val();

    var params = {
        select_duration: duration,
      };

    helper.updateLocalStorageValue('order_offer', params);

    var castIds = $('#current-cast-id-offer').val();
    var totalCast = castIds.split(',');
    var date = $('#current-date-offer').val();
    var time = hour + ':' + minute;
    var classId = $('#current-class-id-offer').val();

    var input = {
      date : date,
      start_time : time,
      type :2,
      duration :duration,
      total_cast :totalCast.length,
      nominee_ids : castIds,
      class_id : classId
    };

    window.axios.post('/api/v1/orders/price',input)
      .then(function(response) {
        totalPoint = response.data['data']
        totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        $('.total-amount').text(totalPoint +'P~');

        var params = {
            current_total_point: totalPoint,
          };

        helper.updateLocalStorageValue('order_offer', params);
      }).catch(function(error) {
        console.log(error);
        if (error.response.status == 401) {
          window.location = '/login/line';
        }
      });
  })

  $('.select-hour-offer').on('change', function (e) {
    var hour = $(this).val();
    var startTimeFrom = $('#start-time-from-offer').val();
    startTimeFrom = startTimeFrom.split(":");
    var startHourFrom = startTimeFrom[0];
    var startMinuteFrom = startTimeFrom[1];

    var startTimeTo = $('#start-time-to-offer').val();
    startTimeTo = startTimeTo.split(":");
    var startHourTo = startTimeTo[0];
    var startMinuteTo = startTimeTo[1];
    var html = '';

    startMinuteFrom = hour == startHourFrom ? startMinuteFrom : 0;
    startMinuteTo   = hour == startHourTo   ? startMinuteTo   : 59;

    for (var i = startMinuteFrom; i <= startMinuteTo; i++) {
      var value = i < 10 ? `0${i}` : i;

      html += `<option value="${value}">${value}分</option>`;
    }

    $('.select-minute-offer').html(html);
  });

  //time
  $('.date-select-offer').on("click",function(){
    var hour = $(".select-hour-offer option:selected").val();
    var minute = $(".select-minute-offer option:selected").val();

    var params = {
        hour : hour,
        minute : minute,
      };

    $('.time-offer').text(hour + ':' + minute +'~');

    helper.updateLocalStorageValue('order_offer', params);


    if ($("input:radio[name='time_set_offer']:checked").length) {
      var duration = $("input:radio[name='time_set_offer']:checked").val();

      if('other_time_set' == duration) {
        duration = $('.select-duration-offer option:selected').val();
      }

      var castIds = $('#current-cast-id-offer').val();
      var totalCast = castIds.split(',');
      var date = $('#current-date-offer').val();
      var time = hour + ':' + minute;
      var classId = $('#current-class-id-offer').val();

      var params = {
        date : date,
        start_time : time,
        type :2,
        duration :duration,
        total_cast :totalCast.length,
        nominee_ids : castIds,
        class_id : classId
      };

      window.axios.post('/api/v1/orders/price',params)
        .then(function(response) {
          totalPoint = response.data['data'];
          totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('.total-amount').text(totalPoint +'P~');

          var params = {
            current_total_point: totalPoint,
          };

          helper.updateLocalStorageValue('order_offer', params);
        }).catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login/line';
          }
      });
    }
  })

  $('.cf-orders-nominate').on('click',function(){
      if($('#md-require-card').length){
        $('#md-require-card').click();
      }else {
        document.getElementById('confirm-order-offer-submit').click();
        $('#create-offer-form').submit();
      }
  });

  if(localStorage.getItem("order_offer")){
    var orderOffer = JSON.parse(localStorage.getItem("order_offer"));

    if(orderOffer.current_total_point){
        $('.total-amount').text(orderOffer.current_total_point +'P~');
    }

      //area
    if(orderOffer.select_area){
     if('その他'== orderOffer.select_area){
        $('.area-offer').css('display', 'flex')
        $("input:text[name='other_area_offer']").val(orderOffer.text_area);
      }

      const inputArea = $(".input-area-offer");
      inputArea.parent().removeClass('active');

      $.each(inputArea,function(index,val){
        if (val.value == orderOffer.select_area) {
          $(this).prop('checked', true);
          $(this).parent().addClass('active');
        }
      })
    }

    //duration
    if(orderOffer.current_duration){
      if('other_time_set' == orderOffer.current_duration) {
        $('.time-input-offer').css('display','flex');

      }

      const inputDuration = $(".input-duration-offer");
      inputDuration.parent().removeClass('active');
      $.each(inputDuration,function(index,val){
        if (val.value == orderOffer.current_duration) {
          $(this).prop('checked', true);
          $(this).parent().addClass('active');
        }
      })

      if(orderOffer.select_duration) {
        const inputDuration = $('select[name=sl_duration_offer] option');
        $.each(inputDuration,function(index,val){
          if(val.value == orderOffer.select_duration) {
            $(this).prop('selected',true);
          }
        })
      }
    }

    //time
    if(orderOffer.hour){
      $('.time-offer').text(orderOffer.hour + ":" + orderOffer.minute + '~');

      const inputHour = $('select[name=select_hour_offer] option');
      $.each(inputHour,function(index,val){
        if(val.value == orderOffer.hour) {
          $(this).prop('selected',true);
        }
      })

      const inputMinute = $('select[name=select_minute_offer] option');
      $.each(inputMinute,function(index,val){
        if(val.value == orderOffer.minute) {
          $(this).prop('selected',true);
        }
      })

    }
  }

})
