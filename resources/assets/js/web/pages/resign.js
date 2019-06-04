$(document).ready(function() {
  $("#withdraw").on("click", function(e) {
    var params = {
      reason1: localStorage.getItem('reason1'),
      reason2: localStorage.getItem('reason2'),
      reason3: localStorage.getItem('reason3'),
      other_reason: localStorage.getItem("other_reason") ? localStorage.getItem('textarea_reason') : '',
    }

    window.axios.post('/api/v1/resigns/create', params)
      .then(function(response) {

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
        
        localStorage.removeItem("textarea_reason");

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
