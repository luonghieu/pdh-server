const helper = require('./helper');
let couponNominee = [];
const couponType = {
  'POINT': 1,
  'DURATION': 2,
  'PERCENT': 3
};

const OrderPaymentMethod = {
  'Credit_Card': 1,
  'Direct_Payment': 2
};

function loadCouponsOrderNominate()
{
  var couponId = null;

  if(localStorage.getItem("order_params")) {
    var orderNominate = JSON.parse(localStorage.getItem("order_params"));
    if(orderNominate.current_duration) {
      var duration = orderNominate.current_duration;

      if ('other_time_set' == duration) {
        duration = 4;

        if(orderNominate.select_duration) {
          duration = orderNominate.select_duration;
        }
      }
    } else {
      var duration = $("input:radio[name='time_set_nomination']:checked").val();

      if(duration) {
        if ('other_time_set' == duration) {
          duration = $('.select-duration option:selected').val();
        }
      } else {
        duration = null;
      }
    }

    if (orderNominate.coupon) {
      couponId = orderNominate.coupon;
    }
  } else {
    var duration = $("input:radio[name='time_set_nomination']:checked").val();

    if(duration) {
      if ('other_time_set' == duration) {
        duration = $('.select-duration option:selected').val();
      }
    } else {
      duration = null;
    }
  }

  var paramCoupon = {
    duration : duration,
  };

  window.axios.get('/api/v1/coupons', {params: paramCoupon})
  .then(function(response) {
    couponNominee = response.data['data'];
    if (couponNominee.length) {
      var html =  `<div class="reservation-item">
                    <div class="caption">
                      <h2>クーポン</h2>
                    </div>
                    <div class="form-grpup" >
                      <select id="coupon-order-nominate" class="select-coupon" name='select_coupon'>
                      <option value="" >クーポンを使用しない</option> `;

      var selectedCoupon = null;
      couponNominee.forEach(function (coupon) {
        var selected = '';
        var id = coupon.id;
        var name = coupon.name;

        if(couponId == id) {
          selectedCoupon = coupon;
          var time = $("input:radio[name='time_join_nomination']:checked").val();
          priceCoupon(duration, time, helper, couponId);
          selected = 'selected';

          switch(coupon.type) {
            case couponType.POINT:
              $('#value-coupon').val(coupon.point);
              break;

            case couponType.DURATION:
              $('#value-coupon').val(coupon.time);
              break;

            case couponType.PERCENT:
              $('#value-coupon').val(coupon.percent);
              break;

            default:
              window.location.href = '/mypage';
          }

          $('#type-coupon').val(coupon.type);

          $('#name-coupon').val(coupon.name);

          if(coupon.max_point) {
            $('#max_point-coupon').val(coupon.max_point);
          } else {
            $('#max_point-coupon').val('');
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
          var max_point = parseInt(selectedCoupon.max_point).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          html += `<p class = "max-point-coupon" > ※割引されるポイントは最大${max_point}Pになります。</p> </div>`;
        }
      }

      html += `</div> </div>`;
      $('#show-coupon-order-nominate').html(html);
    }

  }).catch(function(error) {
    console.log(error);
    if (error.response.status == 401) {
      window.location = '/login';
    }
  });
}

function priceCoupon(duration, time = null, helper, couponId)
{
  if(duration) {
    if ('other_time_set' == duration) {
      duration = $('.select-duration option:selected').val();
    }
    
    var couponId = parseInt(couponId);

    $castId = $('.cast-id').val();
    
    var params = {
      type :3,
      duration :duration,
      total_cast :1,
      nominee_ids : $castId
    };

    if(time){
      var currentDate = new Date();
      utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
      nd = new Date(utc + (3600000*9));

      var year = nd.getFullYear();
      
      if ((time == 'other_time')) {
        var month = $('.select-month').val();
        var checkMonth = nd.getMonth();

        if (month <= checkMonth) {
          var year = nd.getFullYear() + 1;
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
      }else{
        var selectDate = helper.add_minutes(nd,time);

        if (helper.add_minutes(nd, 30) > selectDate) {
          selectDate = helper.add_minutes(nd, 30);
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

      params.date = date;
      params.start_time = time;

      if(couponId) {
        if(!couponNominee) {
          window.location = '/mypage';
        }

        var couponIds = couponNominee.map(function (e) {
          return e.id; 
        });

        var coupon = {};
        if(couponIds.indexOf(couponId) > -1) {
          couponNominee.forEach(function (e) {
            if(e.id == couponId) {
              coupon = e;
            }
          });
        } else {
          window.location = '/mypage';
        }

        if(coupon.max_point) {
          if($('#show_point-sale-coupon').length) {
            var max_point = parseInt(coupon.max_point).toLocaleString(undefined,{ minimumFractionDigits: 0 });
            var html = `<p class = "max-point-coupon" > ※割引されるポイントは最大${max_point}Pになります。</p> `;
            $('#show_point-sale-coupon').html(html);
          }
        } else {
          $('#show_point-sale-coupon').html('');
        }

        if(couponType.POINT == coupon.type) {
          params.duration_coupon = 0;
        }

        if(couponType.DURATION == coupon.type) {
          params.duration_coupon = coupon.time;
        }

        if(couponType.PERCENT == coupon.type) {
          params.duration_coupon = 0;
        }

        window.axios.post('/api/v1/orders/price',params)
          .then(function(response) {
            if (couponType.PERCENT == coupon.type) {
              var tempPoint = response.data['data'];
              var pointCoupon = (parseInt(coupon.percent)/100)*tempPoint;
            }

            if (couponType.POINT == coupon.type) {
              var tempPoint = response.data['data'];
              var pointCoupon = coupon.point;
            }

            if (couponType.DURATION == coupon.type) {
              var totalCouponPoint = response.data['data'];
              var tempPoint = totalCouponPoint.total_point;
              var pointCoupon = totalCouponPoint.order_point_coupon + totalCouponPoint.order_fee_coupon;
            }

            if(coupon.max_point) {
              if(coupon.max_point < pointCoupon) {
                pointCoupon = coupon.max_point;
              }
            }

            var currentPoint = tempPoint-pointCoupon;
            if(currentPoint<0) {
              currentPoint = 0;
            }

            $('#current-temp-point').val(currentPoint);

            var params = {
              current_total_point: tempPoint,
            };

            helper.updateLocalStorageValue('order_params', params);

            totalPoint = parseInt(currentPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
            pointCoupon = parseInt(pointCoupon).toLocaleString(undefined,{ minimumFractionDigits: 0 });
            tempPoint = parseInt(tempPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });


            var html = `<div class="details-total__content show_point-coupon">
                            <div class="reservation-total__sum content-coupon">通常料金
                            <span class="details-total__marks" id="tempoint_order-nominate"></span></div>
                        </div>
                        <div class="details-total__content show_point-coupon">
                          <div class="reservation-total__sum content-coupon">割引額
                          <span class="details-total__marks sale-point-coupon" id = 'sale_point'></span></div>
                        </div>
            `;

            $('#detail_point-coupon').html(html);
            $('#tempoint_order-nominate').text(tempPoint +'P');
            $('#sale_point').text('-' + pointCoupon +'P');
            $('.total-point').text(totalPoint +'P~');
          }).catch(function(error) {
            console.log(error);
            if (error.response.status == 401) {
              window.location = '/login';
            }
          });
      } else {

        var paramCoupon = {
          duration : parseInt(duration)
        }

        window.axios.get('/api/v1/coupons', {params: paramCoupon})
        .then(function(response) {
          couponNominee = response.data['data'];
          if (couponNominee.length) {
            var html =  `<div class="reservation-item">
                          <div class="caption">
                            <h2>クーポン</h2>
                          </div>
                          <div class="form-grpup" >
                            <select id="coupon-order-nominate" class="select-coupon" name='select_coupon'>
                            <option value="" >クーポンを使用しない</option> `;
            couponNominee.forEach(function (coupon) {
              var id = coupon.id;
              var name = coupon.name;
              html += '<option value="'+ id +'">'+ name +'</option>';
            })

            html += `</select>`;
            html += `<div id='show_point-sale-coupon' > </div>`;

            html += `</div> </div>`;
            $('#show-coupon-order-nominate').html(html);
          } else {
            $('#show-coupon-order-nominate').html('');
          }

        }).catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login';
          }
        });

        $('#detail_point-coupon').html('');
        $('#show_point-sale-coupon').html('');

        window.axios.post('/api/v1/orders/price',params)
        .then(function(response) {
          totalPoint = response.data['data'];
          $('#current-temp-point').val(totalPoint);

          var params = {
            current_total_point: totalPoint,
          };

          helper.updateLocalStorageValue('order_params', params);

          totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('.total-point').text(totalPoint +'P~');

        }).catch(function(error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login';
          }
        });
      }
    } else {
      var cost = $('.cost-order').val();
      var totalPoint=cost*(duration*6)/3;

      cost = parseInt(cost).toLocaleString(undefined,{ minimumFractionDigits: 0 });

      $('.reservation-total__text').text('内訳：'+cost+ '(キャストP/30分)✖'+(duration)+'時間');
      $('#current-temp-point').val(totalPoint);

      var params = {
      current_total_point: totalPoint,
      };

      helper.updateLocalStorageValue('order_params', params);

      totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });

      $('.total-point').text(totalPoint +'P~');

    }
  }
}

