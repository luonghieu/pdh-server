
require('./bootstrap');

window.Vue = require('vue');

import ChatRoom from "./components/ChatRoom";
Vue.component("ChatRoom", ChatRoom);
import router from './router/routes';

const app = new Vue({
    el: '#chatroom',
    router
});

