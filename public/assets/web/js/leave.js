$(function() {
  //
  $("#textareaCheck").on("click", function(){
    if ($("#textareaCheck").prop("checked") == true ) {
      console.log("yes")
      $(".leave-comment__input textarea").prop("disabled", false).focus();
    } else {
      console.log("no");
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

  $("#leaveSubmit").on("click", function() {
    if ($("#textareaCheck").prop("checked") == true && $(".leave-comment__input textarea").val().length < 1 ){
      $('.js-resign-message').text("その他の理由が入力されていません");

      return false;
    }
  })

  // leave_confim page
  $(".leave-footer__check .checkbox").on("click", function(e) {
    if ($("input[type='checkbox']").prop("checked")) {
      $(".leave-submit").prop("disabled", false);
    } else {
      $(".leave-submit").prop("disabled", true);
    }
  })

  $("#withdraw").on("click", function(e) {
    e.preventDefault();

    if (!$(".modal_overlay").hasClass("active")) {
      $(".modal_overlay").addClass("active");
    } else {
      $(".modal_overlay").removeClass("active");
    }
  })
})
