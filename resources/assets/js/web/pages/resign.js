$(document).ready(function() {
  $("#withdraw").on("click", function(e) {
    var param = {
      reason1: localStorage.getItem('reason1'),
      reason2: localStorage.getItem('reason2'),
      reason3: localStorage.getItem('reason3'),
      other_reason: localStorage.getItem('other_reason'),
    }

    window.axios.post('/api/v1/resigns/create', param)
      .then(function(response) {
        window.location = '/resigns/complete';
      })
      .catch(function(error) {
        if (error.response.status == 409) {
          window.location = '/mypage';
        }

        if (error.response.status == 422) {
          $("#popup-resign").addClass("active");
        }

        if (error.response.status == 401) {
          window.location = '/login/line';
        }
      });
  })
});