function selectedCouponsNominate(helper)
{
  $('body').on('change', "#coupon-order-nominate", function(){
    var couponId = $(this).val();
    var duration = $("input:radio[name='time_set_nomination']:checked").val();
    var time = $("input:radio[name='time_join_nomination']:checked").val();

    if(!couponNominee) {
      window.location = '/mypage';
    }

    var couponIds = couponNominee.map(function (e) {
      return e.id; 
    });

    if(parseInt(couponId)) {
      var coupon = {};
      if(couponIds.indexOf(parseInt(couponId)) > -1) {
        couponNominee.forEach(function (e) {
          if(e.id == couponId) {
            coupon = e;
          }
        });
        var paramCoupon = {
          coupon : parseInt(couponId)
        }

        helper.updateLocalStorageValue('order_params', paramCoupon);

        switch(coupon.type) {
          case couponType.POINT:
            $('#value-coupon').val(coupon.point);
            break;

          case couponType.DURATION:
            $('#value-coupon').val(coupon.time);
            break;

          case couponType.PERCENT:
            $('#value-coupon').val(coupon.percent);
            break;

          default:
            window.location.href = '/mypage';
        }

        $('#type-coupon').val(coupon.type);
        $('#name-coupon').val(coupon.name);

        if(coupon.max_point) {
          $('#max_point-coupon').val(coupon.max_point)
        } else {
          $('#max_point-coupon').val('');
        }

      } else {
        window.location = '/mypage';
      }
    } else {
      if(localStorage.getItem("order_params")){
        var orderParams = JSON.parse(localStorage.getItem("order_params"));
        if(orderParams.coupon) {
          helper.deleteLocalStorageValue('order_params','coupon');
        }
      }
    }

    priceCoupon(duration, time, helper, couponId);
  })
}

