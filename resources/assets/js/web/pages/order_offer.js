$(document).ready(function(){
  const helper = require('./helper');

  var checkApp = {
      isAppleDevice : function() {
        if (navigator.userAgent.match(/(iPhone|iPod|iPad)/) != null) {
          return true;
        }

        return false;
      }
    };

  if($('.offer-status').length) {
    $offerStatus = $('.offer-status').val();

    if(3 == $offerStatus || 4 == $offerStatus) {
      $('#timeout-offer-message h2').css('font-size', '15px');

      $('#timeout-offer-message h2').html('この予約は募集が締め切られました');

      $('#close-offer').addClass('mypage');

      $('#timeout-offer').prop('checked',true);

      $('.mypage').on("click",function(event){
        window.location = '/mypage';
      })
    }

  }

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

  //order-active
  $('#confirm-orders-offer').on("click",function(event){
    $('#order-offer-popup').prop('checked',true);
  })

  $('.attention-offer').on("click",function(event){
    $('#show-attention').prop('checked',true);
  })

  $('#lb-order-offer').on("click",function(event){
    var area = $("input:radio[name='offer_area']:checked").val();
    if('その他'== area){
      area =   $("input:text[name='other_area_offer']").val();
    }

    var hour = $(".select-hour-offer option:selected").val();
    if (23<hour) {
      switch(hour) {
        case '24':
            hour = '00';
            break;
        case '25':
            hour = '01';
            break;
        case '26':
            hour = '02';
            break;
      }
    }
    var minute = $(".select-minute-offer option:selected").val();

    var time = hour + ':' + minute;

    if(localStorage.getItem("order_offer")){
      var orderOffer = JSON.parse(localStorage.getItem("order_offer"));
      if(orderOffer.current_date) {
        var date = orderOffer.current_date;
      } else {
        var date = $('#current-date-offer').val();
      }
    } else {
      var date = $('#current-date-offer').val();
    }

    var duration = $("#duration-offer").val();
    var classId = $('#current-class-id-offer').val();
    var castIds = $('#current-cast-id-offer').val();
    var totalCast = castIds.split(',').length;
    var tempPoint = $('#temp-point-offer').val();
    var offerId = $('.offer-id').val();

    var params = {
      prefecture_id: 13,
      address: area,
      class_id: classId,
      duration: duration,
      date: date,
      start_time: time,
      total_cast: totalCast,
      type: 2,
      nominee_ids: castIds,
      temp_point: tempPoint,
      offer_id: offerId
    }

    window.axios.post('/api/v1/orders/create_offer', params)
      .then(function(response) {
        $('#order-offer-popup').prop('checked',false);
        var roomId = response.data.data.room_id;
        window.location.href = '/message/' +roomId;
      })
      .catch(function(error) {
        $('#order-offer-popup').prop('checked',false);
         if (error.response.status == 401) {
            window.location = '/login/line';
          } else {
            if(error.response.status == 422) {
                $('#timeout-offer-message h2').css('font-size', '15px');

                $('#timeout-offer-message h2').html('この予約は募集が締め切られました');

                $('#close-offer').addClass('mypage');

                $('#timeout-offer').prop('checked',true);

                $('.mypage').on("click",function(event){
                  window.location = '/mypage';
                })
            } else {
              var content = '';
              var err ='';

              if (error.response.status == 400) {
                var err = '開始時間は現在以降の時間を指定してください';
              }

              if(error.response.status == 500) {
              var err = 'この操作は実行できません';
              }

              if(error.response.status == 404) {
                var err = '予約が存在しません';
              }

              if(error.response.status == 409) {
                var err = '支払い方法が未登録です';
              }

               if(error.response.status == 406) {
                content = '予約日までにクレジットカードの <br> 1有効期限が切れます  <br> <br> 予約を完了するには  <br> カード情報を更新してください';
              }

              $('#err-offer-message h2').html(err);
              $('#err-offer-message p').html(content);

              $('#err-offer').prop('checked',true);
            }
          }


      })
  })

  //textArea
  $("input:text[name='other_area_offer']").on('change', function(e) {
    var offerId = $('.offer-id').val();
    var params = {
      text_area: $(this).val(),
    };

    helper.updateLocalStorageKey('order_offer', params, offerId);
  });

  //area
  var area = $("input:radio[name='offer_area']");
  area.on("change",function(){
    var offerId = $('.offer-id').val();
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
    }

    helper.updateLocalStorageKey('order_offer', params, offerId);

  })

  $('.select-hour-offer').on('change', function (e) {
    var hour = $(this).val();

    if (23<hour) {
      switch(hour) {
        case '24':
            hour = '00';
            break;
        case '25':
            hour = '01';
            break;
        case '26':
            hour = '02';
            break;
      }
    }

    var startTimeFrom = $('#start-time-from-offer').val();
    startTimeFrom = startTimeFrom.split(":");
    var startHourFrom = startTimeFrom[0];
    var startMinuteFrom = startTimeFrom[1];

    var startTimeTo = $('#start-time-to-offer').val();
    startTimeTo = startTimeTo.split(":");
    var startHourTo = startTimeTo[0];
    var startMinuteTo = startTimeTo[1];
    var html = '';

    startMinuteFrom = hour == startHourFrom ? parseInt(startMinuteFrom) : 0;
    startMinuteTo   = hour == startHourTo   ? parseInt(startMinuteTo) : 59;

    for (var i = startMinuteFrom; i <= startMinuteTo; i++) {
      var value = (i < 10) ? `0${i}` : i;

      html += `<option value="${value}">${value}分</option>`;
    }

    $('.select-minute-offer').html(html);
  });

  //time
  $('.date-select-offer').on("click",function(){
    var hour = $(".select-hour-offer option:selected").val();
    var minute = $(".select-minute-offer option:selected").val();
    var currentDate = $('#current-date-offer').val();
    var offerId = $('.offer-id').val();

    currentDate = currentDate.split('-');

     var params = {
        hour : hour,
        minute : minute,
      };

    helper.updateLocalStorageKey('order_offer', params, offerId);

    if (23<hour) {
      switch(hour) {
        case '24':
            hour = '00';
            break;
        case '25':
            hour = '01';
            break;
        case '26':
            hour = '02';
            break;
      }


      if (checkApp.isAppleDevice()) {
        var selectDate = new Date(currentDate[1] +'/' +currentDate[2]+'/'+currentDate[0]);
      } else {
        var selectDate = new Date(currentDate[0] +'-' +currentDate[1]+'-'+currentDate[2]);
      }

      selectDate.setDate(selectDate.getDate() + 1);

      var monthOffer = selectDate.getMonth() +1;
      if (monthOffer<10) {
        monthOffer = '0'+monthOffer;
      }
      var dateOffer = selectDate.getDate();
      if (dateOffer<10) {
        dateOffer = '0'+dateOffer;
      }

      var yearOffer = selectDate.getFullYear();
      var time = yearOffer + '-' + monthOffer + '-' +  dateOffer;
      $('#temp-date-offer').text(yearOffer+'年'+monthOffer+'月'+dateOffer+'日');
    }else {
      var time = $('#current-date-offer').val();
      $('#temp-date-offer').text(currentDate[0]+'年'+currentDate[1]+'月'+currentDate[2]+'日');
    }


    $('.time-offer').text(hour + ':' + minute +'~');

    var params = {
      current_date : time,
    }

    helper.updateLocalStorageKey('order_offer', params, offerId);

    var duration = $("#duration-offer").val();

    var castIds = $('#current-cast-id-offer').val();
    var totalCast = castIds.split(',');
    var classId = $('#current-class-id-offer').val();
    var params = {
      date : time,
      start_time : hour + ':' + minute,
      type :2,
      duration :duration,
      total_cast :totalCast.length,
      nominee_ids : castIds,
      class_id : classId,
      offer : 1
    };

    window.axios.post('/api/v1/orders/price',params)
      .then(function(response) {
        totalPoint = response.data['data'];
        $('#temp-point-offer').val(totalPoint);
        var params = {
          current_total_point: totalPoint,
        };

        totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        $('.total-amount').text(totalPoint +'P~');


        helper.updateLocalStorageKey('order_offer', params, offerId);
      }).catch(function(error) {
        console.log(error);
        if (error.response.status == 401) {
          window.location = '/login/line';
        }
    });
  })

  if($('#temp-point-offer').length) {

    if(localStorage.getItem("order_offer")){
      var offerId = $('.offer-id').val();
      var orderOffer = JSON.parse(localStorage.getItem("order_offer"));

      if(orderOffer[offerId]) {
        orderOffer = orderOffer[offerId];

        if(orderOffer.current_total_point){
          totalPoint = parseInt(orderOffer.current_total_point).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('.total-amount').text(totalPoint +'P~');
          $('#temp-point-offer').val(orderOffer.current_total_point);
        }

        if(orderOffer.current_date) {
          currentDate = orderOffer.current_date.split('-');
          $('#temp-date-offer').text(currentDate[0]+'年'+currentDate[1]+'月'+currentDate[2]+'日');
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

        //time
        if(orderOffer.hour){
          var hour = orderOffer.hour;
          const inputHour = $('select[name=select_hour_offer] option');
          $.each(inputHour,function(index,val){
            if(val.value == hour) {
              $(this).prop('selected',true);
            }
          })

          if (23<hour) {
            switch(hour) {
              case '24':
                  hour = '00';
                  break;
              case '25':
                  hour = '01';
                  break;
              case '26':
                  hour = '02';
                  break;
            }
          }

          $('.time-offer').text(hour + ":" + orderOffer.minute + '~');


          var startTimeFrom = $('#start-time-from-offer').val();
          startTimeFrom = startTimeFrom.split(":");
          var startHourFrom = startTimeFrom[0];
          var startMinuteFrom = startTimeFrom[1];

          var startTimeTo = $('#start-time-to-offer').val();
          startTimeTo = startTimeTo.split(":");
          var startHourTo = startTimeTo[0];
          var startMinuteTo = startTimeTo[1];
          var html = '';

          startMinuteFrom = orderOffer.hour == startHourFrom ? startMinuteFrom : 0;
          startMinuteTo   = orderOffer.hour == startHourTo   ? startMinuteTo   : 59;

          for (var i = startMinuteFrom; i <= startMinuteTo; i++) {
            var value = i < 10 ? `0${i}` : i;
            var selected = i == orderOffer.minute ? 'selected' : '';

            html += `<option value="${value}" ${selected}>${value}分</option>`;
          }

          $('.select-minute-offer').html(html);
        }
      }
    }
  }
})
