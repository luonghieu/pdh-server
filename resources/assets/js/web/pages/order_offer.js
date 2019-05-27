const helper = require('./helper');
let couponOffer = [];

const couponType = {
  'POINT': 1,
  'DURATION': 2,
  'PERCENT': 3
};

const OrderPaymentMethod = {
  'Credit_Card': 1,
  'Direct_Payment': 2
};

function firstLoad() {
  var hour = $(".select-hour-offer option:selected").val();
  var minute = $(".select-minute-offer option:selected").val();
  var offerId = $('.offer-id').val();
  var date = $('#current-date-offer').val();
  var duration = parseInt($('#duration-offer').val());
  var classId = $('#current-class-id-offer').val();
  var castIds = $('#current-cast-id-offer').val();
  var totalCast = castIds.split(',').length;

  var couponId = null;

  if(!duration) {
    window.location = '/login';
  }

  if(localStorage.getItem("order_offer")){
    var offerId = $('.offer-id').val();
    var orderOffer = JSON.parse(localStorage.getItem("order_offer"));
    if(orderOffer[offerId]) {
      orderOffer = orderOffer[offerId];

      if(orderOffer.coupon) {
        couponId = orderOffer.coupon.id;
      }

      if(orderOffer.current_date) {
        date = orderOffer.current_date;
        hour = orderOffer.hour;
        minute = orderOffer.minute;

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
      }
    }
  }

  var time = hour + ':' + minute;

  var input = {
    date: date,
    start_time: time,
    duration: duration,
    type: 2,
    class_id: classId,
    total_cast: totalCast,
    nominee_ids: castIds,
    offer: 1,
  }

  var paramCoupon = {
    duration : duration,
  };

  window.axios.get('/api/v1/coupons', {params: paramCoupon})
  .then(function(response) {
    couponOffer = response.data['data'];

    var selectedCoupon = null;
    if (couponOffer.length) {
      var html = `<div class="caption">
                    <h2>クーポン</h2>
                  </div>
                  <div class="form-grpup" >
                    <select id="coupon-order-offer" class="select-coupon" name='select_coupon'>
                      <option value="" >クーポンを使用しない</option>`;

      couponOffer.forEach(function (coupon) {
        var selected = '';
        var id = coupon.id;
        var name = coupon.name;

        if(couponId == id) {

          var paramCoupon = {
            coupon : coupon
          }

          helper.updateLocalStorageKey('order_offer', paramCoupon, offerId);

          selectedCoupon = coupon;
          selected = 'selected';

          switch(coupon.type) {
            case couponType.POINT:
              input.duration_coupon = 0;
              break;

            case couponType.DURATION:
              input.duration_coupon = coupon.time;
              break;

            case couponType.PERCENT:
              input.duration_coupon = 0;
              break;

            default:
              window.location.href = '/mypage';
          }
        } else {
          selected = '';
        }

        html += '<option value="'+ id +'"'+ selected +' >'+ name +'</option>';
      })

      html += `</select>`;
      html += `<div id='show_point-sale-coupon' > `;

      if(selectedCoupon) {
        if(selectedCoupon.max_point) {
          var maxPoint = parseInt(selectedCoupon.max_point).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          html += `<p class = "max-point-coupon" > ※割引されるポイントは最大${maxPoint}Pになります。</p> </div>`;
        }
      }

      html += `</div> </div>`;
      $('#show-coupon-order-offer').html(html);
    }

    showPoint(input, offerId, selectedCoupon);
  }).catch(function(error) {
    console.log(error);
    if (error.response.status == 401) {
      window.location = '/login';
    }
  });
}

