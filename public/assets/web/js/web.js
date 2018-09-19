$(document).ready(function(){

  $('.lb-cancel').on('click', function (e) {
    var id = $(this).data('id');
    $('.cancel-order').on('click', function (e) {

      $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: "POST",
          dataType: "html",
          url: '/guest/orders/cancel',
          data: {id: id},
          success: function( msg ) {
            window.location.reload();
          },
      });
    });
  });
});
