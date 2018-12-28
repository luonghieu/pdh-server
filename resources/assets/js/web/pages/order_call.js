$(document).ready(function(){
  const helper = require('./helper');
  if($("#ge2-1-x input:radio[name='cast_class']:checked").length){
    $("#ge2-1-x input:radio[name='cast_class']:checked").parent().addClass("active");
    var castClass = $("#ge2-1-x input:radio[name='cast_class']:checked").val();
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

  }

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
          window.location = '/login/line';
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
    var year = currentDate.getFullYear();

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

    utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
    nd = new Date(utc + (3600000*9));

    var checkMonth = currentDate.getMonth();

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

  $(".form-grpup .checkbox-tags").on("change",function(event){
    var tagName = $(this).children().val();
    var activeSum = $(".active").length;
    if ($(this).hasClass("active")) {
      $(this).children().prop('checked',false);
      $(this).removeClass('active');

      if(localStorage.getItem("order_call")){
        var tags = JSON.parse(localStorage.getItem("order_call")).tags;
        if(tags) {
          if(tags.indexOf(tagName) > -1) {
            tags.splice(tags.indexOf(tagName), 1);
          }

          var params = {
            tags: tags,
          };

        }
      }
    } else {
      if(activeSum >= 5) {
        $('#max-tags').prop('checked', true);
        $(this).children().prop('checked',false);
        $(this).removeClass('active');
      } else {
        $(this).children().prop('checked',true);
        $(this).addClass('active');

        if(localStorage.getItem("order_call")){
          var tags = JSON.parse(localStorage.getItem("order_call")).tags;
          if(tags) {
            tags.push(tagName);

            var params = {
              tags: tags,
            };
            
          } else {
            var tags = [tagName];
            var params = {
              tags: tags,
            };
          }
        } else {
          var tags = [tagName];
          var params = {
              tags: tags,
            };
        }
      }
    }

    if(params) {
      helper.updateLocalStorageValue('order_call', params);
    }
  });

  $('#list-cast-order').on("change", ".cast_block .select-casts", function(event){
    var id = $(this).val();
    var countIds = JSON.parse(localStorage.getItem("order_call")).countIds;
    if($('.select-casts:checked').length > countIds) {
      var text = ' 指名できるキャストは'+ countIds + '名です';
      $('#content-message h2').text(text);
      $('#max-cast').prop('checked', true);
      $(this).prop('checked',false);
    }else {
      if ($(this).is(':checked')) {
        if(localStorage.getItem("order_call")){
          var arrIds = JSON.parse(localStorage.getItem("order_call")).arrIds;
          if (arrIds) {
            if(arrIds.length < countIds) {
              arrIds.push(id);
              var params = {
                  arrIds: arrIds
                };

              $(this).prop('checked',true);
              $(this).parent().find('.cast-link').addClass('cast-detail');
              $('.label-select-casts[for='+  id  +']').text('指名中');
            } else {
              var text = ' 指名できるキャストは'+ countIds + '名です';
              $('#content-message h2').text(text);
              $('#max-cast').prop('checked', true);
              $(this).prop('checked',false);
            }

            if(arrIds.length) {
              $('#sb-select-casts a').text('次に進む(3/4)');
            } else {
              $('#sb-select-casts a').text('指名せずに進む(3/4)');
            }

          } else {
            var arrIds = [id];

            var params = {
                arrIds: arrIds
              };

            $(this).prop('checked',true);
            $(this).parent().find('.cast-link').addClass('cast-detail');
            $('.label-select-casts[for='+  id  +']').text('指名中');
            $('#sb-select-casts a').text('次に進む(3/4)');
          }
        } else {
          var arrIds = [id];

          var params = {
              arrIds: arrIds
            };
        }
      } else {
        if(localStorage.getItem("order_call")){
          var arrIds = JSON.parse(localStorage.getItem("order_call")).arrIds;
          if(arrIds) {
            if(arrIds.indexOf(id) > -1) {
              arrIds.splice(arrIds.indexOf(id), 1);
            }

            var params = {
              arrIds: arrIds,
            }

            if(arrIds.length) {
              $('#sb-select-casts a').text('次に進む(3/4)');
            } else {
              $('#sb-select-casts a').text('指名せずに進む(3/4)');
            }
          }
        }

        $(this).prop('checked',false);
        $(this).parent().find('.cast-link').removeClass('cast-detail');
        $('.label-select-casts[for='+  id  +']').text('指名する');
      }
    }

    if(params) {
      helper.updateLocalStorageValue('order_call', params);
      $(".cast-ids").val(arrIds.toString());
    }
  });

  $("#cast-order-call a").on("click",function(event){
    var id = $('#cast-id-info').val();
    if(localStorage.getItem("order_call")){
      var arrIds = JSON.parse(localStorage.getItem("order_call")).arrIds;
      var countIds = JSON.parse(localStorage.getItem("order_call")).countIds;
      if(arrIds) {
        if(arrIds.length < countIds) {
          if(arrIds.indexOf(id) < 0) {
            arrIds.push(id);

            var params = {
              arrIds: arrIds,
            };
          }
        } else {
          if(arrIds.indexOf(id) < 0) {
            localStorage.setItem('full',true);
          }
        }
      } else {
        var arrIds = [];
        arrIds.push(id);

        var params = {
            arrIds: arrIds
          };
      }
    }

    if(params) {
      helper.updateLocalStorageValue('order_call', params);
    }
  })

  $(".cb-cancel").on("change",function(event){
    if ($(this).is(':checked')) {
        $(this).prop('checked', true);
        $('#sp-cancel').removeClass('sp-disable');
        $('#btn-confirm-orders').removeClass('disable');
        $('#btn-confirm-orders').prop('disabled', false);
      } else {
        $(this).prop('checked', false);
        $('#sp-cancel').addClass("sp-disable");
        $('#btn-confirm-orders').addClass("disable");
        $('#btn-confirm-orders').prop('disabled', true);
      }
  });

  if ($(".cb-cancel").is(':checked')) {
      $(this).prop('checked', true);
      $('#sp-cancel').removeClass('sp-disable');
      $('#btn-confirm-orders').removeClass('disable');
      $('#btn-confirm-orders').prop('disabled', false);
    } else {
      $(this).prop('checked', false);
      $('#sp-cancel').addClass("sp-disable");
      $('#btn-confirm-orders').addClass("disable");
      $('#btn-confirm-orders').prop('disabled', true);
    }

  $('#btn-confirm-orders').on('click',function(){
    $('.lb-orders').click();
  });

  $('.order-done').on('click',function(){
    window.location.href = '/mypage';
  });

  $('.lable-register-card').on('click',function(){
    window.location.href = '/credit_card';
  });

//area
  var buttonGreen = $(".button--green.area");
  buttonGreen.on("change",function(){

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

  var txtArea = $("input:text[name='other_area']");
  txtArea.on("input",function(){
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

  //duration
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

  })

  //select-duration 
  
  $('#select-duration-call').on("change",function(){
    var duration = $('#select-duration-call option:selected').val();

    var params = {
        select_duration: duration,
      };

    helper.updateLocalStorageValue('order_call', params);
  })

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

  if($("label").hasClass("status-code")){
    $('.status-code').click();
  }

  $('.checked-order').prop('checked',false);

  $("#step1-create-call").on("click",function(){
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
    }
  })

  if(localStorage.getItem("order_call")){
    var orderCall = JSON.parse(localStorage.getItem("order_call"));
    var arrIds = JSON.parse(localStorage.getItem("order_call")).arrIds;

    if($('.tags-name').length) {
      if(orderCall.tags) {
        var tags = orderCall.tags;
        const inputTags = $("#ge2-1-x .form-grpup .tags-name");

        $.each(inputTags,function(index,val){
          if (tags.indexOf(val.value) > -1) {
            $(this).prop('checked', true);
            $(this).parent().addClass('active');
          }
        })
      }

      if(orderCall.cast_class){
        $('.cast-class-id').val(orderCall.cast_class);
      }
    }

    if($("#step1-create-call").length) {

      //cast-number
      if(orderCall.countIds) {
        $('#cast-number-call').val(orderCall.countIds);
      } else {
        $('#cast-number-call').val(1);
      }

      if(orderCall.cast_class) {
        const castClass = $('.grade-radio');
        $.each(castClass,function(index,val){
          if (val.value == orderCall.cast_class) {
            $(this).prop('checked', true);
          }
        })
      }

        //area
      if(orderCall.select_area){
        const inputArea = $("#ge2-1-x input:radio[name='area']");
        if('その他'== orderCall.select_area){
          $('.area-call').css('display', 'flex')
          $("input:text[name='other_area']").val(orderCall.text_area);
        }

        $.each(inputArea,function(index,val){
          if (val.value == orderCall.select_area) {
            $(this).prop('checked', true);
            $(this).parent().addClass('active');
          }
        })
      }

      //duration     
      if(orderCall.current_duration){
        const inputDuration = $("input[name=time_set]");
        $.each(inputDuration,function(index,val){
          if (val.value == orderCall.current_duration) {
            $(this).prop('checked', true);
            $(this).parent().addClass('active');
          }
        })

        if('other_duration' == orderCall.current_duration) {
          $('.time-input').css('display','flex');
        }

        if (orderCall.select_duration) {
          const selectDuration = $('select[name=sl_duration] option');
          $.each(selectDuration,function(index,val){
            if(val.value == orderCall.select_duration) {
              $(this).prop('selected',true);
            }
          })
        }
      }

      //current_time_set
      if(orderCall.current_time_set){
        $(".time-join-call").parent().removeClass('active');

        if('other_time'== orderCall.current_time_set){
          $('.date-input-call').css('display', 'flex')

          if(orderCall.current_date){
            var day = orderCall.current_date;
            day = day.split('-');

            var time = orderCall.current_time;
            $('.time-call').text(time);
            time = time.split(':');
            
            var month = day[1];
            var date = day[2];
            var hour = time[0];
            var minute = time[1];
            $('.month-call').text(month +'月');
            $('.date-call').text(date +'日');
            
            month = parseInt(month);

            window.axios.post('/api/v1/get_day', {month})
              .then(function(response) {
                var html = '';
                Object.keys(response.data).forEach(function (key) {
                  if(key!='debug') {
                  html +='<option value="'+key+'">'+ response.data[key] +'</option>';
                  }
                })
              $('.select-date').html(html);

              const inputDate = $('select[name=sl_date] option');

              $.each(inputDate,function(index,val){
                if(val.value == parseInt(date)) {
                  $(this).prop('selected',true);
                }
              })
              })
              .catch(function (error) {
                console.log(error);
                if (error.response.status == 401) {
                  window.location = '/login/line';
                }
              });

            const inputMonth = $('select[name=sl_month] option');
            $.each(inputMonth,function(index,val){
              if(val.value == month) {
                $(this).prop('selected',true);
              }
            })
          }

          const inputHour = $('select[name=sl_hour] option');
          $.each(inputHour,function(index,val){
            if(val.value == parseInt(hour)) {
              $(this).prop('selected',true);
            }
          })

          const inputMinute = $('select[name=sl_minute] option');
          $.each(inputMinute,function(index,val){
            if(val.value == parseInt(minute)) {
              $(this).prop('selected',true);
            }
          })
        }

        const inputTimeSet = $(".time-join-call");
        $.each(inputTimeSet,function(index,val){
          if (val.value == orderCall.current_time_set) {
            $(this).prop('checked', true);
            $(this).parent().addClass('active');
          }
        })
      }

      var area = $("input:radio[name='area']:checked").val();
      var otherArea = $("input:text[name='other_area']").val();
      var time = $("input:radio[name='time_join']:checked").val();
      var castClass = $("input:radio[name='cast_class']:checked").val();
      var duration = $("input:radio[name='time_set']:checked").val();

      if((area || (area=='その他' && otherArea)) && time && castClass && duration) {
        $("#step1-create-call").removeClass('disable');
        $("#step1-create-call").prop('disabled', false);
      }

      if(arrIds) {
        var input = {
          arrIds: [],
        };
        helper.updateLocalStorageValue('order_call', input);
      }

      var tags = JSON.parse(localStorage.getItem("order_call")).tags;
      if(tags) {
        var params = {
          tags: [],
        };
        helper.updateLocalStorageValue('order_call', params);
      }
    }
  }

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
});