function selectedCouponsOffer()
{
  $('body').on('change', "#coupon-order-offer", function(){
    var hour = $(".select-hour-offer option:selected").val();
    var minute = $(".select-minute-offer option:selected").val();
    var offerId = $('.offer-id').val();
    var date = $('#current-date-offer').val();
    var duration = parseInt($('#duration-offer').val());
    var classId = $('#current-class-id-offer').val();
    var castIds = $('#current-cast-id-offer').val();
    var totalCast = castIds.split(',').length;

    var couponId = $(this).val();

    if(!duration) {
      window.location = '/login';
    }

    if(localStorage.getItem("order_offer")){
      var offerId = $('.offer-id').val();
      var orderOffer = JSON.parse(localStorage.getItem("order_offer"));
      if(orderOffer[offerId]) {
        orderOffer = orderOffer[offerId];

        if(orderOffer.current_date) {
          date = orderOffer.current_date;
          hour = orderOffer.hour;
          minute = orderOffer.minute;

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
        }
      }
    }

    var time = hour + ':' + minute;

    var input = {
      date: date,
      start_time: time,
      duration: duration,
      type: 2,
      class_id: classId,
      total_cast: totalCast,
      nominee_ids: castIds,
      offer: 1,
    }

    if(!couponOffer) {
      window.location = '/mypage';
    }

    var couponIds = couponOffer.map(function (e) {
      return e.id;
    });

    var coupon = null;
    if(parseInt(couponId)) {
      if(couponIds.indexOf(parseInt(couponId)) > -1) {
        couponOffer.forEach(function (e) {
          if(e.id == couponId) {
            coupon = e;
          }
        });

        var paramCoupon = {
          coupon : coupon
        }

        helper.updateLocalStorageKey('order_offer', paramCoupon, offerId);

        if($('#show_point-sale-coupon').length) {
          if(coupon.max_point) {
            var maxPoint = parseInt(coupon.max_point).toLocaleString(undefined,{ minimumFractionDigits: 0 });
            var html = `<p class = "max-point-coupon" > ※割引されるポイントは最大${maxPoint}Pになります。</p> </div>`;
            $('#show_point-sale-coupon').html(html);
          }
        }

        switch(coupon.type) {
          case couponType.POINT:
            input.duration_coupon = 0;
            break;

          case couponType.DURATION:
            input.duration_coupon = coupon.time;
            break;

          case couponType.PERCENT:
            input.duration_coupon = 0;
            break;

          default:
            window.location.href = '/mypage';
        }
      } else {
        window.location = '/mypage';
      }
    } else {
      if($('#show_point-sale-coupon').length) {
        $('#show_point-sale-coupon').html('');
      }

      if(localStorage.getItem("order_offer")){
        var orderOffer = JSON.parse(localStorage.getItem("order_offer"));
        if(orderOffer[offerId]) {
          orderOffer = orderOffer[offerId];
          if(orderOffer.coupon) {
            helper.deleteLocalStorageKey('order_offer','coupon', offerId);
          }
        }
      }
    }

    showPoint(input, offerId, coupon);
  })
}

