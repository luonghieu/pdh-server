const helper = require('./helper');
let coupons = [];
const couponType = {
  'POINT': 1,
  'DURATION': 2,
  'PERCENT': 3
};

function loadCouponsOrderCall()
{

  if(localStorage.getItem("order_call")) {
      var orderCall = JSON.parse(localStorage.getItem("order_call"));
      if(orderCall.current_duration) {
        var duration = orderCall.current_duration;

        if ('other_duration' == duration) {
          duration = 4;

          if(orderCall.select_duration) {
            duration = orderCall.select_duration;
          }
        }
      } else {
        var duration = $("input:radio[name='time_set']:checked").val();

        if(duration) {
          if ('other_duration' == duration) {
            duration = $('#select-duration-call option:selected').val();
          }
        } else {
          duration = null;
        }
      }
  } else {
    var duration = $("input:radio[name='time_set']:checked").val();

    if(duration) {
      if ('other_duration' == duration) {
        duration = $('#select-duration-call option:selected').val();
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
    coupons = response.data['data'];
    var html = '';
    if (coupons.length) {
      html += '<div class="reservation-item">';
      html += '<div class="caption">';
      html += '<h2>クーポン</h2> </div>';
      html += '<div class="form-grpup" >';
      html += '<select id="coupon-order">';
      html += '<option>クーポンを使用しない</option>';
      coupons.forEach(function (coupon) {
        var id = coupon.id;
        var name = coupon.name;
        html += '<option value="'+ id +'">'+ name +'</option>';
      })

      html += '</select>';
      html += '<p class = "max-point-coupon" > ※割引されるポイントは最大10,000Pになります。</p> </div>';
    }

    $('#show-coupon-order-call').html(html);
  }).catch(function(error) {
    console.log(error);
    if (error.response.status == 401) {
      window.location = '/login';
    }
  });
}

function handlerSelectedArea()
{
  $('body').on('change', ".button--green.area",function(){

    if ($("input:radio[name='area']").length) {
      var areaCall = $("input:radio[name='area']:checked").val();

      if('その他'== areaCall){
        if(localStorage.getItem("order_call")){
          var orderCall = JSON.parse(localStorage.getItem("order_call"));

          if(orderCall.text_area){
            $("input:text[name='other_area']").val(orderCall.text_area);
          }
        }
      }

      var params = {
        select_area: areaCall,
      };

      helper.updateLocalStorageValue('order_call', params);
    }

    $("#ge2-1-x input:radio[name='area']").parent().removeClass("active");
    $("#ge2-1-x input:radio[name='area']:checked").parent().addClass("active");

    var area = $("input:radio[name='area']:checked").val();
    var otherArea = $("input:text[name='other_area']").val();
    var time = $("input:radio[name='time_join']:checked").val();
    var castClass = $("input:radio[name='cast_class']:checked").val();
    var duration = $("input:radio[name='time_set']:checked").val();
    var totalCast = $("input[type='text'][name='txtCast_Number']").val();
    var date = $('.sp-date').text();

    if((!area || (area=='その他' && !otherArea)) || !time || !castClass ||
     (!duration || (duration<1 && 'other_duration' != duration)) ||(!totalCast || totalCast<1) || (time=='other_time' && !date)) {
      $("#step1-create-call").addClass("disable");
      $("#step1-create-call").prop('disabled', true);
    } else {
      $("#step1-create-call").removeClass('disable');
      $("#step1-create-call").prop('disabled', false);
    }
  });
}

function handlerCustomArea()
{
  $('body').on('input', "input:text[name='other_area']",function(){
    //text-area
    var params = {
      text_area: $(this).val(),
    };

    helper.updateLocalStorageValue('order_call', params);

    var otherArea = $(this).val();
    var time = $("input:radio[name='time_join']:checked").val();
    var area = $("input:radio[name='area']:checked").val();
    var castClass = $("input:radio[name='cast_class']:checked").val();
    var duration = $("input:radio[name='time_set']:checked").val();
    var totalCast = $("input[type='text'][name='txtCast_Number']").val();
    var date = $('.sp-date').text();

    if( !time || (!area || (!otherArea)) || !castClass ||
     (!duration || (duration<1 && 'other_duration' != duration)) ||(!totalCast || totalCast<1) || (time=='other_time' && !date)) {
      $("#step1-create-call").addClass("disable");
      $("#step1-create-call").prop('disabled', true);
    } else {
      $("#step1-create-call").removeClass('disable');
      $("#step1-create-call").prop('disabled', false);
    }
  })
}

function handlerSelectedTime()
{
  var dateButton = $(".button--green.date");
  dateButton.on("change",function(){
    var time = $("input:radio[name='time_join']:checked").val();

    if ($("input:radio[name='time_join']").length) {
      var updateTime = {
            current_time_set: time,
          };

      helper.updateLocalStorageValue('order_call', updateTime);
    }

    $("#ge2-1-x input:radio[name='time_join']").parent().removeClass("active");
    $("#ge2-1-x input:radio[name='time_join']:checked").parent().addClass("active");

    var area = $("input:radio[name='area']:checked").val();
    var otherArea = $("input:text[name='other_area']").val();
    var castClass = $("input:radio[name='cast_class']:checked").val();
    var duration = $("input:radio[name='time_set']:checked").val();
    var totalCast = $("input[type='text'][name='txtCast_Number']").val();
    var date = $('.sp-date').text();

    if((!area || (area=='その他' && !otherArea)) || !castClass ||
     (!duration || (duration<1 && 'other_duration' != duration)) ||(!totalCast || totalCast<1) || (time=='other_time' && !date)) {
      $("#step1-create-call").addClass("disable");
      $("#step1-create-call").prop('disabled', true);
    } else {
      $("#step1-create-call").removeClass('disable');
      $("#step1-create-call").prop('disabled', false);
    }
  })

  $('.select-month').on('change', function (e) {
  var month = $(this).val();
  window.axios.post('/api/v1/get_day', {month})
    .then(function(response) {
      var html = '';
      Object.keys(response.data).forEach(function (key) {
        if(key!='debug') {
        html +='<option value="'+key+'">'+ response.data[key] +'</option>';
        }
      })
    $('.select-date').html(html);
    })
    .catch(function (error) {
      console.log(error);
      if (error.response.status == 401) {
        window.location = '/login';
      }
    });
  });

  $(".date-select__ok").on("click",function(){
    var month = $('.select-month').val();
    var date = $('.select-date').val();

    if ($('.select-hour').val() <10) {
      var hour = '0'+$('.select-hour').val();
    }else {
      var hour = $('.select-hour').val();
    }

    if ($('.select-minute').val() <10) {
      var minute = '0'+$('.select-minute').val();
    }else {
      var minute = $('.select-minute').val();
    }


    var currentDate = new Date();
    utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
    nd = new Date(utc + (3600000*9));

    var year = nd.getFullYear();
    var checkMonth = nd.getMonth();

    var app = {
      isAppleDevice : function() {
        if (navigator.userAgent.match(/(iPhone|iPod|iPad)/) != null) {
          return true;
        }

        return false;
      }
    };

    if (app.isAppleDevice()) {
      var selectDate = new Date(month +'/' + date +'/'+ year +' ' + hour +':' + minute);
    } else {
      var selectDate = new Date(year +'-' + month +'-'+ date +' ' + hour +':' + minute);
    }


    if (month > checkMonth) {
      if(helper.add_minutes(nd, 30) > selectDate) {
        selectDate = helper.add_minutes(nd, 30);
        date = selectDate.getDate();
        month = selectDate.getMonth() +1;

        hour = selectDate.getHours();
        if(hour<10) {
          hour = '0' +hour;
        }

        minute = selectDate.getMinutes();
        if(minute<10) {
          minute = '0' +minute;
        }

        $('.select-month').val(month);
        $('.select-date').val(date);
        $('.select-hour').val(selectDate.getHours());
        $('.select-minute').val(selectDate.getMinutes());
      }
    } else {
      year +=1;
    }

    var time = hour +':' +minute;
    
    if (month <10) {
      month = '0'+month;
    }

    if (date <10) {
      date = '0'+date;
    }

    if($("input:radio[name='area']").length) {
      var params = {
            current_date: year + '-' + month + '-' +date,
            current_time : time,
          };

      helper.updateLocalStorageValue('order_call', params);
    }

    $('.sp-date').text(date +'日');
    $('.sp-month').text(month +'月');
    $('.sp-time').text(time);


    var area = $("input:radio[name='area']:checked").val();
    var otherArea = $("input:text[name='other_area']").val();
    var castClass = $("input:radio[name='cast_class']:checked").val();
    var duration = $("input:radio[name='time_set']:checked").val();
    var totalCast = $("input[type='text'][name='txtCast_Number']").val();
    var date = $('.sp-date').text();

    if((!area || (area=='その他' && !otherArea)) || !castClass ||
     (!duration || (duration<1 && 'other_duration' != duration)) ||(!totalCast || totalCast<1) || !date) {
      $("#step1-create-call").addClass("disable");
      $("#step1-create-call").prop('disabled', true);
    } else {
      $("#step1-create-call").removeClass('disable');
      $("#step1-create-call").prop('disabled', false);
    }

    $(".overlay").fadeOut();
  });

  //select-time order 1-1
  $('.choose-time').on("click",function(){
    var cost = $('.cost-order').val();
    var time = $("input:radio[name='time_join_nomination']:checked").val();
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
            window.location = '/login';
          }
      });
    }
  })
}
  
