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

  //select-cast
  $(".iCheck-helper").on("click", function(event){
    var checkedId = $(this).siblings("input:checkbox[name='casts_offer[]']:checked").val();
    var searchId = $(this).siblings("input:checkbox[name='casts_offer[]']").val();

    if(localStorage.getItem("offer")){
      var offer = JSON.parse(localStorage.getItem("offer"));
      if(offer.arrIds) {
        //isset arrIds
        var arrIds = offer.arrIds;

        if(4 > arrIds.length && arrIds.length >= 0) {
          if(checkedId) {
            $(this).css('opacity', 0);
            arrIds.push(checkedId);
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
      } else {
        //not isset arrIds
        var arrIds = [];
        if(checkedId) {
          arrIds.push(checkedId);
        }

        var params = {
            arrIds: arrIds
          };
      }
    } else {
      //not isset arrIds
      var arrIds = [];
      if(checkedId) {
        arrIds.push(checkedId);
      }

      var params = {
            arrIds: arrIds
          };
    }

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

    var params = {
        duration_offer: duration,
      };

    updateLocalStorageValue('offer', params);
  });

  //start_time
  $("#start_time_offer").on("change",function(){
    var time = $("#start_time_offer option:selected").val();

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

      $.each(cbCastOffer,function(index,val){
        if (arrIds.indexOf(val.value) >-1) {
          $(this).prop('checked', true);
          $(this).parent().addClass('checked');
        }
      })
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

  }

});
