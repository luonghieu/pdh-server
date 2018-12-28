$(document).ready(function(){
  const helper = require('./helper');
  if ($('#sb-select-casts').length) {
    if(localStorage.getItem("order_call")){
      var orderCall = JSON.parse(localStorage.getItem("order_call"));
      var params = {
        class_id : orderCall.cast_class,
        latest : 1,
        order : 1,
      };

      window.axios.get('/api/v1/casts', {params})
      .then(function(response) {
        var data = response.data;
        var listCasts = (data.data.data);
        var html = '';
        listCasts.forEach(function (val) {
          html +='<div class="cast_block">';
          html += '<input type="checkbox" name="casts[]" value="'+val.id+'" id="'+val.id+'" class="select-casts">';
          html += '<div class="icon"> <p> <a href="" class="select-casts"';
          html += '<img class="lazy" data-src="" > </a> </p> </div>';
          html += '<span class="sp-name-cast text-ellipsis text-nickname">'+val.nickname+'('+val.age+')</span>'
          html += '<label for="'+val.id+'" class="label-select-casts" >指名する</label> </div>';
        })
        html += '<input type="hidden" id="next_page" value="'+data.data.next_page_url+'"';
        $('#list-cast-order').html(html);
      })
      .catch(function (error) {
        console.log(error);
        if (error.response.status == 401) {
          window.location = '/login/line';
        }
      });
    } else {
      window.location.href = '/mypage';
    }
  }
});
