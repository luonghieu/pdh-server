$(function() {
  //
  $("#textareaCheck").on("click", function(){
    if ($("#textareaCheck").prop("checked") == true ) {
      $(".leave-comment__input textarea").prop("disabled", false).focus();
    } else {
      $(".leave-comment__input textarea").prop("disabled", true);
    }
  });

  // textarea 文字数　コントロール
  $(".leave-comment__input textarea").on("keydown keyup keypress change", function() {
    let sum = $(this).val().length;
    let key = event.which || event.keyCode || event.charCode;
    if (sum >= 181) {
      if (key != 8 ) {
        alert("180文字以内で入力してください");

        return false;
      }
    }

    $(".leave-comment__sum p").text(sum.toFixed());
  })

  //checkbox 判定
  $(".leave-reason-list .checkbox").on("click", function() {
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
    if ($("#textareaCheck").prop("checked") == true && $(".leave-comment__input textarea").val().length < 1 ){
      $('.js-resign-message').text("その他の理由が入力されていません");

      return false;
    }

    var reason1 = '';
    var reason2 = '';
    var reason3 = '';
    var other_reason = '';
    if (document.getElementById("reason1").checked) {
      var reason1 = 'サービスの使い方が分からない';
    }

    if (document.getElementById("reason2").checked) {
      var reason2 = '金額が高すぎる';
    }

    if (document.getElementById("reason3").checked) {
      var reason3 = '一緒に飲みたいキャストがいない';
    }

    if (document.getElementById("textareaCheck").checked) {
      var other_reason = $('textarea#description').val();
    }

    localStorage.setItem('reason1', reason1);
    localStorage.setItem('reason2', reason2);
    localStorage.setItem('reason3', reason3);
    localStorage.setItem('other_reason', other_reason);

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

  // check data when back
  if (localStorage.getItem('reason1') || localStorage.getItem('reason2') || localStorage.getItem('reason3') || localStorage.getItem('other_reason')) {
    $("#leaveSubmit").prop("disabled", false);
  }
})
