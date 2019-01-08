const helper = require('./helper');

function handelSelectedCasts()
{
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
              $('.label-select-casts[for='+  id  +']').text('リクエスト中');
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
            var arrIds = [id];

            var params = {
                arrIds: arrIds
              };

            $(this).prop('checked',true);
            $(this).parent().find('.cast-link').addClass('cast-detail');
            $('.label-select-casts[for='+  id  +']').text('リクエスト中');
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
}

$(document).ready(function () {
  handelSelectedCasts();
});