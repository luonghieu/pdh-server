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
      var checkExpired = $("#check-expired").val();
      if(((checkExpired == 1) || !area || (area=='その他' && !otherArea))) {
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
    $('.modal-confirm-offer').css('display','inline-block');
    $('#order-offer-popup').prop('checked',true);
  })

  $('.attention-offer').on("click",function(event){
    $('#show-attention').prop('checked',true);
  })

  $('#lb-order-offer').on("click",function(event){
    $('.modal-confirm-offer').css('display','none');

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
      var offerId = $('.offer-id').val();
      var orderOffer = JSON.parse(localStorage.getItem("order_offer"));
      if(orderOffer[offerId]) {
        orderOffer = orderOffer[offerId];
        if(orderOffer.current_date) {
          var date = orderOffer.current_date;
        }else {
          var date = $('#current-date-offer').val();
        }
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
            window.location = '/login';
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
                var err = '開始時間は現在時刻から60分以降の時間を選択してください';
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
  $("input:text[name='other_area_offer']").on('input', function(e) {
    var offerId = $('.offer-id').val();
    var otherArea = $(this).val();

    var params = {
      text_area: otherArea,
    };

    helper.updateLocalStorageKey('order_offer', params, offerId);

    var area = $("input:radio[name='offer_area']:checked").val();

    if (!area || (!otherArea)) {
      $('#confirm-orders-offer').addClass("disable");
      $(".checked-order-offer").prop('checked', false);
      $('#confirm-orders-offer').prop('disabled', true);
      $('#sp-cancel').addClass("sp-disable");
    }
  });

  //area
  var area = $("input:radio[name='offer_area']");
  area.on("change",function(){
    var offerId = $('.offer-id').val();
    var areaOffer = $("input:radio[name='offer_area']:checked").val();

    if('その他'== areaOffer){
      if(localStorage.getItem("order_offer")){
        var orderOffer = JSON.parse(localStorage.getItem("order_offer"));
        
        if(orderOffer.text_area){
          $("input:text[name='other_area_offer']").val(orderOffer.text_area);
        }
      }

      if(!$("input:text[name='other_area_offer']").val()) {
        $('#confirm-orders-offer').addClass("disable");
        $(".checked-order-offer").prop('checked', false);
        $('#confirm-orders-offer').prop('disabled', true);
        $('#sp-cancel').addClass("sp-disable");
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
      var value = (i < 10) ? `0${parseInt(i)}` : i;

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

    var now = new Date();
    var check = hour;

    if (23<hour) {
      switch(hour) {
        case '24':
            check = '00';
            break;
        case '25':
            check = '01';
            break;
        case '26':
            check = '02';
            break;
      }


      if (checkApp.isAppleDevice()) {
        var checkDate = new Date(currentDate[1] +'/' +currentDate[2]+'/'+currentDate[0] +' ' +check +':' +minute);
      } else {
        var checkDate = new Date(currentDate[0] +'-' +currentDate[1]+'-'+currentDate[2] +' ' +check +':' +minute);
      }

      checkDate.setDate(checkDate.getDate() + 1);
    }else {
      if (checkApp.isAppleDevice()) {
        var checkDate = new Date(currentDate[1] +'/' +currentDate[2]+'/'+currentDate[0] +' ' +check +':' +minute);
      } else {
        var checkDate = new Date(currentDate[0] +'-' +currentDate[1]+'-'+currentDate[2] +' ' +check +':' +minute);
      }
    }

    utc = now.getTime() + (now.getTimezoneOffset() * 60000);
    nd = new Date(utc + (3600000*9));

    if (helper.add_minutes(nd, 60) > checkDate) {
      checkDate = helper.add_minutes(nd, 60);
    }

    var startTimeTo = $('#start-time-to-offer').val();
    startTimeTo = startTimeTo.split(":");
    var startHourTo = startTimeTo[0];
    var startMinuteTo = startTimeTo[1];
    var startTimeFrom = $('#start-time-from-offer').val();
    startTimeFrom = startTimeFrom.split(":");
    var startHourFrom = startTimeFrom[0];

    if (startHourTo <= startHourFrom) {
      if (checkApp.isAppleDevice()) {
        var timeTo = new Date(currentDate[1] +'/' +currentDate[2]+'/'+currentDate[0] +' ' +startHourTo +':' +startMinuteTo);
      } else {
        var timeTo = new Date(currentDate[0] +'-' +currentDate[1]+'-'+currentDate[2] +' ' +startHourTo +':' +startMinuteTo);
      }

      timeTo.setDate(timeTo.getDate() + 1);
    } else {
      if (checkApp.isAppleDevice()) {
        var timeTo = new Date(currentDate[1] +'/' +currentDate[2]+'/'+currentDate[0] +' ' +startHourTo +':' +startMinuteTo);
      } else {
        var timeTo = new Date(currentDate[0] +'-' +currentDate[1]+'-'+currentDate[2] +' ' +startHourTo +':' +startMinuteTo);
      }
    }

    if (timeTo < checkDate ) {
      checkDate = timeTo;
    }

    var monthOffer = checkDate.getMonth() +1;
    if (monthOffer<10) {
      monthOffer = '0'+monthOffer;
    }
    var dateOffer = checkDate.getDate();
    if (dateOffer<10) {
      dateOffer = '0'+dateOffer;
    }

    var yearOffer = checkDate.getFullYear();

    var hourOffer = checkDate.getHours();
    if (hourOffer<10) {
      hourOffer = '0'+hourOffer;
    }


    var minuteOffer = checkDate.getMinutes();
    if (minuteOffer<10) {
      minuteOffer = '0'+minuteOffer;
    }
    var time = yearOffer + '-' + monthOffer + '-' +  dateOffer;

    $('#temp-date-offer').text(yearOffer+'年'+monthOffer+'月'+dateOffer+'日');
    $('.time-offer').text(hourOffer + ':' + minuteOffer +'~');

    check = hourOffer;

    if (currentDate[2] != dateOffer) {
      switch(hourOffer) {
        case '00':
        hourOffer = '24';
            break;
        case '01':
        hourOffer = '25';
            break;
        case '02':
        hourOffer = '26';
            break;
      }
    }

    $('.select-hour-offer').val(hourOffer);
    $('.select-minute-offer').val(minuteOffer);

    var params = {
      current_date : time,
      hour : hourOffer,
      minute : minuteOffer,
    }

    helper.updateLocalStorageKey('order_offer', params, offerId);

    var duration = $("#duration-offer").val();

    var castIds = $('#current-cast-id-offer').val();
    var totalCast = castIds.split(',');
    var classId = $('#current-class-id-offer').val();
    var input = {
      date : time,
      start_time : check + ':' + minuteOffer,
      type :2,
      duration :duration,
      total_cast :totalCast.length,
      nominee_ids : castIds,
      class_id : classId,
      offer : 1
    };

    window.axios.post('/api/v1/orders/price',input)
      .then(function(response) {
        if (response.data.data) {
          var nightFee = parseInt(response.data.data.allowance_point).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          var orderPoint = parseInt(response.data.data.order_point + response.data.data.order_fee).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          totalPoint = parseInt(response.data.data.allowance_point + response.data.data.order_point + response.data.data.order_fee).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('#order-point').html(orderPoint + 'P');
          $('#night-fee').html(nightFee+'P');
          $('#total-point-order').html(totalPoint+'P');
          $('#temp-point-offer').val(response.data.data.allowance_point + response.data.data.order_point + response.data.data.order_fee);

          var data = {
            current_total_point: response.data.data.allowance_point + response.data.data.order_point + response.data.data.order_fee,
          };
          $('.total-amount').text(totalPoint +'P');
          helper.updateLocalStorageKey('order_offer', data, offerId);
        }
      }).catch(function(error) {
        console.log(error);
        if (error.response.status == 401) {
          window.location = '/login';
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
          $('.total-amount').text(totalPoint +'P');
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
            var value = i < 10 ? `0${parseInt(i)}` : i;
            var selected = i == orderOffer.minute ? 'selected' : '';

            html += `<option value="${value}" ${selected}>${value}分</option>`;
          }

          $('.select-minute-offer').html(html);
        }
      }
    }
  }

  var currentUrl = window.location.href;
  var regex = /offers\/\d/;

  if (currentUrl.match(regex)) {
    $('.btn-choose-time-success').click(function(event) {
      $('#temp-time-offer').removeClass('color-placeholder');
      $('#temp-time-offer').addClass('color-choose-time');
    });

    if(localStorage.getItem("order_offer")){
      var offerId = $('.offer-id').val();
      var orderOffer = JSON.parse(localStorage.getItem("order_offer"));
      if (orderOffer[offerId].hour) {
        $('#temp-time-offer').removeClass('color-placeholder');
        $('#temp-time-offer').addClass('color-choose-time');
      }
    } else {
      $('#temp-time-offer').removeClass('color-choose-time');
      $('#temp-time-offer').addClass('color-placeholder');
    }

    $('.details-list').css({
      display: 'none',
    });

    function caculatorPoint() {
      var hour = $(".select-hour-offer option:selected").val();

      var minute = $(".select-minute-offer option:selected").val();

      if(localStorage.getItem("order_offer")){
        var offerId = $('.offer-id').val();
        var orderOffer = JSON.parse(localStorage.getItem("order_offer"));
        if(orderOffer[offerId]) {
          orderOffer = orderOffer[offerId];
          if(orderOffer.current_date) {
            var date = orderOffer.current_date;
            hour = orderOffer.hour;
            if (23 < hour) {
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
            time = orderOffer.minute;
          }else {
            var date = $('#current-date-offer').val();
          }
        }
      } else {
        var date = $('#current-date-offer').val();
      }

      var time = hour + ':' + minute;
      var duration = $("#duration-offer").val();
      var classId = $('#current-class-id-offer').val();
      var castIds = $('#current-cast-id-offer').val();
      var totalCast = castIds.split(',').length;

      var params = {
        date: date,
        start_time: time,
        duration: duration,
        type: 2,

        class_id: classId,
        total_cast: totalCast,
        nominee_ids: castIds,
        offer: 1,
      }

      window.axios.post('/api/v1/orders/price', params)
      .then(function(response) {
        if(response.data.data) {
          var nightFee = parseInt(response.data.data.allowance_point).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          var orderPoint = parseInt(response.data.data.order_point + response.data.data.order_fee).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          var totalOrderPoint = parseInt(response.data.data.allowance_point + response.data.data.order_point + response.data.data.order_fee).toLocaleString(undefined,{ minimumFractionDigits: 0 });

          $('#order-point').html(orderPoint + 'P');
          $('#night-fee').html(nightFee+'P');
          $('#total-point-order').html(totalOrderPoint+'P');
        }
      })
      .catch(function(error) {
        console.error();
      });
    }

    caculatorPoint();

    // Set the date we're counting down to
    var date = $('#expired-date').val();
    var month = $('#expired-month').val();
    var year = $('#expired-year').val();
    var hour = $('#expired-hour').val();
    var minute = $('#expired-minute').val();

    if (date && month && year && hour && minute) {
      if (checkApp.isAppleDevice()) {
        var dateFolowDevice = new Date(month +'/' + date +'/'+ year +' ' + hour +':' + minute).getTime();
      } else {
        var dateFolowDevice = new Date(year +'-' + month +'-'+ date +' ' + hour +':' + minute).getTime();
      }

      // Update the count down every 1 second
      var x = setInterval(function() {
        // Get todays date and time
        var now = new Date();
        utc = now.getTime() + (now.getTimezoneOffset() * 60000);
        nd = new Date(utc + (3600000*9));
        var nowJapan = new Date(nd).getTime();
        // Find the distance between now and the count down date
        var distance = dateFolowDevice - nowJapan;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        if (minutes < 10) {
          minutes = '0' + minutes;
        }
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        if (seconds < 10) {
          seconds = '0' + seconds;
        }
        // Output the result in an element with id="demo"
        document.getElementById("time-countdown").innerHTML = hours+(days*24) + "時間"
        + minutes + "分" + seconds + "秒";
        // If the count down is over, write some text
        if (distance < 0) {
          clearInterval(x);
          document.getElementById("time-countdown").innerHTML = "0時間00分00秒";
          $("#check-expired").val(1);
        }
      }, 1000);
    } else {
      document.getElementById("time-countdown").innerHTML = "00時間00分00秒";
    }
  }
})