function handlerSelectedDuration()
{
  var timeButton = $(".button--green.time");
  timeButton.on("change",function(){
    var duration = $("input:radio[name='time_set']:checked").val();

    if ($("input:radio[name='time_set']").length) {
      var params = {
        current_duration: duration,
      };

      helper.updateLocalStorageValue('order_call', params);
    }

    $("#ge2-1-x input:radio[name='time_set']").parent().removeClass("active");
    $("#ge2-1-x input:radio[name='time_set']:checked").parent().addClass("active");

    var area = $("input:radio[name='area']:checked").val();
    var otherArea = $("input:text[name='other_area']").val();
    var castClass = $("input:radio[name='cast_class']:checked").val();
    var totalCast = $("input[type='text'][name='txtCast_Number']").val();
    var time = $("input:radio[name='time_join']:checked").val();
    var date = $('.sp-date').text();

    if( !time || (!area || (area=='その他' && !otherArea)) ||
     !castClass || (!duration || (duration<1 && 'other_duration' != duration)) ||(!totalCast || totalCast<1) || (time=='other_time' && !date)) {
      $("#step1-create-call").addClass("disable");
      $("#step1-create-call").prop('disabled', true);
    } else {
      $("#step1-create-call").removeClass('disable');
      $("#step1-create-call").prop('disabled', false);
    }


    //show coupon 
   
    if ('other_duration' == duration) {
      duration = $('#select-duration-call option:selected').val();
    }

    var paramCoupon = {
      duration : duration,
    };

    window.axios.get('/api/v1/coupons', {params: paramCoupon})
    .then(function(response) {
      coupons = response.data['data'];
      var html = '';

      if (coupons.length) {
        html += '<div class="reservation-item">';
        html += '<div class="caption">';
        html += '<h2>クーポン</h2> </div>';
        html += '<div class="form-grpup" >';
        html += '<select id="coupon-order">';
        html += '<option value="">クーポンを使用しない</option>';

        coupons.forEach(function (coupon) {
          var id = coupon.id;
          var name = coupon.name;
          html += '<option value="'+ id +'">'+ name +'</option>';
        })

        html += '</select>';
        html += '<p class = "max-point-coupon" > ※割引されるポイントは最大10,000Pになります。</p> </div>';
      }


      $('#show-coupon-order-call').html(html);
    }).catch(function(error) {
      console.log(error);
      if (error.response.status == 401) {
        window.location = '/login';
      }
    });
  })

  //select-duration 
  $('#select-duration-call').on("change",function(){
    var duration = $('#select-duration-call option:selected').val();

    var params = {
        select_duration: duration,
      };

    helper.updateLocalStorageValue('order_call', params);

    //show coupon 

    var paramCoupon = {
      duration : duration,
    };

    window.axios.get('/api/v1/coupons', {params: paramCoupon})
    .then(function(response) {
      coupons = response.data['data'];
      var html = '';

      if (coupons.length) {
        html += '<div class="reservation-item">';
        html += '<div class="caption">';
        html += '<h2>クーポン</h2> </div>';
        html += '<div class="form-grpup" > ';
        html += '<select id="coupon-order">';
        html += '<option value="">クーポンを使用しない</option>';

        coupons.forEach(function (coupon) {
          var id = coupon.id;
          var name = coupon.name;
          html += '<option value="'+ id +'">'+ name +'</option>';
        })

        html += '</select>';
        html += '<p class = "max-point-coupon" > ※割引されるポイントは最大10,000Pになります。</p> </div>';
      }

      $('#show-coupon-order-call').html(html);
    }).catch(function(error) {
      console.log(error);
      if (error.response.status == 401) {
        window.location = '/login';
      }
    });
  })
}

