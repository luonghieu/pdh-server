const helper = require('./helper');

function handelSelectedTags()
{
  $(".form-grpup .checkbox-tags").on("change",function(event){
    var tagName = $(this).children().val();
    var activeSum = $(".active").length;
    if ($(this).hasClass("active")) {
      $(this).children().prop('checked',false);
      $(this).removeClass('active');

      if(localStorage.getItem("order_call")){
        var tags = JSON.parse(localStorage.getItem("order_call")).tags;
        if(tags) {
          if(tags.indexOf(tagName) > -1) {
            tags.splice(tags.indexOf(tagName), 1);
          }

          var params = {
            tags: tags,
          };

        }
      }
    } else {
      if(activeSum >= 5) {
        $('#max-tags').prop('checked', true);
        $(this).children().prop('checked',false);
        $(this).removeClass('active');
      } else {
        $(this).children().prop('checked',true);
        $(this).addClass('active');

        if(localStorage.getItem("order_call")){
          var tags = JSON.parse(localStorage.getItem("order_call")).tags;
          if(tags) {
            tags.push(tagName);

            var params = {
              tags: tags,
            };
            
          } else {
            var tags = [tagName];
            var params = {
              tags: tags,
            };
          }
        } else {
          var tags = [tagName];
          var params = {
              tags: tags,
            };
        }
      }
    }

    if(params) {
      helper.updateLocalStorageValue('order_call', params);
    }
  });
}

$(document).ready(function () {
  handelSelectedTags();
});
