$(document).ready(function(){
  function submitSquareForm() {
    var nonce = $("#card-nonce").val();

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: "POST",
      dataType: "json",
      url: '/webview/card/create',
      data: {
        nonce: nonce,
      },
      success: function (msg) {
        if (!msg.success) {
          var error = msg.error;
          $(".notify span").text(error);
        } else {
          window.location.href = backUrl;
        }
      },
    });
  }
});
