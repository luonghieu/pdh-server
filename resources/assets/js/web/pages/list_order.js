$(document).ready(function(){
  $('.lb-cancel').on('click', function (e) {
    var id = $(this).data('id');
    $('.cf-cancel-order').on('click', function (e) {
      window.axios.post('/api/v1/orders/' + id+'/cancel');
      $( ".lb-modal-cancel" ).click();
      $('.md-cancel-order').on('click', function (e) {
        window.location.reload();
      });
    });
  });
});
