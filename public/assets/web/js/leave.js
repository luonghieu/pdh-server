$(function() {
  //
  var reason1 = '';
  var reason2 = '';
  var reason3 = '';
  var other_reason = '';

  $("#textareaCheck").on("click", function(){
    if ($("#textareaCheck").prop("checked") == true ) {
      $(".leave-comment__input textarea").prop("disabled", false).focus();
    } else {
      $(".leave-comment__input textarea").prop("disabled", true);
    }
  });
  // textarea 文字数　コントロール
  $(".leave-comment__input textarea").on("keydown keyup keypress change", function(e) {
    let sum = $(this).val().length;
    $(".leave-comment__sum p").text(sum.toFixed());
  });

  //checkbox 判定
  $(".leave-reason-list .checkbox").on("click", function() {
    if(reason1 || reason2 || reason3 || other_reason) {
      $('.js-resign-message').text('');
    }

    if ($(".cb-cancel:checked").length > 0 ) {
      $(".leave-submit").prop("disabled", false);
    } else {
      $(".leave-submit").prop("disabled", true);
    }
  })

  // check textarea
  $('textarea#description').focusout(function() {
    $('.js-resign-message').text('');

    if ($(this).val().length < 1) {
      $('.js-resign-message').text("その他の理由が入力されていません");
    }
  })

  $("#leaveSubmit").on("click", function() {
    if ($("#textareaCheck").prop("checked") == true && $(".leave-comment__input textarea").val().trim().length < 1 ){
      $('.js-resign-message').text("その他の理由が入力されていません");

      return false;
    }

    if (document.getElementById("reason1").checked) {
      reason1 = 'サービスの使い方が分からない';

      localStorage.setItem('reason1', reason1);
    }

    if (document.getElementById("reason2").checked) {
      reason2 = '金額が高すぎる';

      localStorage.setItem('reason2', reason2);
    }

    if (document.getElementById("reason3").checked) {
      reason3 = '一緒に飲みたいキャストがいない';

      localStorage.setItem('reason3', reason3);
    }

    if (document.getElementById("textareaCheck").checked) {
      other_reason = $('textarea#description').val().trim();

      localStorage.setItem('other_reason', other_reason);
    }

    window.location.href = '/resigns/confirm';
  })

  // leave_confirm page
  $(".leave-footer__check .checkbox").on("change", function(e) {
    if (document.getElementById("check-agree").checked) {
      $("#withdraw").prop("disabled", false);
    } else {
      $("#withdraw").prop("disabled", true);
    }
  })

  $("#resign-status").on("click", function(e) {
    if(localStorage.getItem("reason1")){
      localStorage.removeItem("reason1");
    }

    if(localStorage.getItem("reason2")){
      localStorage.removeItem("reason2");
    }

    if(localStorage.getItem("reason3")){
      localStorage.removeItem("reason3");
    }

    if(localStorage.getItem("other_reason")){
      localStorage.removeItem("other_reason");
    }
  })
  
  // check data when back
  if (localStorage.getItem('reason1') || localStorage.getItem('reason2') || localStorage.getItem('reason3') || localStorage.getItem('other_reason')) {
    $("#leaveSubmit").prop("disabled", false);
  }

  if($("#leaveSubmit").length) {
    $(window).on('load', function(){
      if(localStorage.getItem("reason1")){
        localStorage.removeItem("reason1");
      }

      if(localStorage.getItem("reason2")){
        localStorage.removeItem("reason2");
      }

      if(localStorage.getItem("reason3")){
        localStorage.removeItem("reason3");
      }

      if(localStorage.getItem("other_reason")){
        localStorage.removeItem("other_reason");
      }
    });
  }
})
