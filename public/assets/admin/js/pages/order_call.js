let casts = [];
let currentPopup = null;
let type = null;
let listCastMatching = getListCastMatching();
let listCastNominees = getListCastNominees();
let listCastCandidates = getListCastCandidates();
let classId = $('#choosen-cast-class').children("option:selected").val();

function debounce(func, wait, immediate) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};

function getListCastMatching() {
    var arrCastMatching = [];
    $('.cast-matching-id').each(function(index, val) {
        arrCastMatching.push($(val).html());
    });

    return arrCastMatching;
}

function getListCastNominees() {
    var arrCastNominees = [];
    $('.cast-nominee-id').each(function(index, val) {
        arrCastNominees.push($(val).html());
    });

    return arrCastNominees;
}

function getListCastCandidates() {
    var arrCastCandidates = [];
    $('.cast-candidate-id').each(function(index, val) {
        arrCastCandidates.push($(val).html());
    });

    return arrCastCandidates;
}

function checkCastSelected(selectorElement, userId) {
    selectedNomination.forEach(item => {
        if (userId == item.id) {
            setTimeout(() => {
                $(selectorElement + userId).addClass('hidden');
            }, 200);
        }
    });

    selectedCandidate.forEach(item => {
        if (userId == item.id) {
            setTimeout(() => {
                $(selectorElement + userId).addClass('hidden');
            }, 200);
        }
    });

    selectedMatching.forEach(item => {
        if (userId == item.id) {
            setTimeout(() => {
                $(selectorElement + userId).addClass('hidden');
            }, 200);
        }
    });
}

function renderListCast(classId, listCastMatching, listCastNominees, listCastCandidates, search = '') {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "GET",
        url: '/admin/orders/casts/'+ classId,
        data: {
            'listCastMatching': listCastMatching,
            'listCastNominees': listCastNominees,
            'listCastCandidates': listCastCandidates,
            'search': search
        },
        success: function(response) {
            casts = response.casts;
            $('#choose-cast-matching tbody').html(response.view);
            $('#choose-cast-candidate tbody').html(response.view);
            $('#choose-cast-nominee tbody').html(response.view);
        },
    });
}

function orderPoint(cast, isNominee = null) {
    let orderDuration = Number($('#order-duration').val()) * 60;
    const currentCastClass = castClasses.find(i => i.id == $('#choosen-cast-class').val());
    let cost = 0;
    if (isNominee) {
        cost = cast.cost;
    } else {
        cost = currentCastClass.cost;
    }

    return (cost / 2) * Math.floor(orderDuration / 15);
}

function orderFee() {
    const orderDuration = Number($('#order-duration').val()) * 60;
    const multiplier = Math.floor(orderDuration / 15);
    return 500 * multiplier;
}
function allowance() {
    const orderDate = $('#order-date').val();
    const duration = $('#order-duration').val();
    const orderStartDate = moment(orderDate);
    const orderEndDate = moment(orderDate).add(duration, 'hours');

    const allowanceStartTime = moment().set( {hour:0, minute:1, second:0});
    const allowanceEndTime = moment().set( {hour:4, minute:0, second:0});

    console.log(allowanceStartTime.format('YYYY-MM-DD HH:mm'));
    console.log(allowanceEndTime.format('YYYY-MM-DD HH:mm'));
    console.log(orderStartDate.format('YYYY-MM-DD HH:mm'));
    console.log(orderEndDate.format('YYYY-MM-DD HH:mm'));
}
allowance();
function updateTotalPoint() {
    const castsMatching = getListCastMatching();
    const castsNominee = getListCastNominees();
    const castsCandidate = getListCastCandidates();
    let totalPoint = 0;

    castsNominee.forEach(val => {
       const cast = selectedNomination.find(i => i.id == val);
        totalPoint += orderPoint(cast, true) + orderFee();
    });
    castsCandidate.forEach(val => {
        const cast = selectedNomination.find(i => i.id == val);
        totalPoint += orderPoint(cast);
    });

    $('#total-point').text((totalPoint + '').replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + 'P');
}

