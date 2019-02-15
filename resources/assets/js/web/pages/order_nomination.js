$(document).ready(function(){
  const helper = require('./helper');

  $('body').on('change', ".checked-order",function(event){
    if ($(this).is(':checked')) {
        var time = $("input:radio[name='time_join_nomination']:checked").val();
        var area = $("input:radio[name='nomination_area']:checked").val();
        var duration = $("input:radio[name='time_set_nomination']:checked").val();
        var date = $('.sp-date').text();
        var cancel=$("input:checkbox[name='confrim_order_nomination']:checked").length;
        var otherArea = $("input:text[name='other_area_nomination']").val();

        if((!area || (area=='その他' && !otherArea)) || !time ||
         (!duration || (duration<1 && 'other_time_set' != duration)) || (time=='other_time' && !date) 
         || $('.inactive-button-order').length) {

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
  var timeSet = $("input:radio[name='time_set_nomination']");
  timeSet.on("change",function(){
    var time = $("input:radio[name='time_join_nomination']:checked").val();

    var duration = $("input:radio[name='time_set_nomination']:checked").val();

    var params = {
      current_duration: duration,
    };

    helper.updateLocalStorageValue('order_params', params);

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
      } else {
        totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        $('.total-point').text(totalPoint +'P~');

        var params = {
            current_total_point: totalPoint,
          };

        helper.updateLocalStorageValue('order_params', params);
      }
  })

  $('.select-duration').on("change",function(){
    var time = $("input:radio[name='time_join_nomination']:checked").val();
    var duration = $('.select-duration option:selected').val();

    var params = {
        select_duration: duration,
      };

    helper.updateLocalStorageValue('order_params', params);

    var cost = $('.cost-order').val();
    var totalPoint=cost*(duration*6)/3;
    if(time) {
      var currentDate = new Date();
      utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
      nd = new Date(utc + (3600000*9));

      var year = nd.getFullYear();

      if (time == 'other_time') {
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
      } else {
        totalPoint = parseInt(totalPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        $('.total-point').text(totalPoint +'P~');

        var params = {
            current_total_point: totalPoint,
          };

        helper.updateLocalStorageValue('order_params', params);
      }

    cost = parseInt(cost).toLocaleString(undefined,{ minimumFractionDigits: 0 });

    $('.reservation-total__text').text('内訳：'+cost+ '(キャストP/30分)✖'+(duration)+'時間')
  })

//timejoin
  $("input:radio[name='time_join_nomination']").on("change",function(){
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

      var cost = $('.cost-order').val();

      var currentDate = new Date();
      utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
      nd = new Date(utc + (3600000*9));

      var year = nd.getFullYear();

      if (time == 'other_time') {
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

  $('#confirm-orders-nomination').on('click',function(){
    $('.modal-confirm-nominate').css('display','inline-block');
    $('#orders-nominate').prop('checked',true);
  });

  $('.cf-orders-nominate').on('click',function(){
      if($('#md-require-card').length){
        $('#md-require-card').click();
      }else {
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
          $('.total-point').text(orderParams.current_total_point +'P~');
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

})
