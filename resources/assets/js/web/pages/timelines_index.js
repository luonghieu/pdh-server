const PullToRefresh = require('pulltorefreshjs');
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
                    selected.html('<img src="'+ btnLike +'">');
                } else {
                    selected.html('<img src="'+ btnNotLike +'">');
                }

                selected.next().html('<a href="'+ link +'/'+ response.data.data.id +'">'+ response.data.data.total_favorites +'</a>');
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
        console.log('123123123');
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
        var userId = null;
        if($('#user_id_timelines').length) {
            userId = $('#user_id_timelines').val();
        }

        var params = {
            user_id: userId,
        };

        window.axios.get('/api/v1/timelines', {params})
          .then(function(response) {
            var data = response.data;
            var timelines = (data.data.data);
            var html = '';

            timelines.forEach(function (val) {
                if(val.user.avatars.length) {
                    if (val.user.avatars[0].path) {
                      var show ='<img src= "' + val.user.avatars[0].thumbnail + '"  >';
                    } else {
                      var show ='<img src= "' + avatarsDefault + '"  >';
                    }
                } else {
                    var show ='<img src= "' + avatarsDefault + '"  >';
                }

                var link = showDetail +'/' + val.id;

                html +='<div class="timeline-item" id="timeline-'+ val.id +'"> <div class="user-info"> <div class="user-info__profile"> ';
                if(userType.GUEST == val.user.type) {
                    html += '<a href="'+ guestDetail +'/' + val.user.id + '">';
                } else {
                    html += '<a href="'+ castDetail +'/' + val.user.id + '">';
                }
                html += show + '</a></div>';
                html += '<a href="'+ link +'">';
                html += '<div class="user-info__text"> <div class="user-info__top">';
                html += '<p>' + val.user.nickname + '</p>' + '<p>' + val.user.age + '???</p> </div> ';
                html += '<div class="user-info__bottom">';
                if(val.location.length >= 18) {
                    html += '<p style="font-size: 10px">';
                } else {
                    html += '<p>';
                }

                html += val.location + (val.location ? '???' : '') + moment(val.created_at).format('MM/DD HH:mm') +'</p> </div></div> </a>';

                if ($('#user_id_login').val() == val.user.id) {
                    html += '<div class="timeline-delete" data-id="'+ val.id +'"> <img src="'+ btnTimelineDel +'" alt=""> </div>';
                }

                html += '</div>';
                html += '<div class="timeline-content"> <a href="'+ link +'"> <div class="timeline-article"> <div class="timeline-article__text init-text-justify">';
                html += val.content.replace(/\n/g, "<br />") + '</div></div>';

                if(val.image) {
                    html += '<div class="timeline-images"> <div class="timeline-images__list"> <div class="timeline-images__item">';
                    html += '<img class="rotate" src="'+ val.image +'" width="100%"></div></div></div>';
                }

                html += '</a><div class="timeline-like"> <button class="timeline-like__icon" data-id="'+ val.id +'">';
                if(val.is_favourited) {
                    html += '<img src="'+ btnLike +'"> </button>';
                } else {
                    html += '<img src="'+ btnNotLike +'"> </button>';
                }

                html += '<p class="timeline-like__sum"><a href="'+ link +'">'+ val.total_favorites +'</a> </p> </div></div></div>';
            })

            var nextPage = '';
            if (data.data.next_page_url) {
              var nextPage = data.data.next_page_url;
            }

            html += '<input type="hidden" id="next_page" value="' + nextPage + '" />';
            html += loadingIconButtom;

            $('.timeline-list').html(html);
              setTimeout(() => {
                  const imgs = document.getElementsByClassName('rotate');
                  if(imgs.length > 0) {
                      for (let img of imgs) {
                          EXIF.getData(img, function() {
                              const orientation = EXIF.getTag(this, "Orientation");
                              if (orientation === 6) {
                                  img.setAttribute('style', 'transform: rotate(90deg)');
                              }
                          });
                      }
                  }
              }, 1000);
        })
        .catch(function (error) {
            console.log(error);
            if (error.response.status == 401) {
              window.location = '/login';
            }
        });
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
                $('.js-loading').removeClass('css-loading-none');
                requesting = true;

                window.axios.get(loadMoreTimelines, {
                  params: { next_page: url },
                }).then(function (res) {
                  res = res.data;
                  $('#next_page').val(res.next_page || '');
                  $('#next_page').before(res.view);

                  requesting = false;
                  // Add page loading icon
                  $('.js-loading').addClass('css-loading-none');
                }).catch(function () {
                  requesting = false;
                  // Add page loading icon
                  $('.js-loading').addClass('css-loading-none');
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

    const loadingIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-eclipse" style="background: none;"><path ng-attr-d="{{config.pathCmd}}" ng-attr-fill="{{config.color}}" stroke="none" d="M10 50A40 40 0 0 0 90 50A40 55 0 0 1 10 50" fill="#30ccc3" transform="rotate(255.455 50 57.5)"><animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 57.5;360 50 57.5" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"/></path></svg>`;
    if ($('#timeline-index').length) {
        const ptr = PullToRefresh.init({
            mainElement: '#timeline-index',
            instructionsPullToRefresh: ' ',
            instructionsReleaseToRefresh: ' ',
            iconArrow: loadingIcon,
            iconRefreshing: loadingIcon,
            instructionsRefreshing: ` `,
            shouldPullToRefresh() {
                const divTop = $('#timeline-index').offset().top;
                if ($(window).scrollTop() > divTop - 110) {
                    return false;
                } else {
                    return true;
                }
            },
            onRefresh() {
                window.location.reload();
            },
        });
    }

    const loadingIconButtom = `<div class="sk-circle js-loading css-loading-none">
            <div class="sk-circle1 sk-child"></div>
            <div class="sk-circle2 sk-child"></div>
            <div class="sk-circle3 sk-child"></div>
            <div class="sk-circle4 sk-child"></div>
            <div class="sk-circle5 sk-child"></div>
            <div class="sk-circle6 sk-child"></div>
            <div class="sk-circle7 sk-child"></div>
            <div class="sk-circle8 sk-child"></div>
            <div class="sk-circle9 sk-child"></div>
            <div class="sk-circle10 sk-child"></div>
            <div class="sk-circle11 sk-child"></div>
            <div class="sk-circle12 sk-child"></div>
          </div>
        </div>`;

});
