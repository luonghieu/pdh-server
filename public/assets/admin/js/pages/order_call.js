jQuery(document).ready(function($) {
    let casts = [];
    let selectedNomination = [];
    let selectedCandidate = [];
    let selectedMatching = [];
    let currentPopup = null;

    function renderListCast(classId, listCastMatching, listCastNominees, listCastCandidates, type, search = '') {
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "GET",
        dataType: "json",
        url: '/admin/orders/casts/'+ classId + '?search=' + search,
        data: {
          'listCastMatching': listCastMatching,
          'listCastNominees': listCastNominees,
          'listCastCandidates': listCastCandidates,
          'type': type
        },
        success: function(response) {
          casts = response.casts;
          $('#choose-cast-matching tbody').html(response.view);
          $('#choose-cast-candidate tbody').html(response.view);
          $('#choose-cast-nominee tbody').html(response.view);
        },
      });
    }

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
          <td><button class="btn btn-info">このキャストを削除する</button></td>
          </tr>`;
        $('#nomination-selected-table').append(element);
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
          <td><button class="btn btn-info">このキャストを削除する</button></td>
          </tr>`;

          $('#candidate-selected-table').append(element);
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
          <td><button class="btn btn-info">このキャストを削除する</button></td>
          </tr>`;

          $('#matching-selected-table').append(element);
        }
      });
    });

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

    $(document).ready(function() {
      var type = null;
      var listCastMatching = getListCastMatching();
      var listCastNominees = getListCastNominees();
      var listCastCandidates = getListCastCandidates();

      var classId = $('#choosen-cast-class').children("option:selected").val();

      $('body').on('click', '#popup-cast-nominee', function (event) {
        type = 1
        renderListCast(classId, listCastMatching, listCastNominees, listCastCandidates, type);  
        $('#nomination-table > tbody  > tr').each(function(index, val) {
          const dataUserId = $(val).attr('data-user-id');
          checkCastSelected('#nomination-table tr#nomination-', dataUserId);
        });
      });

      $('body').on('click', '#popup-cast-candidate', function (event) {
        type = 2
        renderListCast(classId, listCastMatching, listCastNominees, listCastCandidates, type);
        $('#candidation-table > tbody  > tr').each(function(index, val) {
          const dataUserId = $(val).attr('data-user-id');
          checkCastSelected('#candidation-table tr#candidate-', dataUserId);
        });
      });

      $('body').on('click', '#popup-cast-matching', function (event) {
        type = 3
        renderListCast(classId, listCastMatching, listCastNominees, listCastCandidates, type);
        $('#matching-table > tbody  > tr').each(function(index, val) {
          const dataUserId = $(val).attr('data-user-id');
          checkCastSelected('#matching-table tr#matching-', dataUserId);
        });
      });

      $('#choosen-cast-class').change(function(event) {
        classId = $(this).children("option:selected").val();
        renderListCast(classId, listCastMatching, listCastNominees, listCastCandidates, type);
      });

      renderListCast(classId, listCastMatching, listCastNominees, listCastCandidates, type);
    });
  
    $('#orderdatetimepicker').datetimepicker({
      minDate: 'now',
    });
});