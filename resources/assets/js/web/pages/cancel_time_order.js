const helper = require('./helper');

$(document).ready(function () {

  if($('.select-month').length) {

    var params = {
      current_month : $('.select-month').val(),
      current_date : $('.select-date').val(),
      current_hour : $('.select-hour').val(),
      current_minute : $('.select-minute').val(),
    }

    helper.updateLocalStorageValue('first_load_time', params);
  } else {
    if(localStorage.getItem("first_load_time")){
      localStorage.removeItem("first_load_time");
    }
  }

  if ($('.input-area-offer').length) {
    var params = {
      current_hour_offer : $('.select-hour-offer').val(),
      current_minute_offer : $('.select-minute-offer').val(),
    }

    helper.updateLocalStorageValue('first_load_time_offer', params);
  } else {
    if(localStorage.getItem("first_load_time_offer")){
      localStorage.removeItem("first_load_time_offer");
    }
  }

  $('.date-select__cancel').on("click",function(event){
    //offer
    if($('.input-area-offer').length) {
      var currentTime = JSON.parse(localStorage.getItem("first_load_time_offer"));

      if(localStorage.getItem("order_offer")){
        var offerId = $('.offer-id').val();
        var orderOffer = JSON.parse(localStorage.getItem("order_offer"));
        if(orderOffer[offerId]) {
          orderOffer = orderOffer[offerId];
          if(orderOffer.hour) {
            const inputHour = $('select[name=select_hour_offer] option');
            $.each(inputHour,function(index,val){
              if(val.value == orderOffer.hour) {
                $(this).prop('selected',true);
              }
            })
            $('.select-minute-offer').val(orderOffer.minute);
          } else {
            $('.select-hour-offer').val(currentTime.current_hour_offer);
            $('.select-minute-offer').val(currentTime.current_minute_offer);
          }
        } else {
          $('.select-hour-offer').val(currentTime.current_hour_offer);
          $('.select-minute-offer').val(currentTime.current_minute_offer);
        }
      } else {
        $('.select-hour-offer').val(currentTime.current_hour_offer);
        $('.select-minute-offer').val(currentTime.current_minute_offer);
      }
    }

    if($('.select-month').length) {
      var firstLoadTime = JSON.parse(localStorage.getItem("first_load_time"));
      //1-1
      if($('#confirm-orders-nomination').length) {
        if(localStorage.getItem("order_params")){
          var orderNomination = JSON.parse(localStorage.getItem("order_params"));
          if (orderNomination.current_date) {
            var month = parseInt(orderNomination.current_month);

            //month 
            const inputMonth = $('select[name=sl_month_nomination] option');
            $.each(inputMonth,function(index,val){
              if(val.value == month) {
                $(this).prop('selected',true);
              }
            })

            //date
            window.axios.post('/api/v1/get_day', {month})
              .then(function(response) {
                var html = '';
                Object.keys(response.data).forEach(function (key) {
                  if(key!='debug') {
                  html +='<option value="'+key+'">'+ response.data[key] +'</option>';
                  }
                })
                $('.select-date').html(html);

                const inputDate = $('select[name=sl_date_nomination] option');
                $.each(inputDate,function(index,val){
                  if(val.value == parseInt(orderNomination.current_date)) {
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

              //hour
              const inputHour = $('select[name=sl_hour_nomination] option');
              $.each(inputHour,function(index,val){
                if(val.value == parseInt(orderNomination.current_hour)) {
                  $(this).prop('selected',true);
                }
              })

              //minute
              const inputMinute = $('select[name=sl_minute_nomination] option');
              $.each(inputMinute,function(index,val){
                if(val.value == parseInt(orderNomination.current_minute)) {
                  $(this).prop('selected',true);
                }
              })

          }else {
            var month = firstLoadTime.current_month;
            $('.select-month').val(month);

            window.axios.post('/api/v1/get_day', {month})
              .then(function(response) {
                var html = '';
                Object.keys(response.data).forEach(function (key) {
                  if(key!='debug') {
                  html +='<option value="'+key+'">'+ response.data[key] +'</option>';
                  }
                })
                $('.select-date').html(html);

                const inputDate = $('select[name=sl_date_nomination] option');

                $.each(inputDate,function(index,val){
                  if(val.value == firstLoadTime.current_date) {
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

            $('.select-hour').val(firstLoadTime.current_hour);
            $('.select-minute').val(firstLoadTime.current_minute);
          }
        }
      }

      //call
      if($('#cast-number-call').length) {
        if(localStorage.getItem("order_call")){
          var orderCall = JSON.parse(localStorage.getItem("order_call"));
          if (orderCall.current_time) {
            var currentDate = orderCall.current_date.split('-');
            var currentTime = orderCall.current_time.split(':');

            var month = parseInt(currentDate[1]);

            //month 
            const inputMonth = $('select[name=sl_month] option');
            $.each(inputMonth,function(index,val){
              if(val.value == month) {
                $(this).prop('selected',true);
              }
            })

            //date
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
                  if(val.value == parseInt(currentDate[2])) {
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

            //hour
            const inputHour = $('select[name=sl_hour] option');
            $.each(inputHour,function(index,val){
              if(val.value == parseInt(currentTime[0])) {
                $(this).prop('selected',true);
              }
            })
            //minute
            const inputMinute = $('select[name=sl_minute] option');
            $.each(inputMinute,function(index,val){
              if(val.value == parseInt(currentTime[1])) {
                $(this).prop('selected',true);
              }
            })
          } else {
            var month = firstLoadTime.current_month;
            $('.select-month').val(month);

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
                  if(val.value == firstLoadTime.current_date) {
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

            $('.select-hour').val(firstLoadTime.current_hour);
            $('.select-minute').val(firstLoadTime.current_minute);
          }
        }
      }
    }
  })
});