function showPoint(input, offerId, coupon = null)
{
  window.axios.post('/api/v1/orders/price',input)
    .then(function(response) {
      if (response.data.data) {
        var result = response.data.data;
        var nightFee = parseInt(result.allowance_point).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        var orderPoint = parseInt(result.order_point + result.order_fee).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        var tempPoint = result.allowance_point + result.order_point + result.order_fee;
        var currentPoint = tempPoint;

        $('#order-point').html(orderPoint + 'P');
        $('#night-fee').html(nightFee+'P');

        if (coupon) {
          if (couponType.PERCENT == coupon.type) {
              var pointCoupon = (parseInt(coupon.percent)/100)*tempPoint;
            }

            if (couponType.POINT == coupon.type) {
              var pointCoupon = coupon.point;
            }

            if (couponType.DURATION == coupon.type) {
              var pointCoupon = result.order_point_coupon + result.order_fee_coupon;
            }

            if(coupon.max_point) {
              if(coupon.max_point < pointCoupon) {
                pointCoupon = coupon.max_point;
              }
            }

            currentPoint = tempPoint-pointCoupon;
            if(currentPoint<0) {
              currentPoint = 0;
            }

          pointCoupon = parseInt(pointCoupon).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('#sale-point-coupon').text('-' + pointCoupon +'P');

          $('#show-point-coupon-offer').css('display', 'flex');
        } else {
          $('#sale-point-coupon').text('');
          $('#show-point-coupon-offer').css('display', 'none');
        }

        var data = {
          current_total_point: currentPoint,
        };

        helper.updateLocalStorageKey('order_offer', data, offerId);
        $('#temp-point-offer').val(currentPoint);

        currentPoint = parseInt(currentPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        $('#total-point-order').html(currentPoint+'P');
        $('.total-amount').text(currentPoint +'P');

      }
    }).catch(function(error) {
      console.log(error);
      if (error.response.status == 401) {
        window.location = '/login';
      }
  });
}


function selectedTransfer()
{
  var transfer = $("input:radio[name='transfer_order_offer']");
  transfer.on("change",function(){
    var offerId = $('.offer-id').val();
    var transfer = $("input:radio[name='transfer_order_offer']:checked").val();

    var param = {
          payment_method : transfer,
        }

    helper.updateLocalStorageKey('order_offer', param, offerId);

    if (OrderPaymentMethod.Direct_Payment == parseInt(transfer)) {
      $('#card-registered').css('display', 'none');
    }

    if (OrderPaymentMethod.Credit_Card == parseInt(transfer)) {
      $('#card-registered').css('display', 'block');

      if ($('.inactive-button-order').length) {
        $('#confirm-orders-offer').addClass("disable");
        $('.checked-order-offer').prop('checked', false);
        $('#confirm-orders-offer').prop('disabled', true);
        $('#sp-cancel').addClass("sp-disable");
      }
    }
  })
}

function createOrderOffer(transfer = null)
{
  $('.modal-confirm-offer').css('display','none');
  $('#confirm-orders-offer').prop('disabled', true);

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

  var offerId = $('.offer-id').val();
  if(localStorage.getItem("order_offer")){
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
  var offerId = $('.offer-id').val();

  var params = {
    prefecture_id: orderOffer.prefecture_id,
    address: area,
    class_id: classId,
    duration: duration,
    date: date,
    start_time: time,
    total_cast: totalCast,
    type: 2,
    nominee_ids: castIds,
    temp_point: $('#temp-point-offer').val(),
    offer_id: offerId
  }

  if(transfer) {
    params.payment_method = transfer;
  }

  if(orderOffer.coupon) {
    var coupon = orderOffer.coupon;
    params.coupon_id = coupon.id;
    params.coupon_name = coupon.name;
    params.coupon_type = coupon.type;

    if(coupon.max_point) {
      params.coupon_max_point = coupon.max_point;
    } else {
      params.coupon_max_point = null;
    }

    switch(coupon.type) {
      case couponType.POINT:
        params.coupon_value = coupon.point;
        break;

      case couponType.DURATION:
        params.coupon_value = coupon.time;
        break;

      case couponType.PERCENT:
        params.coupon_value = coupon.percent;
        break;

      default:
        window.location.href = '/mypage';
    }
  }

  window.axios.post('/api/v1/orders/create_offer', params)
    .then(function(response) {
      $('#order-offer-popup').prop('checked',false);
      var roomId = response.data.data.room_id;
      window.location.href = '/message/' +roomId;
    })
    .catch(function(error) {
      $('#confirm-orders-offer').prop('disabled', false);
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
            if (error.response.status == 406) {
              $('#admin-edited').prop('checked',true);

              $('#reload-offer').on("click",function(event){
                if (localStorage.getItem("order_offer")) {
                  localStorage.removeItem("order_offer");
                }
                window.location = '/offers/' + offerId;
              })
            } else {
              var content = '';
              var err ='';

              switch(error.response.status) {
                case 400:
                  var err = '開始時間は現在時刻から30分以降の時間を選択してください';
                  break;
                case 404:
                  var err = '支払い方法が未登録です';
                  break;
                case 409:
                  var err = 'クーポンが無効です';
                  break;
                case 412:
                  var err = '退会申請中のため、予約することはできません。';
                  break;
                case 500:
                  var err = 'この操作は実行できません';
                  break;

                default:break;
              }

              $('#err-offer-message h2').html(err);
              $('#err-offer-message p').html(content);

              $('#err-offer').prop('checked',true);
            }

          }
        }
    })
}

