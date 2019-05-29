const helper = require('./helper');
let couponCastOffer = [];
const couponType = {
  'POINT': 1,
  'DURATION': 2,
  'PERCENT': 3
};

const OrderPaymentMethod = {
  'Credit_Card': 1,
  'Direct_Payment': 2
};

function loadCouponsCastOffer()
{
  var duration = $('#duration-cast-offer').val();
  var offerId = $('#cast_offer-id').val();

  var paramCoupon = {
    duration : duration,
  };

  window.axios.get('/api/v1/coupons', {params: paramCoupon})
  .then(function(response) {
    couponCastOffer = response.data['data'];

    if(localStorage.getItem("cast_offer")){
      var castOffer = JSON.parse(localStorage.getItem("cast_offer"));
      if(castOffer[offerId]) {
        castOffer = castOffer[offerId];

        if (castOffer.coupon) {
          showPriceCoupon(duration, castOffer.coupon);
        }
      }
    }

  }).catch(function(error) {
    console.log(error);
    if (error.response.status == 401) {
      window.location = '/login';
    }
  });
}

function showPriceCoupon(duration = null, coupon = null)
{
  if(coupon) {
    var params = {
      type :3,
      duration :duration,
      total_cast :1,
      nominee_ids : $('#cast-id').val(),
      date : $('#date-cast-offer').val(),
      start_time : $('#time-cast-offer').val(),
      class_id : $('#class_cast-id').val(),
    };
    if(!couponCastOffer) {
      window.location = '/mypage';
    }

    var couponIds = couponCastOffer.map(function (e) {
      return parseInt(e.id); 
    });

    if(couponIds.indexOf(parseInt(coupon)) > -1) {
      couponCastOffer.forEach(function (e) {
        if(e.id == coupon) {
          coupon = e;
        }
      });
    } else {
      window.location = '/mypage';
    }

    if(couponType.POINT == coupon.type) {
      params.duration_coupon = 0;
    }

    if(couponType.DURATION == coupon.type) {
      params.duration_coupon = coupon.time;
    }

    if(couponType.PERCENT == coupon.type) {
      params.duration_coupon = 0;
    }

    switch (coupon.type){
      case couponType.PERCENT:
        params.duration_coupon = 0;

        break;

      case couponType.POINT:
        params.duration_coupon = 0;

        break;

      case couponType.DURATION:
        params.duration_coupon = coupon.time;

        break;
    }

    window.axios.post('/api/v1/orders/price',params)
      .then(function(response) {
        if (couponType.PERCENT == coupon.type) {
          var tempPoint = response.data['data'];
          var pointCoupon = (parseInt(coupon.percent)/100)*tempPoint;
        }

        if (couponType.POINT == coupon.type) {
          var tempPoint = response.data['data'];
          var pointCoupon = coupon.point;
        }

        if (couponType.DURATION == coupon.type) {
          var totalCouponPoint = response.data['data'];
          var tempPoint = totalCouponPoint.total_point;
          var pointCoupon = totalCouponPoint.order_point_coupon + totalCouponPoint.order_fee_coupon;
        }

        switch (coupon.type){
          case couponType.PERCENT:
            var tempPoint = response.data['data'];
            var pointCoupon = (parseInt(coupon.percent)/100)*tempPoint;

            break;

          case couponType.POINT:
            var tempPoint = response.data['data'];
            var pointCoupon = coupon.point;

            break;

          case couponType.DURATION:
            var totalCouponPoint = response.data['data'];
            var tempPoint = totalCouponPoint.total_point;
            var pointCoupon = totalCouponPoint.order_point_coupon + totalCouponPoint.order_fee_coupon;

            break;
        }

        if(coupon.max_point) {
          if(coupon.max_point < pointCoupon) {
            pointCoupon = coupon.max_point;
          }

          var maxPoint = parseInt(coupon.max_point).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('.offer-coupon__text').html(`※割引されるポイントは最大${maxPoint}Pになります。`);
        }

        var currentPoint = tempPoint-pointCoupon;
        if(currentPoint<0) {
          currentPoint = 0;
        }

        $('#total-point-cast-offer').val(currentPoint);

        currentPoint = parseInt(currentPoint).toLocaleString(undefined,{ minimumFractionDigits: 0 });
        pointCoupon = parseInt(pointCoupon).toLocaleString(undefined,{ minimumFractionDigits: 0 });

        var html = `割引額<span class="red">${pointCoupon} P</span>`;
        var text = `合計<span >${currentPoint} P</span>`;
        $('#point-sale-coupon').html(html);
        $('#current-point').html(text);
      }).catch(function(error) {
        console.log(error);
        if (error.response.status == 401) {
          window.location = '/login';
        }
      });
  } else {
    var pointShow = $('#current-point-cast-offer').val();
    pointShow = parseInt(pointShow).toLocaleString(undefined,{ minimumFractionDigits: 0 });
    
    $('#point-sale-coupon').html('');
    $('#current-point').html(`合計<span >${pointShow} P</span>`);
  }
}

