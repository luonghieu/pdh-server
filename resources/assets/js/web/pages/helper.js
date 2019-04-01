export function getFormData(domQuery){
    const out = {};
    const data = $(domQuery).serializeArray();

    for(let i = 0; i < data.length; i++){
        const record = data[i];
        out[record.name] = record.value;
    }
    return out;
}

export function getResponseMessage(data) {
    let message = '';
    if (typeof data === 'object') {
        for (let key in data) {
            if (data.hasOwnProperty(key)) {
                message += data[key] + '</br>';
            }
        }
    } else {
        message = data;
    }

    return message;
}

export function setCookie(cookie_name, value) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + (365 * 25));
    document.cookie = cookie_name + "=" + escape(value) + "; expires=" + exdate.toUTCString() + "; path=/";
}

export function getCookie(cookie_name) {
    if (document.cookie.length > 0) {
        var cookie_start = document.cookie.indexOf(cookie_name + "=");
        if (cookie_start != -1) {
            cookie_start = cookie_start + cookie_name.length + 1;
            var cookie_end = document.cookie.indexOf(";", cookie_start);
            if (cookie_end == -1) {
                cookie_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(cookie_start, cookie_end));
        }
    }

    return "";
}

export function updateLocalStorageValue(key, data) {
    var oldData = JSON.parse(localStorage.getItem(key));
    var newData;

    if (oldData) {
      newData = Object.assign({}, oldData, data);
    } else {
      newData = data;
    }

    localStorage.setItem(key, JSON.stringify(newData));
  }

export function updateLocalStorageKey(key, data, ids) {
    var oldData = JSON.parse(localStorage.getItem(key));
    var newData = {};

    if (oldData) {
      if(oldData[ids]) {
        oldData[ids] = Object.assign({}, oldData[ids], data);
        newData = oldData;
      } else {
        oldData[ids] = data;
        newData = oldData;
      }
    } else {
      newData[ids] = data;
    }

    localStorage.setItem(key, JSON.stringify(newData));
  }

export  function add_minutes(dt, minutes) {
      return new Date(dt.getTime() + minutes*60000);
    }

export function deleteLocalStorageValue(key, delKey) {
    var data = JSON.parse(localStorage.getItem(key));

    if (data) {
      if(data[delKey]) {
        delete data[delKey];
      }
    }

    localStorage.setItem(key, JSON.stringify(data));
  }

export function deleteLocalStorageKey(key, delKey, ids) {
    var data = JSON.parse(localStorage.getItem(key));

    if (data) {
      if(data[ids]) {
        if(data[ids][delKey]){
            delete data[ids][delKey];
        }
      }
    }
    localStorage.setItem(key, JSON.stringify(data));
  }

export function loadShift(show = null)
{
  if($('select[name=sl_month_nomination]').length) {
    if(localStorage.getItem("shifts")){
      var castId = $('.cast-id').val();
      var shift = JSON.parse(localStorage.getItem("shifts"));
      if(shift[castId]) {
        shift = shift[castId];

        var date = parseInt(shift.date);
        var month = parseInt(shift.month);
        var day = shift.dayOfWeekString;

        var htmlMonth = `<option value="${month}" >${month}月</option>`;
        var htmlDate = `<option value="${date}" >${date}日(${day})</option>`;

        $('select[name=sl_month_nomination]').html(htmlMonth);
        $('select[name=sl_date_nomination]').html(htmlDate);

        var currentDate = new Date();
        var utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
        var nd = new Date(utc + (3600000*9));

        var currentDate = parseInt(nd.getDate());
        
        if (date != currentDate) {
          $('.input-time-number').prop('disabled', 'true');
          $('.input-time-number').parent().removeClass('active');
          $('.input-time-number').parent().addClass('inactive');

          if(localStorage.getItem("order_params")){
            var orderParams = JSON.parse(localStorage.getItem("order_params"));

            if(!orderParams.current_minute) {
              $('select[name=sl_hour_nomination]>option:eq(21)').prop('selected', true);
              $('select[name=sl_minute_nomination]>option:eq(0)').prop('selected', true);

              $('#date_input').addClass('active');
              $('.input-other-time').prop('checked', 'true');
              $('.date-input-nomination').css('display', 'flex');

              if(!show) {
                $(".date-input").click();
              }

              var time = $('.input-other-time').val();

              var updateTime = {
                    current_time_set: time,
                  };

              this.updateLocalStorageValue('order_params', updateTime);
            }
          } else {
            $('select[name=sl_hour_nomination]>option:eq(21)').prop('selected', true);
            $('select[name=sl_minute_nomination]>option:eq(0)').prop('selected', true);

            $('#date_input').addClass('active');
            $('.input-other-time').prop('checked', 'true');
            $('.date-input-nomination').css('display', 'flex');
            $(".date-input").click();
            var time = $('.input-other-time').val();

            var updateTime = {
                  current_time_set: time,
                };

            this.updateLocalStorageValue('order_params', updateTime);
          }
        }
      }
    }
  }
}

