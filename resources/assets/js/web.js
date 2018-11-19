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
  host: window.App.api_url,
  transports: ['websocket']
});

window.Echo.connector.options.auth.headers["Authorization"] = "Bearer " + accessToken;

window.axios.defaults.baseURL = window.App.api_url;

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
require('./web/pages/avatar');
require('./web/pages/update_profile');
require('./web/pages/order_call');
require('./web/pages/list_order');
require('./web/pages/point');
require('./web/pages/chat');
require('./web/pages/room');
require('./web/pages/rating');
require('./web/pages/receipt');
require('./web/pages/card');
require('./web/pages/payment');
require('./web/pages/upload_avatar');
require('./web/pages/order_nomination');
require('./web/pages/cast_mypage');
require('./web/pages/cast_detail');
require('./web/pages/bank_account');
require('./web/pages/create_room');
require('./web/pages/verify');