function selectedCouponsCastOffer()
{
  $('body').on('change', "#cast-offer-coupon", function(){
    var offerId = $('#cast_offer-id').val();
    var couponId = $(this).val();
    var duration = $('#duration-cast-offer').val();
    var time = $("input:radio[name='time_join_nomination']:checked").val();

    if(!couponCastOffer) {
      window.location = '/mypage';
    }

    var couponIds = couponCastOffer.map(function (e) {
      return e.id; 
    });

    var coupon = {};
    if(parseInt(couponId)) {
      if(couponIds.indexOf(parseInt(couponId)) > -1) {
        var paramCoupon = {
          coupon : parseInt(couponId),
        }

        helper.updateLocalStorageKey('cast_offer', paramCoupon, offerId);

        couponCastOffer.forEach(function (e) {
          if(e.id == couponId) {
            coupon = e;
          }
        });

        if(coupon.max_point) {
          var maxPoint = parseInt(coupon.max_point).toLocaleString(undefined,{ minimumFractionDigits: 0 });
          $('.offer-coupon__text').html(`※割引されるポイントは最大${maxPoint}Pになります。`);
        }
      } else {
        window.location = '/mypage';
      }
    } else {
      $('.offer-coupon__text').html('');
      if(localStorage.getItem("cast_offer")){
        var castOffer = JSON.parse(localStorage.getItem("cast_offer"));

        if(castOffer[offerId]) {
          castOffer = castOffer[offerId];
          if(castOffer.coupon) {
            helper.deleteLocalStorageKey('cast_offer','coupon', offerId);
          }
        }
      }
    }

    showPriceCoupon(duration, couponId);
   })
}

function handlerPaymentMethod()
{
  var transfer = $("input:radio[name='payment_method']");
  transfer.on("change",function(){
    var offerId = $('#cast_offer-id').val();
    var transfer = $("input:radio[name='payment_method']:checked").val();

    var param = {
          payment_method : transfer,
        }

    helper.updateLocalStorageKey('cast_offer', param, offerId);

    if (OrderPaymentMethod.Direct_Payment == parseInt(transfer)) {
      $('#show-card-cast-offer').css('display', 'none');
    }

    if (OrderPaymentMethod.Credit_Card == parseInt(transfer)) {
      $('#show-card-cast-offer').css('display', 'block');

      if ($('.inactive-button-order').length) {
        $('#confirm-cast-order').addClass("disabled");
        $('.checked-cast-offer').prop('checked', false);
        $('#confirm-cast-order').prop('disabled', true);
        $('#sp-cancel').addClass("sp-disable");
      }
    }
  })
}

