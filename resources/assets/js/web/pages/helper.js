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