$(document).ready(function(){
  function dayOfWeek() {
    return ['日', '月', '火', '水', '木', '金', '土'];
  }

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

    if(3 == $offerStatus || 4 == $offerStatus || $('.deleted_at').val()) {
      $('#timeout-offer-message h2').css('font-size', '15px');

      if ($('.deleted_at').val()) {
        $('#timeout-offer-message h2').html('この予約は無効になりました');
      } else {
        $('#timeout-offer-message h2').html('この予約は募集が締め切られました');
      }

      $('#close-offer').addClass('mypage');

      $('#timeout-offer').prop('checked',true);

      $('.mypage').on("click",function(event){
        window.location = '/mypage';
      })
    }
  }

  $('body').on('change', ".checked-order-offer",function(event){
    if ($(this).is(':checked')) {
      if(localStorage.getItem("order_offer")){
        var offerId = $('.offer-id').val();
        var orderOffer = JSON.parse(localStorage.getItem("order_offer"));

        if(orderOffer[offerId]) {
          orderOffer = orderOffer[offerId];
          var area = $("input:radio[name='offer_area']:checked").val();
          var otherArea = $("input:text[name='other_area_offer']").val();
          var checkExpired = $("#check-expired").val();
          var checkCard = $('.inactive-button-order').length;
          var transfer = $("input:radio[name='transfer_order_offer']:checked").val();

          if(OrderPaymentMethod.Direct_Payment == transfer) {
            checkCard = false;
          }

          if(((checkExpired == 1) || !area || (area=='その他' && !otherArea) || checkCard
            || !orderOffer.current_date)) {
            $('#confirm-orders-offer').addClass("disable");
            $(this).prop('checked', false);
            $('#confirm-orders-offer').prop('disabled', true);
            $('#sp-cancel').addClass("sp-disable");
          } else {
            $(this).prop('checked', true);
            $('#sp-cancel').removeClass('sp-disable');
            $('#confirm-orders-offer').removeClass('disable');
            $('#confirm-orders-offer').prop('disabled', false);
          }
        } else {
          $('#confirm-orders-offer').addClass("disable");
          $(this).prop('checked', false);
          $('#confirm-orders-offer').prop('disabled', true);
          $('#sp-cancel').addClass("sp-disable");
        }
      } else {
        $('#confirm-orders-offer').addClass("disable");
        $(this).prop('checked', false);
        $('#confirm-orders-offer').prop('disabled', true);
        $('#sp-cancel').addClass("sp-disable");
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

  $('body').on('click', "#lb-order-offer", function(event){
    var transfer = parseInt($("input[name='transfer_order_offer']:checked").val());

    if (transfer) {
      if (OrderPaymentMethod.Credit_Card == transfer || OrderPaymentMethod.Direct_Payment == transfer) {
        if (OrderPaymentMethod.Direct_Payment == transfer) {
          window.axios.get('/api/v1/auth/me')
            .then(function(response) {
              var pointUser = response.data['data'].point;

              window.axios.get('/api/v1/guest/points_used')
                .then(function(response) {
                  var pointUsed = response.data['data'];
                  var tempPointOrder = parseInt($('#temp-point-offer').val()) + parseInt(pointUsed);

                  if (parseInt(tempPointOrder) > parseInt(pointUser)) {
                    $('#order-offer-popup').prop('checked',false);
                    $('.checked-order-offer').prop('checked', false);
                    $('#sp-cancel').addClass('sp-disable');
                    $('#confirm-orders-offer').prop('disabled', true);
                    $('#confirm-orders-offer').addClass('disable');

                    if (parseInt(pointUsed) > parseInt(pointUser)) {
                      var point = parseInt($('#temp-point-offer').val());
                    } else {
                      var point = parseInt(tempPointOrder) - parseInt(pointUser);
                    }

                    window.location.href = '/payment/transfer?point=' + point;

                    return ;
                  } else {
                    createOrderOffer(transfer);
                  }
                }).catch(function(error) {
                  console.log(error);
                  if (error.response.status == 401) {
                    window.location = '/login';
                  }
                });
            }).catch(function(error) {
              console.log(error);
              if (error.response.status == 401) {
                window.location = '/login';
              }
            });
        } else {
          createOrderOffer(transfer);
        }
      } else {
          window.location.href = '/mypage';
      }
    } else {
      createOrderOffer();
    }
  });

  //textArea
  $('body').on('input', "input:text[name='other_area_offer']", function(e){
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
  $('body').on('change', "input:radio[name='offer_area']", function(){
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

  $('body').on('click', ".date-select-offer", function(){
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

    if (helper.add_minutes(nd, 30) > checkDate) {
      checkDate = helper.add_minutes(nd, 30);
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

    if (checkApp.isAppleDevice()) {
      var dateFolowDevice = new Date(monthOffer +'/' + dateOffer +'/'+ yearOffer);
    } else {
      var dateFolowDevice = new Date(yearOffer +'-' + monthOffer +'-'+ dateOffer);
    }

    var getDayOfWeek = dateFolowDevice.getDay();
    var dayOfWeekString = dayOfWeek()[getDayOfWeek];

    $('#temp-date-offer').text(yearOffer+'年'+monthOffer+'月'+dateOffer+'日('+dayOfWeekString+')');
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

    if(!couponOffer) {
      window.location = '/mypage';
    }

    var couponId = null;
    if($('#coupon-order-offer').length) {
      couponId = $('#coupon-order-offer').val();
    }

    var couponIds = couponOffer.map(function (e) {
      return e.id;
    });

    var coupon = null;
    if(parseInt(couponId)) {
      if(couponIds.indexOf(parseInt(couponId)) > -1) {
        couponOffer.forEach(function (e) {
          if(e.id == couponId) {
            coupon = e;
          }
        });

        switch(coupon.type) {
          case couponType.POINT:
            input.duration_coupon = 0;
            break;

          case couponType.DURATION:
            input.duration_coupon = coupon.time;
            break;

          case couponType.PERCENT:
            input.duration_coupon = 0;
            break;

          default:
            window.location.href = '/mypage';
        }
      } else {
        window.location = '/mypage';
      }
    }

    showPoint(input, offerId, coupon);
  })

  if($('#temp-point-offer').length) {
    var offerId = $('.offer-id').val();
    if(localStorage.getItem("order_offer")){
      let orderOffer = JSON.parse(localStorage.getItem("order_offer"));

      if(orderOffer[offerId]) {
        orderOffer = orderOffer[offerId];

        // if(orderOffer.current_total_point){
        //   totalPoint = parseInt(orderOffer.current_total_point).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        //   $('.total-amount').text(totalPoint +'P');
        //   $('#temp-point-offer').val(orderOffer.current_total_point);
        // }


        //payment

        if(orderOffer.payment_method) {
          const inputTransfer = $("input:radio[name='transfer_order_offer']");
          $.each(inputTransfer,function(index,val){
            if(val.value == parseInt(orderOffer.payment_method)) {
              $(this).prop('checked',true);
            }
          })

          if (OrderPaymentMethod.Direct_Payment == parseInt(orderOffer.payment_method)) {
            $('#card-registered').css('display', 'none');
          }
        }

        if(orderOffer.current_date) {
          currentDate = orderOffer.current_date.split('-');
          if (checkApp.isAppleDevice()) {
            var dateFolowDevice = new Date(currentDate[1] +'/' + currentDate[2] +'/'+ currentDate[0]);
          } else {
            var dateFolowDevice = new Date(currentDate[0] +'-' + currentDate[1] +'-'+ currentDate[2]);
          }

          var getDayOfWeek = dateFolowDevice.getDay();
          var dayOfWeekString = dayOfWeek()[getDayOfWeek];
          $('#temp-date-offer').text(currentDate[0]+'年'+currentDate[1]+'月'+currentDate[2]+'日('+dayOfWeekString+')');
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

        if(orderOffer.prefecture_id){
          $('.select-prefecture-offer').val(orderOffer.prefecture_id);
          var params = {
            prefecture_id : orderOffer.prefecture_id,
          };

          window.axios.get('/api/v1/municipalities', {params})
            .then(function(response) {
              var data = response.data;

              var municipalities = (data.data);
              html = '';
              municipalities.forEach(function (val) {
                name = val.name;
                html += '<label class="button button--green area">';
                html += '<input class="input-area-offer" type="radio" name="offer_area" value="'+ name +'">' + name +'</label>';
              })

              html += '<label id="area_input" class="button button--green area ">';
              html += '<input class="input-area-offer" type="radio" name="offer_area" value="その他">その他</label>';
              html += '<label class="area-input area-offer"><span>希望エリア</span>';
              html += '<input type="text" id="other_area_offer" placeholder="入力してください" name="other_area_offer" value=""></label>';

              $('#list-municipalities-offer').html(html);

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
            })
            .catch(function (error) {
              console.log(error);
              if (error.response.status == 401) {
                window.location = '/login';
              }
            });
        }
      }
    } else {
      var params = {
          prefecture_id : $('.select-prefecture-offer option:selected').val(),
        };
      helper.updateLocalStorageKey('order_offer', params, offerId);
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
      if (orderOffer[offerId]) {
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
    if($('#temp-point-offer').length) {
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
          var utc = now.getTime() + (now.getTimezoneOffset() * 60000);
          var nd = new Date(utc + (3600000*9));
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
  }

  //select prefecture
  var selectedPrefectureOffer = $(".select-prefecture-offer");
  selectedPrefectureOffer.on("change",function(){
    var offerId = $('.offer-id').val();
    $('#confirm-orders-offer').addClass("disable");
    $('.checked-order-offer').prop('checked', false);
    $('#confirm-orders-offer').prop('disabled', true);
    $('#sp-cancel').addClass("sp-disable");

    helper.deleteLocalStorageKey('order_offer','select_area', offerId);
    helper.deleteLocalStorageKey('order_offer','text_area', offerId);

    var params = {
      prefecture_id : this.value,
    };

    helper.updateLocalStorageKey('order_offer', params, offerId);

    window.axios.get('/api/v1/municipalities', {params})
      .then(function(response) {
        var data = response.data;

        var municipalities = (data.data);
        html = '';
        municipalities.forEach(function (val) {
          name = val.name;
          html += '<label class="button button--green area">';
          html += '<input class="input-area-offer" type="radio" name="offer_area" value="'+ name +'">' + name +'</label>';
        })

        html += '<label id="area_input" class="button button--green area ">';
        html += '<input class="input-area-offer" type="radio" name="offer_area" value="その他">その他</label>';
        html += '<label class="area-input area-offer"><span>希望エリア</span>';
        html += '<input type="text" id="other_area_offer" placeholder="入力してください" name="other_area_offer" value=""></label>';

        $('#list-municipalities-offer').html(html);
      })
      .catch(function (error) {
        console.log(error);
        if (error.response.status == 401) {
          window.location = '/login';
        }
      });
  });

  if($('#temp-point-offer').length) {
    firstLoad();
    selectedCouponsOffer();
    selectedTransfer();
  }
})
