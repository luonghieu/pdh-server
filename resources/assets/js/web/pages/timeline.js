$(document).ready(function() {
  var formDataTimeline = new FormData();
  var flagContent = false;
  var flagImage = false;

  $(document).on("keyup", ".timeline-edit__area", function(){
    let sum = $(".timeline-edit__text").text().length ;

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
    let sum = $(".timeline-edit__text").text().length ;
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

  /////////////////////////////////
  //   timeline image
  ////////////////////////////////


  let timelineEditPic = $(".timeline-edit-pic");
  let timelineEditCamera = $(".timeline-edit-camera");

  timelineEditPic.on("change",function(e){
    var _insertPicture = e.target.files[0];

    postImage(_insertPicture);
    $(".timeline-edit-pic input").remove();
  });

  timelineEditCamera.on("change",function(e){
    var _insertPicture = e.target.files[0];

    postImage(_insertPicture);
    $(".timeline-edit-camera input").remove();
  });

  function postImage(img) {
    $('#timeline-btn-submit').addClass('btn-submit-timeline-blue');
    var reader = new FileReader();

    reader.onload = function(e){
      $('.timeline-edit__area').append(`<div><br></div><div class='timeline-edit-image' contenteditable='false'><img src=` + e.target.result +`><div class='timeline-edit-image__del'><img src='/assets/web/images/timeline/timeline-create-img_del.svg'></div></div><div><br></div>`);
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
    if( positionText != "" ){
      $(".user-info__bottom p").text(positionText);
      document.getElementById('add-location').click()
    }

    $('.timeline-edit__text').focus();
  });


  var userId = $('#create-timeline-user-id').val();

  $('#timeline-btn-submit').on('click', function () {
    let content = $('.timeline-edit__text').text().trim();
    let location = $('.user-info__bottom p').text().trim();
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
  })

  $('.timeline-edit__text').focus();
});
