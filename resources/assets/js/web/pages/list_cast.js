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

      if(orderCall.arrIds) {
        var arrIds = orderCall.arrIds;
      } else {
        var arrIds = [];
      }

      window.axios.get('/api/v1/casts', {params})
      .then(function(response) {
        var data = response.data;
        var listCasts = (data.data.data);
        var html = '';
        listCasts.forEach(function (val) {
          if(arrIds.indexOf(val.id.toString()) > -1) {
            var checked = 'checked';
            var text = 'リクエスト中';
            var detail = 'cast-detail';
          } else {
            var checked = '';
            var text = 'リクエストする';
            var detail = '';
          }

          if(val.avatars.length) {
            if (val.avatars[0].path) {
              var show ='<img src= "' + val.avatars[0].thumbnail + '" class="img-cast" >';
            } else {
              var show ='<img src= "' + avatarsDefault + '" class="img-cast" >';
            }
          } else {
            var show ='<img src= "' + avatarsDefault + '" class="img-cast" >';
          }

          html +='<div class="cast_block">';
          html += '<input type="checkbox" name="casts[]" value="'+ val.id +'" id="'+ val.id +'" class="select-casts"'+ checked +'>';
          html += '<div class="icon"> <p> <a href="' + link + val.id + '/call" class="cast-link '+ detail + '" >';
          html += show + ' </a> </p> </div>';
          html += '<span class="sp-name-cast text-ellipsis text-nickname">'+ val.nickname +'('+ val.age + ')</span>'
          html += '<label for="'+ val.id +'" class="label-select-casts" >' + text + '</label> </div>';
        })

        var nextPage = '';
        if (data.data.next_page_url) {
          var nextPage = data.data.next_page_url;
        }

        html += '<input type="hidden" id="next_page" value="' + nextPage + '" />';
        $('#list-cast-order').html(html);
        $('.img-cast').error(function(){
          $(this).attr("src", avatarsDefault);
        });
      })
      .catch(function (error) {
        console.log(error);
        if (error.response.status == 401) {
          window.location = '/login';
        }
      });

      function checkedCasts() {
        if(localStorage.getItem("order_call")){
          var arrIds = JSON.parse(localStorage.getItem("order_call")).arrIds;
          if(arrIds) {
            if(arrIds.length) {
              const inputCasts = $('.select-casts');
              $.each(inputCasts,function(index,val){
                if(arrIds.indexOf(val.value) > -1) {
                  $(this).prop('checked',true);
                  $(this).parent().find('.cast-link').addClass('cast-detail');
                  $('.label-select-casts[for='+  val.value  +']').text('リクエスト中');
                }
              })

              $(".cast-ids").val(arrIds.toString());
              $('#sb-select-casts a').text('次に進む(3/4)');
            }
          }
        }
      }

      /*Load more list cast order*/
      var requesting = false;
      var windowHeight = $(window).height();

      function needToLoadmore() {
        return requesting == false && $(window).scrollTop() >= $(document).height() - windowHeight - 500;
      }

      function handleOnLoadMore() {
        // Improve load list image
        $('.lazy').lazy({
            placeholder: "data:image/gif;base64,R0lGODlhEALAPQAPzl5uLr9Nrl8e7..."
        });

        if (needToLoadmore()) {
          var url = $('#next_page').val();

          if (url) {
            requesting = true;
            window.axios.get(loadMore, {
              params: { next_page: url },
            }).then(function (res) {
              res = res.data;
              $('#next_page').val(res.next_page || '');
              $('#next_page').before(res.view);
              checkedCasts();
              requesting = false;
            }).catch(function () {
              requesting = false;
            });
          }
        }
      }
      setTimeout(() => {

        $(document).on('scroll', handleOnLoadMore);
        $(document).ready(handleOnLoadMore);
        checkedCasts();
      }, 500);
      /*!----*/


      if (localStorage.getItem("order_call")) {
        var countIds = JSON.parse(localStorage.getItem("order_call")).countIds;
        if (localStorage.getItem("full")) {
            var text = ' 指名できるキャストは'+ countIds + '名です';
            $('#content-message h2').text(text);
            $('#max-cast').prop('checked',true);
            localStorage.removeItem("full");
        }
      }
    } else {
      window.location.href = '/mypage';
    }
  }
});