function handlerSelectedTransfer()
{
  var transfer = $("input:radio[name='transfer_order_nominate']");
  transfer.on("change",function(){
    var transfer = $("input:radio[name='transfer_order_nominate']:checked").val();

    var param = {
          payment_method : transfer,
        }

    helper.updateLocalStorageValue('order_params', param);

    if (OrderPaymentMethod.Direct_Payment == parseInt(transfer)) {
      $('#show-card-registered').css('display', 'none');
    }

    if (OrderPaymentMethod.Credit_Card == parseInt(transfer)) {
      $('#show-card-registered').css('display', 'block');

      if ($('.inactive-button-order').length) {
        $('#confirm-orders-nomination').addClass("disable");
        $('.checked-order').prop('checked', false);
        $('#confirm-orders-nomination').prop('disabled', true);
        $('#sp-cancel').addClass("sp-disable");
      }
    }
  })
}

$(document).ready(function(){
  $('body').on('change', ".checked-order",function(event){
    if ($(this).is(':checked')) {
        var time = $("input:radio[name='time_join_nomination']:checked").val();
        var area = $("input:radio[name='nomination_area']:checked").val();
        var duration = $("input:radio[name='time_set_nomination']:checked").val();
        var date = $('.sp-date').text();
        var cancel=$("input:checkbox[name='confrim_order_nomination']:checked").length;
        var otherArea = $("input:text[name='other_area_nomination']").val();

        var checkCard = $('.inactive-button-order').length;
        var transfer = $("input:radio[name='transfer_order_nominate']:checked").val();

        if(OrderPaymentMethod.Direct_Payment == transfer) {
          checkCard = false;
        }

        if((!area || (area=='その他' && !otherArea)) || !time ||
         (!duration || (duration<1 && 'other_time_set' != duration)) || (time=='other_time' && !date) 
         || checkCard) {

          $('#confirm-orders-nomination').addClass("disable");
          $(this).prop('checked', false);
          $('#confirm-orders-nomination').prop('disabled', true);
          $('#sp-cancel').addClass("sp-disable");
        } else {
          $('#confirm-orders-nomination').removeClass('disable');
          $(this).prop('checked', true);
          $('#sp-cancel').removeClass('sp-disable');

          window.axios.get('/api/v1/auth/me')
            .then(function(response) {
              var tempPoint = response.data['data'].point;

              $('#current-point').val(tempPoint);
              window.axios.get('/api/v1/guest/points_used')
                .then(function(response) {
                  var pointUsed = response.data['data'];
                  $('#point_used_nominate').val(pointUsed);
                  
                  $('#confirm-orders-nomination').prop('disabled', false);
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
        }
    } else {
        $(this).prop('checked', false);
        $('#confirm-orders-nomination').addClass("disable");
        $('#confirm-orders-nomination').prop('disabled', true);
        $('#sp-cancel').addClass("sp-disable");
    }
  });

  //textArea
  $('body').on('input', "input:text[name='other_area_nomination']", function(e) {
    var params = {
      text_area: $(this).val(),
    };
    
    helper.updateLocalStorageValue('order_params', params);

    var area = $("input:radio[name='nomination_area']:checked").val();

    if (!area || (!$(this).val())) {
      $('#confirm-orders-nomination').addClass("disable");
       $('.checked-order').prop('checked', false);
      $('#confirm-orders-nomination').prop('disabled', true);
      $('#sp-cancel').addClass("sp-disable");
    }
  });

  //area
  $('body').on('change', "input:radio[name='nomination_area']", function(){
    var areaNomination = $("input:radio[name='nomination_area']:checked").val();

    if('その他'== areaNomination){
      if(localStorage.getItem("order_params")){
        var orderParams = JSON.parse(localStorage.getItem("order_params"));

        if(orderParams.text_area){
          $("input:text[name='other_area_nomination']").val(orderParams.text_area);
        }
      }

      if (!$("input:text[name='other_area_nomination']").val()) {
        $('#confirm-orders-nomination').addClass("disable");
        $('.checked-order').prop('checked', false);
        $('#confirm-orders-nomination').prop('disabled', true);
        $('#sp-cancel').addClass("sp-disable");
      }
    }

    var params = {
      select_area: areaNomination,
    };

    helper.updateLocalStorageValue('order_params', params);
  })

  //duration
  $('body').on('change', ".input-duration",function(){
    if(localStorage.getItem("order_params")){
      var orderParams = JSON.parse(localStorage.getItem("order_params"));
      if(orderParams.coupon) {
        helper.deleteLocalStorageValue('order_params','coupon');
      }
    }

    var time = $("input:radio[name='time_join_nomination']:checked").val();
    var duration = $("input:radio[name='time_set_nomination']:checked").val();

    var params = {
      current_duration: duration,
    };

    helper.updateLocalStorageValue('order_params', params);

    if('other_time_set' == duration) {
      duration = $('.select-duration option:selected').val();
    }

    var cost = $('.cost-order').val();
    var totalPoint=cost*(duration*6)/3;

    cost = parseInt(cost).toLocaleString(undefined,{ minimumFractionDigits: 0 });

    $('.reservation-total__text').text('内訳：'+cost+ '(キャストP/30分)✖'+(duration)+'時間');

    priceCoupon(duration, time, helper, null);
  })

  $('body').on('change', ".select-duration",function(){
     if(localStorage.getItem("order_params")){
      var orderParams = JSON.parse(localStorage.getItem("order_params"));

      if(orderParams.coupon) {
        helper.deleteLocalStorageValue('order_params','coupon');
      }
    }

    var time = $("input:radio[name='time_join_nomination']:checked").val();
    var duration = $('.select-duration option:selected').val();

    var params = {
        select_duration: duration,
      };

    helper.updateLocalStorageValue('order_params', params);

    var cost = $('.cost-order').val();
    var totalPoint=cost*(duration*6)/3;

    cost = parseInt(cost).toLocaleString(undefined,{ minimumFractionDigits: 0 });

    $('.reservation-total__text').text('内訳：'+cost+ '(キャストP/30分)✖'+(duration)+'時間');

    priceCoupon(duration, time, helper, null);
  })

  //timejoin
  $('body').on('change', ".input-time-join",function(){
    var time = $("input:radio[name='time_join_nomination']:checked").val();
    var duration = $("input:radio[name='time_set_nomination']:checked").val();

    var updateTime = {
          current_time_set: time,
        };

    helper.updateLocalStorageValue('order_params', updateTime);

    if('other_time' == time) {
      if(localStorage.getItem("order_params")){
        var orderParams = JSON.parse(localStorage.getItem("order_params"));

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

      var couponID = null;
      if($('#coupon-order-nominate').length) {
        couponID = $('#coupon-order-nominate').val();
      }

      priceCoupon(duration, time, helper, couponID);
    }
  })

  //select-time order 1-1
  $('body').on('click', ".choose-time", function(){
    if ($("input:radio[name='time_set_nomination']:checked").length) {
      var time = $("input:radio[name='time_join_nomination']:checked").val();
      var duration = $("input:radio[name='time_set_nomination']:checked").val();

      if('other_time_set' == duration) {
        duration = $('.select-duration option:selected').val();
      }

      var couponID = null;
      if($('#coupon-order-nominate').length) {
        couponID = $('#coupon-order-nominate').val();
      }

      priceCoupon(duration, time, helper, couponID);
    }
  })

  $('#confirm-orders-nomination').on('click',function(){
    $('.modal-confirm-nominate').css('display','inline-block');
    $('#orders-nominate').prop('checked',true);
  });

  $('.cf-orders-nominate').on('click',function(){

    var checkCard = $('.inactive-button-order').length;
    var transfer = $("input:radio[name='transfer_order_nominate']:checked").val();

    if(OrderPaymentMethod.Direct_Payment == transfer) {
      checkCard = false;
    }

    if(checkCard) {
      if($('#md-require-card').length){
        $('#md-require-card').click();
      }
    } else {
      if(OrderPaymentMethod.Direct_Payment == transfer) {

        var tempPointOrders = parseInt($('#current-temp-point').val()) + parseInt($('#point_used_nominate').val());
        var pointUser = $('#current-point').val();
        
        if (parseInt(tempPointOrders) > parseInt(pointUser)) {
          $('.checked-order').prop('checked',false);
          $('#sp-cancel').addClass('sp-disable');
          $('#confirm-orders-nomination').prop('disabled', true);
          $('#confirm-orders-nomination').addClass('disable');
          // $('#orders-nominate').prop('checked',false);

          if (parseInt($('#point_used_nominate').val()) > parseInt(pointUser)) {
            var pointShow = parseInt($('#current-temp-point').val());
          } else {
            var pointShow = parseInt(tempPointOrders) - parseInt(pointUser);
          }
                

          window.location.href = '/payment/transfer?point=' + pointShow;

          return;
        }
      }

      $('.modal-confirm-nominate').css('display','none');
      $('#confirm-orders-nomination').prop('disabled','disabled');
      document.getElementById('confirm-order-nomination-submit').click();
      $('#create-nomination-form').submit();
    }
  });

  if ($('#create-nomination-form').length) {

    if(localStorage.getItem("order_params")){
      var orderParams = JSON.parse(localStorage.getItem("order_params"));

      if(orderParams.current_total_point){
          $('#current-temp-point').val(parseInt(orderParams.current_total_point));
          var currenttempPoint = parseInt(orderParams.current_total_point).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('.total-point').text(currenttempPoint + 'P~');
      }

      //payment

      if(orderParams.payment_method) {
        const inputTransfer = $("input:radio[name='transfer_order_nominate']");
        $.each(inputTransfer,function(index,val){
          if(val.value == parseInt(orderParams.payment_method)) {
            $(this).prop('checked',true);
          }
        })

        if (OrderPaymentMethod.Direct_Payment == parseInt(orderParams.payment_method)) {
          $('#show-card-registered').css('display', 'none');
        }
      }

      //duration
      var cost = $('.cost-order').val();
      if(orderParams.current_duration){
        if('other_time_set' == orderParams.current_duration) {
          if(orderParams.select_duration) {
          var chooseDuration = orderParams.select_duration;
          } else {
          var chooseDuration = 4;
          }
          
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
            $('.month-nomination').text(orderParams.current_month +'月');
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
                  $('.date-nomination').text(orderParams.current_date +'日');
                  var currentDate = parseInt(orderParams.current_date);
                  const inputDate = $('select[name=sl_date_nomination] option');

                  $.each(inputDate,function(index,val){
                    if(val.value == currentDate) {
                      $(this).prop('selected',true);
                    }
                  })
                }
    
                helper.loadShift();
              })
              .catch(function (error) {
                console.log(error);
                if (error.response.status == 401) {
                  window.location = '/login';
                }
              });

            const inputMonth = $('select[name=sl_month_nomination] option');
            $.each(inputMonth,function(index,val){
              if(val.value == month) {
                $(this).prop('selected',true);
              }
            })
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

      if(orderParams.prefecture_id){
        $('.select-prefecture-nomination').val(orderParams.prefecture_id);
        var params = {
          prefecture_id : orderParams.prefecture_id,
        };
        window.axios.get('/api/v1/municipalities', {params})
          .then(function(response) {
            var data = response.data;

            var municipalities = (data.data);
            html = '';
            municipalities.forEach(function (val) {
              name = val.name;
              html += '<label class="button button--green area">';
              html += '<input class="input-area" type="radio" name="nomination_area" value="'+ name +'">' + name +'</label>';
            })
            
            html += '<label id="area_input" class="button button--green area ">';
            html += '<input class="input-area" type="radio" name="nomination_area" value="その他">その他</label>';
            html += '<label class="area-input area-nomination"><span>希望エリア</span>';
            html += '<input type="text" id="other_area_nomination" placeholder="入力してください" name="other_area_nomination" value=""></label>';

            $('#list-municipalities-nomination').html(html);

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
          })
          .catch(function (error) {
            console.log(error);
            if (error.response.status == 401) {
              window.location = '/login';
            }
          });
      }
    } else {
      var params = {
          prefecture_id : $('.select-prefecture-nomination option:selected').val(),
        };
      helper.updateLocalStorageValue('order_params', params);
    }
  }

  if($("label").hasClass("status-code-nomination")){
    $('.status-code-nomination').click();
  }


  var selectedPrefectureNomination = $(".select-prefecture-nomination");
  selectedPrefectureNomination.on("change",function(){
    $(".checked-order").prop('checked', false);
    $('#confirm-orders-nomination').addClass("disable");
    $('#confirm-orders-nomination').prop('disabled', true);
    $('#sp-cancel').addClass("sp-disable");

    helper.deleteLocalStorageValue('order_params','select_area');
    helper.deleteLocalStorageValue('order_params','text_area');
    
    var params = {
      prefecture_id : this.value,
    };

    helper.updateLocalStorageValue('order_params', params);

    window.axios.get('/api/v1/municipalities', {params})
      .then(function(response) {
        var data = response.data;

        var municipalities = (data.data);
        html = '';
        municipalities.forEach(function (val) {
          name = val.name;
          html += '<label class="button button--green area">';
          html += '<input class="input-area" type="radio" name="nomination_area" value="'+ name +'">' + name +'</label>';
        })
        
        html += '<label id="area_input" class="button button--green area ">';
        html += '<input class="input-area" type="radio" name="nomination_area" value="その他">その他</label>';
        html += '<label class="area-input area-nomination"><span>希望エリア</span>';
        html += '<input type="text" id="other_area_nomination" placeholder="入力してください" name="other_area_nomination" value=""></label>';

        $('#list-municipalities-nomination').html(html);
      })
      .catch(function (error) {
        console.log(error);
        if (error.response.status == 401) {
          window.location = '/login';
        }
      });
  });

  if($('#show-coupon-order-nominate').length) {
    loadCouponsOrderNominate();
    selectedCouponsNominate(helper);
    helper.loadShift();
    handlerSelectedTransfer();
  } else {
    if(localStorage.getItem("shifts")){
      localStorage.removeItem("shifts");
    }
  }
})
