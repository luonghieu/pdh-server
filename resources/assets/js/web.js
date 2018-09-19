
require('./bootstrap');

window.Vue = require('vue');

var store = require('./store');

window.axios.interceptors.request.use(
  config => {
    if (!config.headers.Authorization) {
      const token = store.getToken();

      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
    }

    return config;
  },
  error => Promise.reject(error)
);

require('./web/pages/index');
require('./web/pages/login');
