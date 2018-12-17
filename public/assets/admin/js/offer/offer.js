$(document).ready(function(){
  function updateLocalStorageValue(key, data) {
    var oldData = JSON.parse(localStorage.getItem(key));
    var newData;

    if (oldData) {
      newData = Object.assign({}, oldData, data);
    } else {
      newData = data;
    }

    localStorage.setItem(key, JSON.stringify(newData));
  }

  $('#sbm-offer').on("click", function(event){
    var classId = $('#class-id-offer').val();

    if(localStorage.getItem("offer")){
      var offer = JSON.parse(localStorage.getItem("offer"));
      if(offer.arrIds) {
        if (offer.class_id != classId) {
          var params = {
            arrIds: [],
            class_id : classId,
            current_point : 0,
          }

          updateLocalStorageValue('offer', params);
        }
      }
    }
  })

  if(localStorage.getItem("offer")){
    var offer = JSON.parse(localStorage.getItem("offer"));
    if(offer.class_id) {
      $('.class-id-offer').val(offer.class_id);
    }
  }

  $('#start_time_offer').on('change', function (e) {
    var startTimeFrom = $(this).val();

    var startTimeTo = getStartTimeTo(startTimeFrom);
    var html ='';
    for (var i = 0; i < startTimeTo.length; i++) {
       html += `<option value="${startTimeTo[i]}">${startTimeTo[i]}</option>`;
    }

    $('#end_time_offer').html(html);

    var time = $("#end_time_offer option:selected").val();

    var params = {
        end_time: time,
      };

    updateLocalStorageValue('offer', params);

  });

  function getStartTimeTo(data)
  {
    var startTimeFrom = data.split(":");
    var startHourFrom = startTimeFrom[0];
    var startMinuteFrom = startTimeFrom[1];

    var startHourTo = parseInt(startHourFrom);
    startHourTo +=1;
    var startMinuteTo   = startMinuteFrom;
    var arrTimeTo = [];

    for (var i = startHourTo; i <= 26; i++) {
      var value = i < 10 ? `0${i}` : i;
      arrTimeTo.push(value + ':00',value + ':30')
    }

    if (startMinuteTo == 30 ) {
      arrTimeTo.splice(0,1);
    }

    arrTimeTo.splice(arrTimeTo.length-1,1);

    return arrTimeTo;
  }

  if ($(".cast-ids-edit").length) {
    if(!localStorage.getItem("offer")){
      var arrIds = $(".cast-ids-edit").val();
      arrIds =arrIds.split(',');
      var point = $(".temp_point-edit").val();
      var classId = $(".class_id-edit").val();

      var date = $(".date-offer-edit").val();
      var startTimeFrom = $(".start_time_from-edit").val();
      var startTimeTo = $(".start_time_to-edit").val();
      var duration = $(".duration-edit").val();
      var comment = $(".comment-edit").val();

      var params = {
        arrIds: arrIds,
        current_point: point,
        class_id: classId,
        duration_offer: duration,
        comment: comment,
        end_time: startTimeTo,
        start_time: startTimeFrom,
        date: date
      };

      updateLocalStorageValue('offer', params);
    }

  }

  //select-cast
  $(".iCheck-helper").on("click", function(event){
    var checkedId = $(this).siblings("input:checkbox[name='casts_offer[]']:checked").val();
    var searchId = $(this).siblings("input:checkbox[name='casts_offer[]']").val();
    var classId = $(this).siblings("input:checkbox[name='casts_offer[]']").data("id");

    if(localStorage.getItem("offer")){
      var offer = JSON.parse(localStorage.getItem("offer"));
      if(offer.arrIds) {
        //isset arrIds
        var arrIds = offer.arrIds;

        if(6 > arrIds.length && arrIds.length >= 0) {
          if(checkedId) {
            if (offer.class_id == classId) {
              $(this).css('opacity', 0);
              arrIds.push(checkedId);
            } else {
              $(this).css('opacity', 1);
            }
          } else {
            $(this).css('opacity', 1);

            if(arrIds.indexOf(searchId) > -1) {
              arrIds.splice(arrIds.indexOf(searchId), 1);
            }
          }
        } else {
          if(arrIds.indexOf(searchId) > -1) {
            arrIds.splice(arrIds.indexOf(searchId), 1);
          }

          $(this).siblings("input:checkbox[name='casts_offer[]']").prop('checked', false);
          $(this).css('opacity', 1)
        }

        var params = {
              arrIds: arrIds,
            };

        if(arrIds.length) {
          var nomineeIds = arrIds.toString();
          var date =  $('.date-offer option:selected').val();
          var duration = $("#duration_offer option:selected").val();
          var time = $('#start_time_offer option:selected').val();
          $('.class-id-offer').val(classId);
          $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            dataType: "html",
            url: '/admin/offers/price',
            data: {
              date : date,
              start_time : time,
              type :2,
              duration :duration,
              total_cast :arrIds.length,
              nominee_ids : nomineeIds,
              class_id : classId,
              offer : 1
            },
            success: function( val ) {
              var point = {
                current_point: val,
              };
              updateLocalStorageValue('offer', point);

              $('#current-point-offer').val(val);

              val = parseInt(val).toLocaleString(undefined,{ minimumFractionDigits: 0 });
              $('.show-current-point-offer').text('予定合計ポイント : ' + val + 'P~' );
            },
          });
        } else {
          $('.show-current-point-offer').text('予定合計ポイント : 0P~' );
          $('#current-point-offer').val(0);
          var point = {
            current_point: 0,
          };
          updateLocalStorageValue('offer', point);
        }


      } else {
        //not isset arrIds
        var arrIds = [];
        if(checkedId) {
          arrIds.push(checkedId);
        }

        $('.class-id-offer').val(classId);
        var nomineeIds = arrIds.toString();
        var date =  $('.date-offer option:selected').val();
        var duration = $("#duration_offer option:selected").val();
        var time = $('#start_time_offer option:selected').val();

        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: "POST",
          dataType: "html",
          url: '/admin/offers/price',
          data: {
            date : date,
            start_time : time,
            type :2,
            duration :duration,
            total_cast :arrIds.length,
            nominee_ids : nomineeIds,
            class_id : classId,
            offer : 1
          },
          success: function( val ) {
            $('#current-point-offer').val(val);
            var point = {
              current_point: val,
            };
            updateLocalStorageValue('offer', point);
            val = parseInt(val).toLocaleString(undefined,{ minimumFractionDigits: 0 });
            $('.show-current-point-offer').text('予定合計ポイント : ' + val + 'P~' );
          },
        });


        var params = {
            arrIds: arrIds,
            class_id: classId
          };
      }
    } else {
      //not isset arrIds
      var arrIds = [];
      if(checkedId) {
        arrIds.push(checkedId);
      }
      $('.class-id-offer').val(classId);
      var nomineeIds = arrIds.toString();
      var date =  $('.date-offer option:selected').val();
      var duration = $("#duration_offer option:selected").val();
      var time = $('#start_time_offer option:selected').val();

      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "POST",
        dataType: "html",
        url: '/admin/offers/price',
        data: {
          date : date,
          start_time : time,
          type :2,
          duration :duration,
          total_cast :arrIds.length,
          nominee_ids : nomineeIds,
          class_id : classId,
          offer : 1
        },
        success: function( val ) {
          $('#current-point-offer').val(val);
          var point = {
            current_point: val,
          };
          updateLocalStorageValue('offer', point);

          val = parseInt(val).toLocaleString(undefined,{ minimumFractionDigits: 0 });

          $('.show-current-point-offer').text('予定合計ポイント : ' + val + 'P~' );
        },
      });

      var params = {
            arrIds: arrIds,
            class_id: classId
          };
    }

    $(".cast-ids-offer").val(arrIds.toString());

    updateLocalStorageValue('offer', params);

    var totalCast = JSON.parse(localStorage.getItem("offer"));
    totalCast = totalCast.arrIds;

    $('.total-cast-offer').text('現在選択しているキャスト: ' + totalCast.length + '名');
  })

  //comment
  $("#comment-offer").on('change', function(e) {
    var params = {
      comment: $(this).val(),
    };
    updateLocalStorageValue('offer', params);
  });

  //duration
  $("#duration_offer").on("change",function(){
    var duration = $("#duration_offer option:selected").val();
    if(localStorage.getItem("offer")){
      var offer = JSON.parse(localStorage.getItem("offer"));
      if(offer.arrIds.length) {
        var nomineeIds = offer.arrIds.toString();
        var date =  $('.date-offer option:selected').val();

        var time = $('#start_time_offer option:selected').val();

        if(offer.class_id) {
          var classId = offer.class_id
        } else {
          var classId =1;
        }

        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: "POST",
          dataType: "html",
          url: '/admin/offers/price',
          data: {
            date : date,
            start_time : time,
            type :2,
            duration :duration,
            total_cast :offer.arrIds.length,
            nominee_ids : nomineeIds,
            class_id : classId,
            offer : 1
          },
          success: function( val ) {
            $('#current-point-offer').val(val);
            var point = {
              current_point: val,
            };
            updateLocalStorageValue('offer', point);

            val = parseInt(val).toLocaleString(undefined,{ minimumFractionDigits: 0 });
            $('.show-current-point-offer').text('予定合計ポイント : ' + val + 'P~' );
          },
        });

      }
    }

    var params = {
        duration_offer: duration,
      };

    updateLocalStorageValue('offer', params);
  });

  //date

  $("#select-date-offer").on("change",function(){
    var date = $("#select-date-offer option:selected").val();

     var params = {
        date: date,
      };

    updateLocalStorageValue('offer', params);
  })


  //start_time
  $("#start_time_offer").on("change",function(){
    var time = $("#start_time_offer option:selected").val();
    if(localStorage.getItem("offer")){
      var offer = JSON.parse(localStorage.getItem("offer"));
      if(offer.arrIds) {
        if(offer.arrIds.length) {
          var duration = $("#duration_offer option:selected").val();
          var nomineeIds = offer.arrIds.toString();
          var date =  $('.date-offer option:selected').val();

          if(offer.class_id) {
            var classId = offer.class_id
          } else {
            var classId =1;
          }

          $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            dataType: "html",
            url: '/admin/offers/price',
            data: {
              date : date,
              start_time : time,
              type :2,
              duration :duration,
              total_cast :offer.arrIds.length,
              nominee_ids : nomineeIds,
              class_id : classId,
              offer : 1
            },
            success: function( val ) {
              $('#current-point-offer').val(val);
              var point = {
                current_point: val,
              };
              updateLocalStorageValue('offer', point);

              val = parseInt(val).toLocaleString(undefined,{ minimumFractionDigits: 0 });
              $('.show-current-point-offer').text('予定合計ポイント : ' + val + 'P~' );
            },
          });
        }
      }
    }

    var params = {
        start_time: time,
      };

    updateLocalStorageValue('offer', params);
  });

  //end_time
  $("#end_time_offer").on("change",function(){
    var time = $("#end_time_offer option:selected").val();

    var params = {
        end_time: time,
      };

    updateLocalStorageValue('offer', params);
  });



  if(localStorage.getItem("offer")){
    var offer = JSON.parse(localStorage.getItem("offer"));

    //select-cast
    if(offer.arrIds){

      const cbCastOffer = $("input:checkbox[name='casts_offer[]']");
      var arrIds = offer.arrIds;
      $('.total-cast-offer').text('現在選択しているキャスト: ' + arrIds.length + '名');

      if(offer.arrIds.length){
        $(".cast-ids-offer").val(offer.arrIds.toString());
        $.each(cbCastOffer,function(index,val){
          if (arrIds.indexOf(val.value) >-1) {
            $(this).prop('checked', true);
            $(this).parent().addClass('checked');
          }
        })
        pointOffer = parseInt(offer.current_point).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        $('.show-current-point-offer').text('予定合計ポイント : ' + pointOffer + 'P~' );
        $('#current-point-offer').val(offer.current_point);
        $('.class-id-offer').val(offer.class_id);
      }
    }

    //comment
    if(offer.comment){
      $("#comment-offer").text(offer.comment);
    }

    //duration
    if(offer.duration_offer){
      const inputDuration = $('select[name=duration_offer] option');
      $.each(inputDuration,function(index,val){
        if(val.value == offer.duration_offer) {
          $(this).prop('selected',true);
        }
      })
    }

    //start_time
    if(offer.start_time){
      const inputStartTime = $('select[name=start_time_offer] option');
      $.each(inputStartTime,function(index,val){
        if(val.value == offer.start_time) {
          $(this).prop('selected',true);
        }
      })

      var startTimeFrom = offer.start_time;

      var startTimeTo = getStartTimeTo(startTimeFrom);
      var html ='';
      for (var i = 0; i < startTimeTo.length; i++) {
         html += `<option value="${startTimeTo[i]}">${startTimeTo[i]}</option>`;
      }

      $('#end_time_offer').html(html);

    }

    //end_time
    if(offer.end_time){
      const inputEndTime = $('select[name=end_time_offer] option');
      $.each(inputEndTime,function(index,val){
        if(val.value == offer.end_time) {
          $(this).prop('selected',true);
        }
      })
    }

    //date
    if(offer.date){
      const inputEndTime = $('select[name=date_offer] option');
      $.each(inputEndTime,function(index,val){
        if(val.value == offer.date) {
          $(this).prop('selected',true);
        }
      })
    }
  }

});