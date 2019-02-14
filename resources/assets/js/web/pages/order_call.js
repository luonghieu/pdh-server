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
  
  $(".cb-cancel").on("change",function(event){
    if ($(this).is(':checked')) {
      if($('.inactive-button-order').length) {
        $(this).prop('checked', false);
        $('#sp-cancel').addClass("sp-disable");
        $('#btn-confirm-orders').addClass("disable");
        $('#btn-confirm-orders').prop('disabled', true);
      } else {
        $(this).prop('checked', true);
        $('#sp-cancel').removeClass('sp-disable');
        $('#btn-confirm-orders').removeClass('disable');
        $('#btn-confirm-orders').prop('disabled', false);
      }
    } else {
      $(this).prop('checked', false);
      $('#sp-cancel').addClass("sp-disable");
      $('#btn-confirm-orders').addClass("disable");
      $('#btn-confirm-orders').prop('disabled', true);
    }
  });

  $('#btn-confirm-orders').on('click',function(){
    $('#orders').prop('checked', true);
  });

  $('.order-done').on('click',function(){
    window.location.href = '/mypage';
  });

  $('.lable-register-card').on('click',function(){
    if (window.App.payment_service == 'telecom_credit') {
      $('#telecom-credit-form').submit();
    } else {
      window.location.href = '/credit_card';
    }
  });

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
                  window.location = '/login';
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

      if(orderCall.prefecture_id){
        $('.select-prefecture').val(orderCall.prefecture_id);
        var params = {
          prefecture_id : orderCall.prefecture_id,
        };
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
        })
        .catch(function (error) {
          console.log(error);
          if (error.response.status == 401) {
            window.location = '/login';
          }
        });
      }

      var time = $("input:radio[name='time_join']:checked").val();
      var castClass = $("input:radio[name='cast_class']:checked").val();
      var duration = $("input:radio[name='time_set']:checked").val();

      if(((orderCall.select_area && orderCall.select_area !='その他') || (orderCall.select_area=='その他' && orderCall.text_area))
       && time && castClass && duration) {
        $("#step1-create-call").removeClass('disable');
        $("#step1-create-call").prop('disabled', false);
      }

      if(arrIds) {
        helper.deleteLocalStorageValue('order_call','arrIds');
      }

      var tags = JSON.parse(localStorage.getItem("order_call")).tags;
      if(tags) {
        helper.deleteLocalStorageValue('order_call','tags');
      }
    }
  }

  if($('.select-prefecture').length) {
    if(!localStorage.getItem("order_call")) {
      var params = {
          prefecture_id : $('.select-prefecture option:selected').val(),
        };

      helper.updateLocalStorageValue('order_call', params);
    }

  }

});