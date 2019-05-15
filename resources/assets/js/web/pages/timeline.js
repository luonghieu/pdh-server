$(document).ready(function () {
  // Like/unlike timeline in timeline detail
  $('body').on('click', '#heart-timeline', function(e) {
    var _this = $(this);
    var id = _this.attr('data-timeline-id');
    total_favorites = _this.attr('data-total-favorites-timeline');
    is_favorited_timeline = _this.attr('data-is-favorited-timeline');

    var nickname = $('#nickname').val();
    var age = $('#age').val();
    var avatar = $('#avatar').val();
    var userId = $('#timeline-user-id').val();

    window.axios.post('/api/v1/timelines/' + id + '/favorites')
      .then(function(response) {
        var total = parseInt(total_favorites);
        if (is_favorited_timeline == 0) {
          var total = total + 1;
          _this.html(`<img class="init-cursor" src="/assets/web/images/common/like.svg">`);
          _this.attr('data-total-favorites-timeline', total);

          var html = `<div class="timeline-like-item user-` + userId + `">
              <div class="timeline-like-item__profile">
                <img src="` + avatar + `" alt="">
              </div>
              <div class="timeline-like-item__info">
                <p>` + nickname + `</p>
                <p>` + age + `歳</p>
              </div>
            </div>`;

          $('.js-add-favorite').before(html);
        } else {
          var total = total - 1;
          _this.html(`<img class="init-cursor" src="/assets/web/images/common/unlike.svg">`)
          _this.attr('data-total-favorites-timeline', total);

          $('.user-' + userId).remove();
        }

        $('#total-favorites').text(total);
        _this.attr('data-is-favorited-timeline', is_favorited_timeline == 1 ? 0 : 1);
      })
      .catch(function(error) {
        if (error.response.status == 401) {
          window.location = '/login/line';
        }
      });
  });   /* End like/unlike timeline in timeline detail */

  /* Post timeline */
  var formDataTimeline = new FormData();
  var flagContent = false;
  var flagImage = false;

  $(document).on("keyup", ".timeline-edit__area", function(){
    const str = $(".timeline-edit__text").text();

    let sum = Array.from(str.split(/[\ufe00-\ufe0f]/).join("")).length;

    if (sum >= 1) {
      flagContent = true;
      $('#timeline-btn-submit').addClass('btn-submit-timeline-blue');

      $('#timeline-btn-submit').removeAttr('disabled');
    } else {
      flagContent = false;
      if (flagImage || flagContent) {
        $('#timeline-btn-submit').addClass('btn-submit-timeline-blue');
        $('#timeline-btn-submit').removeAttr('disabled');
      } else {
        $('#timeline-btn-submit').removeClass('btn-submit-timeline-blue');
        $('#timeline-btn-submit').attr('disabled','disabled');
      }
    }
    if (sum > 240) {
      return false;
    }

    $(".timeline-edit-sum__text").text(sum.toFixed() );
  });

  $(document).on("keydown", ".timeline-edit__area", function(e){
    const str = $(".timeline-edit__text").text();
    let sum = Array.from(str.split(/[\ufe00-\ufe0f]/).join("")).length;

    var keyCode = e.keyCode;

    if(keyCode == 8 || keyCode == 46 || keyCode == 37 || keyCode == 39) {
      return true;
    }

    if (sum >= 240) {
      return false;
    }

    flagContent = true;
    $('#timeline-btn-submit').addClass('btn-submit-timeline-blue');
    $('#timeline-btn-submit').removeAttr('disabled');
  });


  $(document).on("keydown", "#positionInput", function(e){
    let sum = $("#positionInput").val().length ;

    let keyCode = e.keyCode;

    if(keyCode == 8 || keyCode == 46 || keyCode == 37 || keyCode == 39) {
      return true;
    }

    if (sum >= 20) {
      return false;
    }
  });

  /////////////////////////////////
  //   timeline image
  ////////////////////////////////


  let timelineEditPic = $(".timeline-edit-pic");
  let timelineEditCamera = $(".timeline-edit-camera");

  timelineEditPic.on("change",function(e){
    formDataTimeline.delete('image');
    var _insertPicture = e.target.files[0];

    postImage(_insertPicture);
  });

  timelineEditCamera.on("change",function(e){
    formDataTimeline.delete('image');
    var _insertPicture = e.target.files[0];

    postImage(_insertPicture);
  });

  function postImage(img) {
    $('#timeline-btn-submit').addClass('btn-submit-timeline-blue');
    var reader = new FileReader();

    reader.onload = function(e){
      $('.timeline-edit-image').empty();

      $('.timeline-edit__area').append(`<div class='timeline-edit-image' contenteditable='false'><img src=` + e.target.result +`><div class='timeline-edit-image__del'><img src='/assets/web/images/timeline/timeline-create-img_del.svg'></div></div>`);
    };
    reader.readAsDataURL(img);
    formDataTimeline.append('image', img);
    flagImage = true;
    $('#timeline-btn-submit').addClass('btn-submit-timeline-blue');
    $('#timeline-btn-submit').removeAttr('disabled');
    $('.timeline-edit__text').focus();
  }

  $(document).on("click", ".timeline-edit-image__del", function(){
    formDataTimeline.delete('image');
    $(".timeline-edit-pic").append("<input type='file' style='display: none' name='image' accept='image/*'>");
    $(".timeline-edit-camera").append("<input type='file' style='display: none' name='image' accept='image/*'>");
    $(this).parent(".timeline-edit-image").fadeOut(300,function(){
      $(this).remove();
    });

    flagImage = false;
    if (flagImage || flagContent) {
      $('#timeline-btn-submit').addClass('btn-submit-timeline-blue');
      $('#timeline-btn-submit').removeAttr('disabled');
    } else {
      $('#timeline-btn-submit').removeClass('btn-submit-timeline-blue');
      $('#timeline-btn-submit').attr('disabled','disabled');
    }

    $('.timeline-edit__text').focus();
  });

  //////////////////////////////////////
  //          timeline-edit-position
  /////////////////////////////////

  let $timelineEditPosition = $(".timeline-edit-position img");

  $(document).on("click", "#positionOk", function(){
    let positionText = $("#positionInput").val();
    $(".user-info__bottom p").text(positionText);
    document.getElementById('add-location').click()
  });


  var userId = $('#create-timeline-user-id').val();

  $('#timeline-btn-submit').on('click', function () {
    let location = $('.user-info__bottom p').text().trim();
    let content = $('.timeline-edit__text').html().replace(/<div>/gi,`\n`).replace(/<\/div>/gi,``);

    if (content !== null) {
      formDataTimeline.append('content', content);
    }

    if (location !== null) {
      formDataTimeline.append('location', location);
    }

    formDataTimeline.append('user_id', userId);

    window.axios.post('/api/v1/timelines/create', formDataTimeline)
      .then(function(response) {
       window.location.href = '/timelines';
      })
      .catch(function(error) {
        console.log(error);
      });
  })

  //////////////////////////////////////
  //          timeline-delete-post
  /////////////////////////////////

  $('.timeline .btn_cancel').on('click', function () {
    $('#del-post-timeline').trigger('click');
  });

  $('.timeline-edit__text').focus();



  $('.timeline-edit__text').bind("DOMSubtreeModified",function(){
    const str = $(".timeline-edit__text").text();
    let sum = Array.from(str.split(/[\ufe00-\ufe0f]/).join("")).length;

    if (sum > 240) {
      $(this).html(Array.from(str.split(/[\ufe00-\ufe0f]/).join("")).slice(0,240));
      setCaretPosition('timeline-edit-content', str)
    }

    $('#timeline-btn-submit').addClass('btn-submit-timeline-blue');
    $('#timeline-btn-submit').removeAttr('disabled');

    if (sum > 0) {
      $('.timeline-edit__text').removeClass('pl');
    } else {
      $('.timeline-edit__text').addClass('pl');
    }
  });

  $(".timeline-edit__text").bind({
      paste : function(){
        setTimeout(function () {
          const str = $(".timeline-edit__text").text();
          let sum = Array.from(str.split(/[\ufe00-\ufe0f]/).join("")).length;
          $(".timeline-edit-sum__text").text(sum.toFixed() );
        }, 100);
      },
  });

  $("#positionInput").bind({
      paste : function(){
        setTimeout(function () {
          const str = $("#positionInput").val();
          if (str.length > 20) {
            $("#positionInput").val(str.slice(0,20));
            $('#positionInput').focus();
          }
        }, 100);
      },
  });

  $("#positionInput").on('change', function () {
    const str = $("#positionInput").val();
    if (str.length > 20) {
      $("#positionInput").val(str.slice(0,20));
      $('#positionInput').focus();
    }
  })
  /* End Post timeline */
});

function setCaretPosition(elementId, str){
  const editableDiv = document.getElementById(elementId);
  const selection = window.getSelection();
  selection.collapse(editableDiv.childNodes[editableDiv.childNodes.length - 1], str.length);
}
