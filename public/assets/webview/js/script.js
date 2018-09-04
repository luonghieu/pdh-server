function numberCardLength(event)
{
  var str = document.getElementById("number-card").value;
  var strlen = str.length;
  var keyCode = event.keyCode;
  if (keyCode == 8 || keyCode == 46 || keyCode == 37 || keyCode == 39)
  {
    return true;
  }

  if (strlen >= 16) {
    return false;
  }
}

function numberCvvLength(event)
{
  var str = document.getElementById("card-cvv").value;
  var strlen = str.length;
  var keyCode = event.keyCode;
  if (keyCode == 8 || keyCode == 46 || keyCode == 37 || keyCode == 39)
  {
    return true;
  }

  if (strlen >= 4) {
    return false;
  }

}
var flag = false;
var flag_color = false;
function creditValidate()
{
  var str = document.getElementById("number-card").value;
  var visa = '^4[0-9]{12}(?:[0-9]{3})?$';
  var mastercard = '^5[1-5][0-9]{14}$';
  var americanExpress = '^3[47][0-9]{13,14}$';

  if(str.match(visa) || str.match(mastercard) || str.match(americanExpress) || str === "") {
    var element = document.getElementById("error");
    element.classList.remove("error");
    var element2 = document.getElementById("number-card-display");
    element2.classList.add("number-true");
    var element3 = document.getElementById("number-card");
    element3.classList.add("color-caret");
    flag = true;
  } else{
    var element = document.getElementById("number-card-display");
    element.classList.remove("number-true");
    var element2 = document.getElementById("number-card");
    element2.classList.remove("color-caret");
    var element1 = document.getElementById("error");
    element1.classList.add("error");
    flag = false;
  }
  var changeString = "";
  var count = 3;
  for (var i = 0; i < str.length; i++) {
    changeString+=str.charAt(i);
    if(i == count) {
      changeString+=" ";
      count+=4;
    }
  }
  if (str === ""){
    document.getElementById("number-card-display").innerHTML = "0000 0000 0000 0000";
    document.getElementById("number-card-display").classList.remove('number-true');
    document.getElementById("number-card-display").classList.add('color-placeholder');
  } else {
    document.getElementById("number-card-display").innerHTML = changeString;
    document.getElementById("number-card-display").classList.remove('color-placeholder');
    document.getElementById("number-card-display").classList.add('color');
  }
  if(flag && flag_color){
    $('#btn-create').css('color', "#ff6090");
  }else{
    $('#btn-create').css('color', "#cccccc");
  }
}

function addColor()
{
  var str = document.getElementById("card-cvv").value;
  var strlen = str.length;
  var parsed = Number.parseInt(str);

  if (((strlen == 3 || strlen == 4) && !Number.isNaN(parsed)) || str === "" ) {
    var element = document.getElementById("error-cvv");
    element.classList.remove("error-cvv");
    var element1 = document.getElementById("card-cvv");
    element1.classList.remove("card-cvv-color");
    var element2 = document.getElementById("card-cvv");
    element2.classList.add("number-true");
    flag_color = true;
  } else {
    var element = document.getElementById("card-cvv");
    element.classList.remove("number-true");
    var element1 = document.getElementById("error-cvv");
    element1.classList.add("error-cvv");
    var element2 = document.getElementById("card-cvv");
    element2.classList.add("card-cvv-color");
    flag_color = false;
  }
  if(flag && flag_color){
    $('#btn-create').css('color', "#ff6090");
  }else{
    $('#btn-create').css('color', "#cccccc");
  }
}

$(document).ready(function(){
  $('#btn-create').on('click', function (e) {
    var numberCard = $("#number-card").val();
    var month = $("#month").val();
    var year = $("#year").val();
    var cardCvv = $("#card-cvv").val();

      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "POST",
        dataType: "json",
        url: '/webview/card/create',
        data: {
          number_card: numberCard,
          month: month,
          year: year,
          card_cvv: cardCvv,
        },
        success: function( msg ) {
          if(!msg.success) {
            var error = msg.error;
            $(".notify span").text(error);
          } else {
            window.location = msg.url;
          }
        },
      });
  });
});
