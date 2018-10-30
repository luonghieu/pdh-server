$(document).ready(function(){
  var backUrl = $('#back-url').val();
  var referrer =  document.referrer;

  var num = sessionStorage.getItem("number");
  $('#number').val(num);
  var holderName = sessionStorage.getItem("holderName");
  $('#holder-name').val(holderName);
  var type = sessionStorage.getItem("type");

  if(type) {
    $('.account-type label').addClass('hidden-label');
  } else {
    $('.account-type label').removeClass('hidden-label');
  }
  $('#select-account-type').val(type);

  var num = $('#number').val();
  var holderName = $("#holder-name").val();
  var bankName = $('#bank-name').text();
  var typeClass = $(".account-type label").attr("class");
  if(backUrl != referrer) {
    if(bankName && typeClass == 'hidden-label' && num && holderName) {
      $('.btn-submit-bank').addClass('btn-create-bank-info-color');
      $('.btn-edit-bank').addClass('btn-create-bank-info-color');
      $('.btn-edit-bank').addClass('btn-update-bank-info');
    } else {
      $('.btn-submit-bank').removeClass('btn-create-bank-info-color');
      $('.btn-edit-bank').removeClass('btn-create-bank-info-color');
      $('.btn-edit-bank').removeClass('btn-update-bank-info');
    }
  }

  $('#number').keyup(function(event) {
    function valid(str) {
      var count = 0;
      ['(', ')', '.', '+', '-', ',', ';', 'N', '/', '*', '#', ' '].forEach(function (sample) {
          if(str.indexOf(sample) >= 0) {
            count++;
            return count;
          }

      });
      return count;
    }

    var num = $('#number').val();
    if (valid(num) > 0) {
      num = num.slice(0, num.length - 1);
      $('#number').val(num);
    }
    sessionStorage.setItem("number", num);
    var holderName = $("#holder-name").val();
    var bankName = $('#bank-name').text();
    var typeClass = $(".account-type label").attr("class");

    if(bankName && typeClass == 'hidden-label' && num && holderName) {
      $('.btn-submit-bank').addClass('btn-create-bank-info-color');
      $('.btn-edit-bank').addClass('btn-create-bank-info-color');
      $('.btn-edit-bank').addClass('btn-update-bank-info');
    } else {
      $('.btn-submit-bank').removeClass('btn-create-bank-info-color');
      $('.btn-edit-bank').removeClass('btn-create-bank-info-color');
      $('.btn-edit-bank').removeClass('btn-update-bank-info');
    }
  });

  $('#number').keypress(function(event) {
    var num = $('#number').val();

    if(num.length > 6){
      return false;
    }
    return true;
  });

  $('#holder-name').keyup(function(event) {
    var num = $('#number').val();
    var holderName = $("#holder-name").val();
    sessionStorage.setItem("holderName", holderName);
    var bankName = $('#bank-name').text();
    var typeClass = $(".account-type label").attr("class");

    if(bankName && typeClass == 'hidden-label' && num && holderName) {
      $('.btn-submit-bank').addClass('btn-create-bank-info-color');
      $('.btn-edit-bank').addClass('btn-create-bank-info-color');
      $('.btn-edit-bank').addClass('btn-update-bank-info');
    } else {
      $('.btn-submit-bank').removeClass('btn-create-bank-info-color');
      $('.btn-edit-bank').removeClass('btn-create-bank-info-color');
      $('.btn-edit-bank').removeClass('btn-update-bank-info');
    }
  });

  $('#select-account-type').change(function(event) {
    var num = $('#number').val();
    var holderName = $("#holder-name").val();
    var bankName = $('#bank-name').text();
    var typeClass = $(".account-type label").attr("class");
    var type = $(".account-type select").val();
    sessionStorage.setItem("type", type);

    if(bankName && typeClass == 'hidden-label' && num && holderName) {
      $('.btn-submit-bank').addClass('btn-create-bank-info-color');
      $('.btn-edit-bank').addClass('btn-create-bank-info-color');
      $('.btn-edit-bank').addClass('btn-update-bank-info');
    } else {
      $('.btn-submit-bank').removeClass('btn-create-bank-info-color');
      $('.btn-edit-bank').removeClass('btn-create-bank-info-color');
      $('.btn-edit-bank').removeClass('btn-update-bank-info');
    }
  });

  $('#select-account-type').click(function(event) {
    var type = $('#select-account-type').val();
    if (type) {
      $('#select-account-type').val(type);
    } else {
      $('#select-account-type').val(1);
    }
    $('.account-type label').addClass('hidden-label');
  });

  $('.btn-submit-bank').click(function(event) {
    localStorage.removeItem("number");
    localStorage.removeItem("holderName");
    localStorage.removeItem("type");

    var formData = new FormData();
    var bankName = $('#bank-name').text();
    var bankCode = $("#bank-code").val();
    var branchName = $('#branch-name').text();
    var branchCode = $("#branch-code").val();
    var type = $("#select-account-type").val();
    var number = $("#number").val();
    var holderName = $("#holder-name").val();

    formData.append('bank_name', bankName);
    formData.append('bank_code', bankCode);
    formData.append('branch_name', branchName);
    formData.append('branch_code', branchCode);
    formData.append('type', type);
    formData.append('number', number);
    formData.append('holder_name', holderName);

    axios.post(`/api/v1/cast/bank_accounts`, formData)
    .then(function (response) {
      window.location = '/cast_mypage/bank_account';
    })
    .catch(function (error) {
      console.log(error);
    });
  });

  $('body').on('click', '.btn-update-bank-info', function(event) {
    localStorage.removeItem("number");
    localStorage.removeItem("holderName");
    localStorage.removeItem("type");

    var formData = new FormData();
    var bankName = $('#bank-name').text();
    var bankCode = $("#bank-code").val();
    var branchName = $('#branch-name').text();
    var branchCode = $("#branch-code").val();
    var type = $("#select-account-type").val();
    var number = $("#number").val();
    var holderName = $("#holder-name").val();
    var bankAccount = $("#bank-account").val();

    formData.append('bank_name', bankName);
    formData.append('bank_code', bankCode);
    formData.append('branch_name', branchName);
    formData.append('branch_code', branchCode);
    formData.append('type', type);
    formData.append('number', number);
    formData.append('holder_name', holderName);

    axios.post(`/api/v1/cast/bank_accounts/${bankAccount}`, formData)
    .then(function (response) {
      window.location = '/cast_mypage/bank_account';
    })
    .catch(function (error) {
      console.log(error);
    });
  });
});