function handlerSelectedCastClass()
{
  var castClass = $("input:radio[name='cast_class']");
  castClass.on("change",function(){
    var castClass = $("input:radio[name='cast_class']:checked").val();

    var className = $("input:radio[name='cast_class']:checked").data('name');

    var params = {
      cast_class: castClass,
      class_name: className,
    };

    helper.updateLocalStorageValue('order_call', params);
    
    if (castClass == 3) {
      $('.notify-campaign-over-cast-class span').text('※”ダイヤモンド”はキャンペーン対象外です');
      $('.notify-campaign-over-cast-class').css('display','block');
    }

    if (castClass == 2) {
      $('.notify-campaign-over-cast-class span').text('※”プラチナ”はキャンペーン対象外です');
      $('.notify-campaign-over-cast-class').css('display','block');
    }

    if (castClass == 1) {
      $('.notify-campaign-over-cast-class').css('display','none');
    }

    var area = $("input:radio[name='area']:checked").val();
    var otherArea = $("input:text[name='other_area']").val();
    var duration = $("input:radio[name='time_set']:checked").val();
    var totalCast = $("input[type='text'][name='txtCast_Number']").val();
    var time = $("input:radio[name='time_join']:checked").val();
    var date = $('.sp-date').text();

    if( !time || (!area || (area=='その他' && !otherArea)) || !castClass ||
     (!duration || (duration<1 && 'other_duration' != duration)) ||(!totalCast || totalCast<1) || (time=='other_time' && !date)) {
      $("#step1-create-call").addClass("disable");
      $("#step1-create-call").prop('disabled', true);
    } else {
      $("#step1-create-call").removeClass('disable');
      $("#step1-create-call").prop('disabled', false);
    }
  })
}

