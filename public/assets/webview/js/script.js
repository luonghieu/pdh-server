var flag = false;
var flag_color = false;
function creditValidate()
{
  var numberCard = new Cleave('#number-card', {
    creditCard: true,
  });

  var str = document.getElementById("number-card").value;
  var visa = '^4[0-9]{12}(?:[0-9]{3})?$';
  var mastercard = '^5[1-5][0-9]{14}$';
  var americanExpress = '^3[47][0-9]{13,14}$';
  var dinnersClub = '^3(?:0[0-5]|[68][0-9])[0-9]{11}$';
  var jcb = '^(?:2131|1800|35\\d{3})\\d{11}$';
  str = str.replace(/\s/g, '');

  if(str.match(visa) || str.match(mastercard) || str.match(americanExpress) || str.match(dinnersClub) || str.match(jcb)) {
    var element = document.getElementById("error");
    element.classList.remove("error");
    var element1 = document.getElementById("number-card");
    element1.classList.remove("number-false");
    var element2 = document.getElementById("number-card");
    element2.classList.add("number-true");
    flag = true;
  } else {
    var element1 = document.getElementById("error");
    element1.classList.add("error");
    var element3 = document.getElementById("number-card");
    element3.classList.add("number-false");
    var element = document.getElementById("number-card");
    element.classList.remove("number-true");
    flag = false;
  }

  if (str === ""){
    document.getElementById("error").classList.remove('error');
    document.getElementById("number-card").classList.remove('number-true');
    document.getElementById("number-card").classList.add('color-placeholder');
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
    var element1 = document.getElementById("card-cvv");
    element1.classList.remove("card-cvv-color");
    var element2 = document.getElementById("card-cvv");
    element2.classList.add("number-true");
    flag_color = true;
  } else {
    var element = document.getElementById("card-cvv");
    element.classList.remove("number-true");
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

function numberCvvLength(event)
{
  new Cleave('#card-cvv', {
    numeral: true,
    numeralDecimalMark: '',
    delimiter: '',
  });
  var str = document.getElementById("card-cvv").value;
  var strlen = str.length;
  var keyCode = event.keyCode;
  if(keyCode == 8 || keyCode == 46 || keyCode == 37 || keyCode == 39) {
    return true;
  }

  if (((keyCode >= 96 && keyCode <=105 ) || (keyCode >= 48 && keyCode <=57 )) && strlen < 4 && keyCode != 69)
  {
    return true;
  } else {
    return false;
  }
}