function createCastOffer(transfer = null)
{
  $('.modal-confirm-cast-offer').css('display','none');
  $('#confirm-cast-order').prop('disabled', true);
  var castOrderId = $('#cast_offer-id').val();

  var params = {
    temp_point: $('#temp-point-offer').val(),
    order_id: castOrderId,
  }

  if ($('#total-point-cast-offer').val()) {
    params.temp_point = parseInt($('#total-point-cast-offer').val());
  } else {
    params.temp_point = parseInt($('#current-point-cast-offer').val());
  }

  if(transfer) {
    params.payment_method = transfer;
  }

  if(localStorage.getItem("cast_offer")){
    var castOffer = JSON.parse(localStorage.getItem("cast_offer"));
    if(castOffer[castOrderId]) {
      castOffer = castOffer[castOrderId];
      if(castOffer.coupon) {

        var couponIds = couponCastOffer.map(function (e) {
          return parseInt(e.id); 
        });

        var coupon = null;            
        if(couponIds.indexOf(parseInt(castOffer.coupon)) > -1) {
          couponCastOffer.forEach(function (e) {
            if(e.id == castOffer.coupon) {
              coupon = e;
            }
          });
        } else {
          window.location = '/mypage';
        }
  
        params.coupon_id = coupon.id;
        params.coupon_name = coupon.name;
        params.coupon_type = coupon.type;
        
        if(coupon.max_point) {
          params.coupon_max_point = coupon.max_point;
        } else {
          params.coupon_max_point = null;
        }

        switch(coupon.type) {
          case couponType.POINT:
            params.coupon_value = coupon.point;
            break;

          case couponType.DURATION:
            params.coupon_value = coupon.time;
            break;

          case couponType.PERCENT:
            params.coupon_value = coupon.percent;
            break;

          default:
            window.location.href = '/mypage';
        }
      }
    }
  }

  window.axios.post('/api/v1/guest/cast_offers/accept', params)
  .then(function(response) {
    $('#cast-offer-popup').prop('checked',false);
    var roomId = response.data.data.room_id;
    window.location.href = '/message/' +roomId;
  })
  .catch(function(error) {
    console.log(error)
    $('#confirm-cast-order').prop('disabled', false);
    $('#cast-offer-popup').prop('checked',false);
     if (error.response.status == 401) {
        window.location = '/login';
      } else {
        if(error.response.status == 422) {
            $('#timeout-offer-message h2').css('font-size', '15px');

            $('#timeout-offer-message h2').html('この操作は実行できません');

            $('#timeout-offer').prop('checked',true);     
        } else {
          var err ='';

          switch (parseInt(error.response.status)){
            case 403:
              err = 'アカウントが凍結されています';

              break;

            case 404:
              err = '支払い方法が未登録です';

              break;

            case 406:
              err = 'こちらの飲み会の提案は取り下げられました。';

              break;

            case 409:
              err = 'クーポンが無効です';

              break;

            case 412:
              err = '退会申請中のため、予約することはできません。';

              break;

            default:
              err = 'サーバーエラーが発生しました';

              break;
          }

          $('#err-offer-message h2').html(err);

          $('#err-offer').prop('checked',true);
        }
      }
  })
}

function checkedCastOffer()
{
  $('body').on('change', ".checked-cast-offer",function(event){
    if ($(this).is(':checked')) {
      var checkCard = $('.inactive-button-order').length;
      var transfer = $("input:radio[name='transfer_order_nominate']:checked").val();

      if(OrderPaymentMethod.Direct_Payment == transfer) {
        checkCard = false;
      }

      if(checkCard) {
        $('#confirm-cast-order').addClass("disable");
        $(this).prop('checked', false);
        $('#confirm-cast-order').prop('disabled', true);
        $('#sp-cancel').addClass("sp-disable");
      } else {
        $(this).prop('checked', true);
        $('#sp-cancel').removeClass('sp-disable');
        $('#confirm-cast-order').removeClass('disable');
        $('#confirm-cast-order').prop('disabled', false);
      }
    } else {
      $('#confirm-cast-order').addClass("disable");
      $(this).prop('checked', false);
      $('#confirm-cast-order').prop('disabled', true);
      $('#sp-cancel').addClass("sp-disable");
    }
  });
}

function denyCastOffer()
{
  $('body').on('click', "#canceled-cast-offer", function(){
    var castOrderId = $('#cast_offer-id').val();

    window.axios.post('/api/v1/guest/cast_offers/' + parseInt(castOrderId) +'/deny')
      .then(function(response) {
        window.location.href = '/mypage';
      })
      .catch(function(error) {
        $('#confirm-cast-order').prop('disabled', false);
        $('#cast-offer-popup').prop('checked',false);
         if (error.response.status == 401) {
            window.location = '/login';
          } else {
            if(error.response.status == 422) {
                $('#timeout-offer-message h2').css('font-size', '15px');

                $('#timeout-offer-message h2').html('この操作は実行できません');

                $('#timeout-offer').prop('checked',true);     
            } else {
              var err ='';

              switch (error.response.status){
                case 406:
                  err = 'こちらの飲み会の提案は取り下げられました。';

                  break;

                default:
                  err = 'サーバーエラーが発生しました';

                  break;
              }

              $('#err-offer-message h2').html(err);

              $('#err-offer').prop('checked',true);
            }
          }
        })
    })
}

