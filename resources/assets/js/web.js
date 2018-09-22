var store = require('./store');
window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

const accessToken = store.getToken();

if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}


import Echo from 'laravel-echo'

window.io = require('socket.io-client');

window.Echo = new Echo({
  broadcaster: 'socket.io',
  host: window.location.hostname,
  transports: ['websocket']
});

window.Echo.connector.options.auth.headers["Authorization"] = "Bearer " + accessToken;

window.axios.interceptors.request.use(
  config => {
    if (!config.headers.Authorization) {
      if (accessToken) {
        config.headers.Authorization = `Bearer ${accessToken}`;
      }
    }

    return config;
  },
  error => Promise.reject(error)
);

require('./web/pages/index');
require('./web/pages/login');
require('./web/pages/update_avatar');
require('./web/pages/update_profile');
require('./web/pages/list_order');
require('./web/pages/point');
require('./web/pages/chat');
require('./web/pages/rating');