function handlerNumberCasts()
{
  $(".cast-number__button-plus").on("click",function(){
    var number_val = parseInt( $(".cast-number__value input").val());

    if(number_val>=1) {
      $(".cast-number__button-minus").addClass('active');
      $(".cast-number__button-minus").css({"border": "1.5px #00c3c3 solid"});
      $(".cast-number__button-minus").prop('disabled', false);
    }

    if(number_val == 2 ) {
      $('.notify-campaign-over span').text('※3名はキャンペーン対象外です');
      $('.notify-campaign-over').css('display','block');
    }

    if (number_val >= 3 ) {
      $('.notify-campaign-over span').text('※4名はキャンペーン対象外です');
      $('.notify-campaign-over').css('display','block');
    }

    if(number_val==(maxCasts-1)){
      $(this).css({"border": "1.5px #cccccc solid"});
      $(this).addClass('active');
    }

    if(number_val>=maxCasts) {
      $(this).attr("disabled", "disabled");
    }else {
      number_val = number_val + 1;
      $(".cast-number__value input").val(number_val);
    }

    var params = {
      countIds : number_val,
    };

    helper.updateLocalStorageValue('order_call', params);

  })

  $(".cast-number__button-minus").on("click",function(){
    var number_val = parseInt( $(".cast-number__value input").val());
    if(number_val ==1) {
      $(this).removeClass('active');
      $(this).attr("disabled", "disabled");
      $(this).css({"border": "1.5px #cccccc solid"});
    } else {
      $(".cast-number__button-plus").prop('disabled', false);
    }

    if (number_val == 4 ) {
      $('.notify-campaign-over span').text('※3名はキャンペーン対象外です');
    }

    if(number_val < 4 ) {
      $('.notify-campaign-over').css('display','none');
    }

    if(number_val>0 && number_val !=1) {
      if(number_val==2) {
        $(this).removeClass('active');
        $(this).css({"border": "1.5px #cccccc solid"});
      }
      number_val = number_val - 1;
      $(".cast-number__button-plus").removeClass('active');
      $(".cast-number__button-plus").css({"border": "1.5px #00c3c3 solid"});
      $(".cast-number__value input").val(number_val)
    }

    var params = {
      countIds : number_val,
    };

    helper.updateLocalStorageValue('order_call', params);
  })

  var checkNumber = parseInt( $(".cast-number__value input").val());
  var maxCasts = parseInt( $("#max_casts").val());

  if(!maxCasts) {
    maxCasts =10;
  }

  if (checkNumber > 2) {
    if (checkNumber == 3) {
      $('.notify-campaign-over span').text('※3名はキャンペーン対象外です');
    }

    if (checkNumber == 4) {
      $('.notify-campaign-over span').text('※4名はキャンペーン対象外です');
    }

    $('.notify-campaign-over').css('display','block');
  }

  if (checkNumber>1) {
    if (checkNumber==maxCasts) {
      $(".cast-number__button-plus").prop('disabled', false);
      $(".cast-number__button-plus").css({"border": "1.5px #cccccc solid"});
      $(".cast-number__button-plus").addClass('active');
    }

    $(".cast-number__button-minus").addClass('active');
    $(".cast-number__button-minus").css({"border": "1.5px #00c3c3 solid"});
    $(".cast-number__button-minus").prop('disabled', false);
  }
}

