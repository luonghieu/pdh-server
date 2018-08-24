$(document).ready(function(){
  $('#change-point-form').click(function(e){
    e.preventDefault();

    var id = $('#link-change-point').attr('data-user-id');
    $.ajax({
      url: '/admin/casts/' + id + '/operation_history',
      method: 'PUT',
      data: {
        _token: $('meta[name="csrf-token"]').attr('content'),
        point: $('#point').val(),
      },
      success: function(result, xhr){
        console.log(result);
      },
      error: function(xhr) {
        $('#point-alert').empty();
        const errors = xhr.responseJSON.errors;

        for(const error of  errors) {
          $('#point-alert').append(`<strong>${error}</strong>`);
        }
      }
    }, "json");
  });
});
