$(document).ready(function(){
  const helper = require('./helper');
  if($('#btn-confirm-orders').length) {
    if(localStorage.getItem("order_call")){
      var orderCall = JSON.parse(localStorage.getItem("order_call"));

      if (orderCall.select_area) {
        var area = orderCall.select_area;

        if('その他' == area) {
          area = orderCall.text_area;
        }

        $('.word18').text(area);
      }

      if(orderCall.current_time_set) {
        var time = orderCall.current_time_set;

        if ('other_time' == time) {
          var day = orderCall.current_date;
          day = day.split('-');
          var time = orderCall.current_time;

          var year = day[0];
          var month = day[1];
          var date = day[2];

          $('.time-detail-call').text(year +'年' + month + '月' + date + '日' + time);
        } else {
          $('.time-detail-call').text(time + '分後');
        }
      }

      if (orderCall.current_duration) {
        var duration = orderCall.current_duration;

        if('other_duration' == duration) {
          duration = orderCall.select_duration;
        }

        $('.duration-call').text(duration + '時間')
      }

      if (orderCall.tags) {
        var tags = orderCall.tags;
        var html = '';
        (tags).forEach(function (data) {
          console.log(data)
          html +='<li class="details-info-list_kibun">'+data+'</li>';
        })
        $('.details-info-list').html(html);
      }

    } else {
      window.location.href = '/mypage';
    }
    
  }
})