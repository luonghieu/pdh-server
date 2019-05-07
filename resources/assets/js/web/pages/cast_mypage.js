$(document).ready(function() {
  $('#triggerVerify').trigger('click');
  $('#change-point').on('click', function(event){
    var check = $('#point-cast').is(':disabled');
    if(check) {
      $('#sp-text-point').css('color','#00C3C3');
      $('#point-cast').prop('disabled', false);
      $('#point-cast').css('background-image', 'url(/assets/webview/images/IC_down.png)');
    }else {
      $('.update-cost').click();
    }
  })

  $('.cf-update-cost').on('click', function(event){
    var params = {
        cost: $('#point-cast').val()
    };

    window.axios.post('/api/v1/auth/update', params)
      .then(function(response) {
        $('#update-point-success').html('変更が完了しました！');
        $('#update-point-alert').trigger('click');

        setTimeout(() => {
          window.location = '/mypage';
        }, 1500);
      })
      .catch(function(error) {
        if (error.response.status == 401) {
          window.location = '/login/line';
        }
      });
  })

  var numOfAvgRatePlatium = $('#num-of-avg-rate-platium').val();
  var wid = 0;

  if (numOfAvgRatePlatium) {
    wid = (numOfAvgRatePlatium * 100)/ 5;
  }

  $('body').append('<style>#star-rating-schedule:before{width: ' + wid +'px;}</style>');
});