jQuery(document).ready(function($) {
    // checkbox cast nominee
    $('body').on('click', '#add-cast-nominee', function() {
      var totalCast = $("#total-cast option:selected").val();
      if (totalCast <= numOfCast) {
        alert('invalid');
        return false;
      }
      cast_ids = [];

      $('.verify-checkboxs:checked').each(function() {
        cast_ids.push(this.value);
      });

      $('#cast_ids').val(cast_ids.join(','));

      $('#choose-cast-nominee').modal('hide');

      $.each(cast_ids , function(index, val) {
        const cast = casts.find(i => i.id == val);

        if (cast) {
          selectedNomination.push(cast);
          numOfCast++;

          const element = `<tr>
          <td class="cast-nominee-id">${cast.id}</td>
          <td>${cast.nickname}</td>
          <td><button type="button" class="btn btn-info remove-btn" data-user-id="${cast.id}" data-type="1" 
          >このキャストを削除する
          </button></td>
          </tr>`;
          $('#nomination-selected-table').append(element);
          updateTotalPoint();
        }
      });
    });

    // checkbox cast candidate
    $('body').on('click', '#add-cast-candidate', function() {
      var totalCast = $("#total-cast option:selected").val();
      if (totalCast <= numOfCast) {
        alert('invalid');
        return false;
      }
      cast_ids = [];

      $('.verify-checkboxs:checked').each(function() {
        cast_ids.push(this.value);
      });

      $('#cast_ids').val(cast_ids.join(','));

      $('#choose-cast-candidate').modal('hide');

      $.each(cast_ids , function(index, val) {
        const cast = casts.find(i => i.id == val);

        if (cast) {
          selectedCandidate.push(cast);
          numOfCast++;

          const element = `<tr>
          <td class="cast-candidate-id">${cast.id}</td>
          <td>${cast.nickname}</td>
          <td><button type="button" class="btn btn-info remove-btn" data-type="2" data-user-id="${cast.id}">このキャストを削除する
          </button></td>
          </tr>`;

          $('#candidate-selected-table').append(element);
          updateTotalPoint();
        }
      });
    });

    // checkbox cast matching
    $('body').on('click', '#add-cast-matching', function() {
      var totalCast = $("#total-cast option:selected").val();
      if (totalCast <= numOfCast) {
        alert('invalid');
        return false;
      }
      
      cast_ids = [];

      $('.verify-checkboxs:checked').each(function() {
        cast_ids.push(this.value);
      });

      $('#cast_ids').val(cast_ids.join(','));

      $('#choose-cast-matching').modal('hide');

      $.each(cast_ids , function(index, val) {
        const cast = casts.find(i => i.id == val);

        if (cast) {
          selectedMatching.push(cast);
          numOfCast++;

          const element = `<tr>
          <td class="cast-matching-id">${cast.id}</td>
          <td>${cast.nickname}</td>
          <td><button class="btn btn-info" data-type="3" data-user-id="${cast.id}">このキャストを削除する</button></td>
          </tr>`;

          $('#matching-selected-table').append(element);
        }
      });
    });

    $('body').on('click', '#popup-cast-nominee', function (event) {
        type = 1;
        renderListCast(classId, getListCastMatching(), getListCastNominees(), getListCastCandidates());
        $('#nomination-table > tbody  > tr').each(function(index, val) {
            const dataUserId = $(val).attr('data-user-id');
            checkCastSelected('#nomination-table tr#nomination-', dataUserId);
        });
    });

    $('body').on('click', '#popup-cast-candidate', function (event) {
        type = 2;
        renderListCast(classId, getListCastMatching(), getListCastNominees(), getListCastCandidates());
        $('#candidation-table > tbody  > tr').each(function(index, val) {
            const dataUserId = $(val).attr('data-user-id');
            checkCastSelected('#candidation-table tr#candidate-', dataUserId);
        });
    });

    $('body').on('click', '#popup-cast-matching', function (event) {
        type = 3;
        renderListCast(classId, getListCastMatching(), getListCastNominees(), getListCastCandidates());
        $('#matching-table > tbody  > tr').each(function(index, val) {
            const dataUserId = $(val).attr('data-user-id');
            checkCastSelected('#matching-table tr#matching-', dataUserId);
        });
    });

    $('#choosen-cast-class').change(function(event) {
        classId = $(this).children("option:selected").val();
        renderListCast(classId, getListCastMatching(), getListCastNominees(), getListCastCandidates());
    });

    // Remove user
    $('body').on('click', '.remove-btn', function (event) {
        const ele = $(this);
        const type = ele.attr('data-type');
        const userId = ele.attr('data-user-id');
        if (type == 1) {
            const index = selectedNomination.findIndex(i => i.id == userId);
            selectedNomination.splice(index, 1);
        }

        if (type == 2) {
            const index = selectedCandidate.findIndex(i => i.id == userId);
            selectedCandidate.splice(index, 1);
        }

        if (type == 3) {
            const index = selectedMatching.findIndex(i => i.id == userId);
            selectedMatching.splice(index, 1);
        }
        numOfCast-=1;
        ele.parent().parent().remove();
    });
    $('#order-duration').on('change', function (event) {
        console.log($(this).val());
    });
    renderListCast(classId, getListCastMatching(), getListCastNominees(), getListCastCandidates());

    // Search user in popup
    $('.input-search').keyup(debounce(function(){
        var search = $(this).val();
        renderListCast(1, getListCastMatching(), getListCastNominees(), getListCastCandidates());
    },500));
    $('#orderdatetimepicker').datetimepicker({
      minDate: 'now',
    });
});