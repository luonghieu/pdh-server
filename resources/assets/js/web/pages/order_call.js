$(document).ready(function(){
  const helper = require('./helper');
  if($("#ge2-1-x input:radio[name='area']:checked").length){
    $("#ge2-1-x input:radio[name='area']:checked").parent().addClass("active");
  }

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

  if($("#ge2-1-x input:radio[name='time_set']:checked").length){
    $("#ge2-1-x input:radio[name='time_set']:checked").parent().addClass("active");
  }

  if($("#ge2-1-x input:radio[name='time_join']:checked").length){
    $("#ge2-1-x input:radio[name='time_join']:checked").parent().addClass("active");
  }

  if($("#ge2-1-x .form-grpup .checkbox-tags input:checkbox[name='desires[]']:checked").length){
    const checkedDesires = $("#ge2-1-x .form-grpup .checkbox-tags input:checkbox[name='desires[]']:checked");
    $.each(checkedDesires,function(index,val){
      $(this).parent().addClass('active');
    })
  }

  if($("#ge2-1-x .form-grpup .checkbox-tags input:checkbox[name='situations[]']:checked").length){
    const checkedSituations = $("#ge2-1-x .form-grpup .checkbox-tags input:checkbox[name='situations[]']:checked");
    $.each(checkedSituations,function(index,val){
      $(this).parent().addClass('active');
    })
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

    var time = hour +':' +minute;

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


    var add_minutes =  function (dt, minutes) {
        return new Date(dt.getTime() + minutes*60000);
    }

    utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
    nd = new Date(utc + (3600000*9));

    var checkMonth = currentDate.getMonth();

    if (month > checkMonth) {
      if(add_minutes(nd, 30) > selectDate) {
        selectDate = add_minutes(nd, 30);
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

        time = hour + ':' + minute;
        $('.select-month').val(month);
        $('.select-date').val(date);
        $('.select-hour').val(selectDate.getHours());
        $('.select-minute').val(selectDate.getMinutes());
      }
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
      $("button[type='submit'][name='sb_create']").addClass("disable");
      $("button[type='submit'][name='sb_create']").prop('disabled', true);
    } else {
      $("button[type='submit'][name='sb_create']").removeClass('disable');
      $("button[type='submit'][name='sb_create']").prop('disabled', false);
    }

    $(".overlay").fadeOut();
  });

  $(".form-grpup .checkbox-tags").on("change",function(event){
    var activeSum = $(".active").length;
    if ($(this).hasClass("active")) {
      $(this).children().prop('checked',false);
      $(this).removeClass('active');
    } else {
      if(activeSum >= 5) {
        $('#max-tags').prop('checked', true);
        $(this).children().prop('checked',false);
        $(this).removeClass('active');
      } else {
        $(this).children().prop('checked',true);
        $(this).addClass('active');
      }
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
            $('#sb-select-casts a').text('希望リクエストせずに進む(3/4)');
          }

          } else {
            var arrIds = [];
            arrIds.push(id);

            var params = {
                arrIds: arrIds
              };

            $(this).prop('checked',true);
            $(this).parent().find('.cast-link').addClass('cast-detail');
            $('.label-select-casts[for='+  id  +']').text('指名中');
            $('#sb-select-casts a').text('次に進む(3/4)');
          }
        } else {
          var arrIds = [];
          arrIds.push(id);

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
              $('#sb-select-casts a').text('希望リクエストせずに進む(3/4)');
            }
          }
        }

        $(this).prop('checked',false);
        $(this).parent().find('.cast-link').removeClass('cast-detail');
        $('.label-select-casts[for='+  id  +']').text('リクエストする');
      }
    }

    if(params) {
      helper.updateLocalStorageValue('order_call', params);
      $(".cast-ids").val(arrIds.toString());
    }
  });

  if($('.cast-numbers').length){
    var ids = $('.cast-numbers').val();
    var params = {
          countIds: ids,
        };
    helper.updateLocalStorageValue('order_call', params);
  }

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

  $('.sb-form-orders').on('click',function(){
    if($('#md-require-card').length){
      $('#md-require-card').click();
    }else {
      document.getElementById('confirm-order-submit').click();
      $('#add-orders').submit();
    }
  });

  $('.order-done').on('click',function(){
    window.location.href = '/mypage';
  });

  $('.lable-register-card').on('click',function(){
    window.location.href = '/credit_card';
  });

  var area = $("input:radio[name='area']:checked").val();
  var otherArea = $("input:text[name='other_area']").val();
  var time = $("input:radio[name='time_join']:checked").val();
  var castClass = $("input:radio[name='cast_class']:checked").val();
  var duration = $("input:radio[name='time_set']:checked").val();

   if((area || (area=='その他' && otherArea)) && time && castClass && duration) {
    $("button[type='submit'][name='sb_create']").removeClass('disable');
    $("button[type='submit'][name='sb_create']").prop('disabled', false);
  }

  var buttonGreen = $(".button--green.area");
  buttonGreen.on("change",function(){
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
      $("button[type='submit'][name='sb_create']").addClass("disable");
      $("button[type='submit'][name='sb_create']").prop('disabled', true);
    } else {
      $("button[type='submit'][name='sb_create']").removeClass('disable');
      $("button[type='submit'][name='sb_create']").prop('disabled', false);
    }
  });

  var dateButton = $(".button--green.date");
    dateButton.on("change",function(){
    $("#ge2-1-x input:radio[name='time_join']").parent().removeClass("active");
    $("#ge2-1-x input:radio[name='time_join']:checked").parent().addClass("active");

    var area = $("input:radio[name='area']:checked").val();
    var otherArea = $("input:text[name='other_area']").val();
    var castClass = $("input:radio[name='cast_class']:checked").val();
    var duration = $("input:radio[name='time_set']:checked").val();
    var totalCast = $("input[type='text'][name='txtCast_Number']").val();
    var time = $("input:radio[name='time_join']:checked").val();
    var date = $('.sp-date').text();

    if((!area || (area=='その他' && !otherArea)) || !castClass ||
     (!duration || (duration<1 && 'other_duration' != duration)) ||(!totalCast || totalCast<1) || (time=='other_time' && !date)) {
      $("button[type='submit'][name='sb_create']").addClass("disable");
      $("button[type='submit'][name='sb_create']").prop('disabled', true);
    } else {
      $("button[type='submit'][name='sb_create']").removeClass('disable');
      $("button[type='submit'][name='sb_create']").prop('disabled', false);
    }
  })

  var txtArea = $("input:text[name='other_area']");
  txtArea.on("input",function(){
    var otherArea = $(this).val();
    var time = $("input:radio[name='time_join']:checked").val();
    var area = $("input:radio[name='area']:checked").val();
    var castClass = $("input:radio[name='cast_class']:checked").val();
    var duration = $("input:radio[name='time_set']:checked").val();
    var totalCast = $("input[type='text'][name='txtCast_Number']").val();
    var date = $('.sp-date').text();

    if( !time || (!area || (!otherArea)) || !castClass ||
     (!duration || (duration<1 && 'other_duration' != duration)) ||(!totalCast || totalCast<1) || (time=='other_time' && !date)) {
      $("button[type='submit'][name='sb_create']").addClass("disable");
      $("button[type='submit'][name='sb_create']").prop('disabled', true);
    } else {
      $("button[type='submit'][name='sb_create']").removeClass('disable');
      $("button[type='submit'][name='sb_create']").prop('disabled', false);
    }
  })

  //duration
  var timeButton = $(".button--green.time");
  timeButton.on("change",function(){
    $("#ge2-1-x input:radio[name='time_set']").parent().removeClass("active");
    $("#ge2-1-x input:radio[name='time_set']:checked").parent().addClass("active");

    var area = $("input:radio[name='area']:checked").val();
    var otherArea = $("input:text[name='other_area']").val();
    var castClass = $("input:radio[name='cast_class']:checked").val();
    var duration = $("input:radio[name='time_set']:checked").val();
    var totalCast = $("input[type='text'][name='txtCast_Number']").val();
    var time = $("input:radio[name='time_join']:checked").val();
    var date = $('.sp-date').text();

    if( !time || (!area || (area=='その他' && !otherArea)) ||
     !castClass || (!duration || (duration<1 && 'other_duration' != duration)) ||(!totalCast || totalCast<1) || (time=='other_time' && !date)) {
      $("button[type='submit'][name='sb_create']").addClass("disable");
      $("button[type='submit'][name='sb_create']").prop('disabled', true);
    } else {
      $("button[type='submit'][name='sb_create']").removeClass('disable');
      $("button[type='submit'][name='sb_create']").prop('disabled', false);
    }

  })

  var castClass = $("input:radio[name='cast_class']");
  castClass.on("change",function(){
    var castClass = $("input:radio[name='cast_class']:checked").val();
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
      $("button[type='submit'][name='sb_create']").addClass("disable");
      $("button[type='submit'][name='sb_create']").prop('disabled', true);
    } else {
      $("button[type='submit'][name='sb_create']").removeClass('disable');
      $("button[type='submit'][name='sb_create']").prop('disabled', false);
    }
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
  })

  if($("label").hasClass("status-code")){
    $('.status-code').click();
  }

  $('.checked-order').prop('checked',false);

  if ($("#cast-ids-nominate").length) {
    if(localStorage.getItem("order_call")){
      var arrIds = JSON.parse(localStorage.getItem("order_call")).arrIds;
      if(arrIds) {
        if(arrIds.length) {
          $("#cast-ids-nominate").val(arrIds.toString());
        }
      }
    }   
  }
});