function handlerSelectedPrefecture()
{
  var selectedPrefecture = $(".select-prefecture");
  selectedPrefecture.on("change",function(){
    $("#step1-create-call").addClass("disable");
    $("#step1-create-call").prop('disabled', true);

    helper.deleteLocalStorageValue('order_call','select_area');
    helper.deleteLocalStorageValue('order_call','text_area');

    var params = {
      prefecture_id : this.value,
    };

    helper.updateLocalStorageValue('order_call', params);

    window.axios.get('/api/v1/municipalities', {params})
      .then(function(response) {
        var data = response.data;

        var municipalities = (data.data);
        html = '';
        municipalities.forEach(function (val) {
          name = val.name;
          html += '<label class="button button--green area">';
          html += '<input type="radio" name="area" value="'+ name +'">' + name +'</label>';
        })
        
        html += '<label id="area_input" class="button button--green area">';
        html += '<input type="radio" name="area" value="その他">その他 </label>';
        html += '<label class="area-input area-call"> <span>希望エリア</span>';
        html += '<input type="text" placeholder="入力してください" name="other_area" value=""> </label>';

        $('#list-municipalities').html(html);
      })
      .catch(function (error) {
        console.log(error);
        if (error.response.status == 401) {
          window.location = '/login';
        }
      });
  });
}

function handleStepOne()
{
  $('body').on('click', "#step1-create-call",function(){
    if(localStorage.getItem("order_call")) {
      var orderCall = JSON.parse(localStorage.getItem("order_call"));
      if(!orderCall.countIds) {
        var number_val = parseInt( $(".cast-number__value input").val());

        var params = {
          countIds: number_val,
        };

        helper.updateLocalStorageValue('order_call', params);
      }

      if (!orderCall.current_time_set) {
        var timeJoin = $("input:radio[name='time_join']:checked").val()
        var params = {
          current_time_set: timeJoin,
        };

        helper.updateLocalStorageValue('order_call', params);
      }

      if (!orderCall.select_duration) {
        var duration = $('#select-duration-call option:selected').val();
        var params = {
          select_duration: duration,
        };

        helper.updateLocalStorageValue('order_call', params);
      }

      if($('#coupon-order').length) {
        var couponId = parseInt($('#coupon-order').val());
        
        if(couponId) {
          if(!coupons.length) {
            window.location = '/mypage';
          }

          var couponIds = coupons.map(function (e) {
            return e.id; 
          });

          if(couponIds.indexOf(couponId) > -1) {
            var coupon = {};
            coupons.forEach(function (e) {
              if(e.id == couponId) {
                coupon = e;
              }
            });
          }

          if(coupon) {
            var params = {
              coupon: coupon,
            };

            helper.updateLocalStorageValue('order_call', params);
          }
        }
      } else {
        if (orderCall.coupon) {
          helper.deleteLocalStorageValue('order_call','coupon');
        }
      }

    } else {
      window.location = '/mypage';
    }
  })
}

$(document).ready(function () {
  handlerSelectedArea();
  handlerCustomArea();
  handlerSelectedTime();
  handlerSelectedDuration();
  handlerSelectedCastClass();
  handlerNumberCasts();
  handlerSelectedPrefecture();

  if($('#step1-create-call').length) {
    loadCouponsOrderCall();
    handleStepOne();
  }

});
