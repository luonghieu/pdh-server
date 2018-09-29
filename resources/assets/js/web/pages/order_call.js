$(document).ready(function(){
  $('.select-month').on('change', function (e) {
    var month = $(this).val();
    window.axios.post('/get_day', {month})
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

    var selectDate = new Date(year +'-' +month+'-'+date +' ' +hour +':' +minute);

    var add_minutes =  function (dt, minutes) {
        return new Date(dt.getTime() + minutes*60000);
    }

    utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
    nd = new Date(utc + (3600000*9));

    if(add_minutes(nd,20) > selectDate) {
      selectDate = add_minutes(nd,20);
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

  $('.tags-name').on('click', function(e) {
    var activeTag = $('.tags-name:checked').length;

    if (activeTag > 5) {
      $('.lbmax').click();
    }
  });

  $(".form-grpup .checkbox-tags").on("change",function(event){
    if ($(this).hasClass("active")) {
      $(this).children().attr('checked',false);
    } else {
      $(this).children().attr('checked',true);
    }

    var activeSum = $(".active").length;

    if(activeSum >= 5 && !$(this).hasClass("active")){
      $(this).children().attr('checked',false);
    }else{
      $(this).toggleClass("active");
    }
  });

  $(".cast_block .select-casts").on("change",function(event){
    var castNumbers = $(".cast-numbers").val();
    if($('.select-casts:checked').length > castNumbers) {
      $(this).attr('checked',false);
    }else {

      var id = $(this).val();
      if ($(this).is(':checked')) {
        $(this).attr('checked',true);
        $('.label-select-casts[for='+  id  +']').text('指名中');
      } else {
        $(this).attr('checked',false);
        $('.label-select-casts[for='+  id  +']').text('指名する');
      }
    }
  });

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
      $('#add-orders').submit();
    }
  });

  if($("label").hasClass("order-done")){
    $('.order-done').click();
  }

  $('.modal-redirect').on('click',function(){
   $('#redirect-index').submit();
  });

  $('.order-done').on('click',function(){
   $('.register-card').submit();
  });

  $('.lable-register-card').on('click',function(){
   $('.register-card').submit();
  });

  var area = $("input:radio[name='area']:checked").val();
  var otherArea = $("input:text[name='other_area']").val();
  var time = $("input:radio[name='time_join']:checked").val();
  var castClass = $("input:radio[name='cast_class']:checked").val();
  var duration = $("input:radio[name='time_set']:checked").val();

   if((area || (area=='その他' && otherArea)) || time || castClass || duration) {
    $("button[type='submit'][name='sb_create']").removeClass('disable');
    $("button[type='submit'][name='sb_create']").prop('disabled', false);
  }

  var buttonGreen = $(".button--green.area");
  buttonGreen.on("change",function(){
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

  if (checkNumber>1) {
    if (checkNumber==10) {
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

    if(number_val==9){
      $(this).css({"border": "1.5px #cccccc solid"});
      $(this).addClass('active');
    }

    if(number_val>=10) {
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
});