$(document).ready(function(){

  if($('#confirm-cast-order').length) {
    loadCouponsCastOffer();
    checkedCastOffer();
    handlerPaymentMethod();
    selectedCouponsCastOffer();
    denyCastOffer();
    var castOrderId = $('#cast_offer-id').val();

    if(localStorage.getItem("cast_offer")){
      let castOffer = JSON.parse(localStorage.getItem("cast_offer"));
      if(castOffer[castOrderId]) {
        castOffer = castOffer[castOrderId];

        //payment
        if(castOffer.payment_method) {
          const inputTransfer = $("input:radio[name='payment_method']");
          $.each(inputTransfer,function(index,val){
            if(val.value == parseInt(castOffer.payment_method)) {
              $(this).prop('checked',true);
            }
          })

          if (OrderPaymentMethod.Direct_Payment == parseInt(castOffer.payment_method)) {
            $('#show-card-cast-offer').css('display', 'none');
          }
        }

        if(castOffer.coupon) {
          const selectCoupon = $("#cast-offer-coupon option");
          $.each(selectCoupon,function(index,val){
            if(val.value == parseInt(castOffer.coupon)) {
              $(this).prop('selected',true);
            }
          })
        }
      }
    }

    $('#confirm-cast-order').on('click',function(){
      $('.modal-confirm-cast-offer').css('display','inline-block');
      $('#cast-offer-popup').prop('checked',true);
    })

    $('body').on('click', "#create-cast-offer", function(){
      var transfer = parseInt($("input[name='payment_method']:checked").val());

      if (transfer) {
        if (OrderPaymentMethod.Credit_Card == transfer || OrderPaymentMethod.Direct_Payment == transfer) {
          if (OrderPaymentMethod.Direct_Payment == transfer) {
            window.axios.get('/api/v1/auth/me')
              .then(function(response) {
                var pointUser = response.data['data'].point;
                window.axios.get('/api/v1/guest/points_used')
                  .then(function(response) {
                    var pointUsed = response.data['data'];

                    if($('#total-point-cast-offer').val()){
                      var tempPointCastOffer = $('#total-point-cast-offer').val();
                    } else {
                      var tempPointCastOffer = $('#current-point-cast-offer').val();
                    }

                    var tempPointOrder = parseInt(tempPointCastOffer) + parseInt(pointUsed);

                    if (parseInt(tempPointOrder) > parseInt(pointUser)) {
                      $('#cast-offer-popup').prop('checked',false);
                      $('.checked-cast-offer').prop('checked', false);
                      $('#sp-cancel').addClass('sp-disable');
                      $('#confirm-cast-order').prop('disabled', true);
                      $('#confirm-cast-order').addClass('disable');

                      if (parseInt(pointUsed) > parseInt(pointUser)) {
                        var point = parseInt(tempPointCastOffer);
                      } else {
                        var point = parseInt(tempPointOrder) - parseInt(pointUser);
                      }
                  
                      window.location.href = '/payment/transfer?point=' + point;

                      return ;
                    } else {
                      createCastOffer(transfer);
                    }
                  }).catch(function(error) {
                    console.log(error);
                    if (error.response.status == 401) {
                      window.location = '/login';
                    }
                  });
              }).catch(function(error) {
                console.log(error);
                if (error.response.status == 401) {
                  window.location = '/login';
                }
              });
          } else {
            createCastOffer(transfer);
          }
        } else {
            window.location.href = '/mypage';
        }
      } else {
        createCastOffer();
      }
    });

    $('.redirect-mypage').on("click",function(event){
      window.location = '/mypage';
    })

    $('#btn-cancel-offer').on("click",function(event){
      $('#cancel-cast-offer').prop('checked', true);
    })

    if($('#order-status').length) {
      let orderStatus = parseInt($('#order-status').val());

      switch (orderStatus){

        case 2: //active
        case 3: //processing
        case 4: //done
          $('#timeout-offer-message h2').html('既に予約確定しています。');

          break;

        case 5: //done
          $('#timeout-offer-message h2').html('既に予約キャンセルしています。');

          break;

        case 6: //done
          $('#timeout-offer-message h2').html('こちらの飲み会の提案は取り下げられました。');

          break;

        case 7: // Order time out
          $('#timeout-offer-message h2').html('この予約の回答期限は終了しました');

          break;

        case 8: // Order time out
          $('#timeout-offer-message h2').html('既に予約キャンセルしています。');

          break;

        case 10: //guest denied order
          $('#timeout-offer-message h2').html('既に予約キャンセルしています。');

          break;

        case 11: //cast cancel order
          $('#timeout-offer-message h2').html('こちらの飲み会の提案は取り下げられました。');

          break;
      }

      if(1 != orderStatus && 9 != orderStatus) { // 1~open, 9~open_for_guest
        $('#timeout-offer').prop('checked',true);
      }
    }
  }
})
