const userType = {
  'GUEST': 1,
  'CAST': 2,
  'ADMIN': 3
};

function handleFavouritedTimelines(link)
{
    $('body').on('click', ".timeline-like__icon", function(){
        var id = $(this).data("id");
        var selected = $(this);
        window.axios.post('/api/v1/timelines/' + id +'/favorites')
            .then(function(response) {
                if(response.data.data.is_favourited) {
                    $(selected).html('<img src="'+ btnLike +'">');
                } else {
                    $(selected).html('<img src="'+ btnNotLike +'">');
                }

                $(selected).next().html('<a href="'+ link +'/'+ response.data.data.id +'">'+ response.data.data.total_favorites +'</a>');
            })
            .catch(function (error) {
                console.log(error);
                if (error.response.status == 401) {
                  window.location = '/login';
                }
            });
    })
}

function handleDelTimeline()
{
    $('body').on('click', ".timeline-delete", function(){
        var id = $(this).data("id");

        $('#btn-del-timeline').data('id', '');
        $('#btn-del-timeline').data('id', id);

        $('#timeline-del').prop('checked', true);

    })

    $('body').on('click', "#btn-del-timeline", function(){
        var id = $(this).data("id");
        if(id) {
            window.axios.delete('/api/v1/timelines/' + id)
                .then(function(response) {
                    $('#timeline-del').prop('checked', false);
                    $('#timeline-' + id).remove();
                })
                .catch(function (error) {
                    console.log(error);
                    if (error.response.status == 401) {
                      window.location = '/login';
                    }

                    if (error.response.status == 404) {
                      $('#timeline-not-found').prop('checked', true);
                    }
                });
        } else {
            window.location = '/login';
        }
    })
}

$(document).ready(function(){
  const helper = require('./helper');
    if($('#timeline-index').length) {
 
          /*Load more list cast order*/
        var requesting = false;
        var windowHeight = $(window).height();

        function needToLoadmore() {
            return requesting == false && $(window).scrollTop() >= $(document).height() - windowHeight - 1000;
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
                window.axios.get(loadMoreTimelines, {
                  params: { next_page: url },
                }).then(function (res) {
                  res = res.data;
                  $('#next_page').val(res.next_page || '');
                  $('#next_page').before(res.view);
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
        }, 500);

        handleFavouritedTimelines(showDetail);
        handleDelTimeline();
    }
});
