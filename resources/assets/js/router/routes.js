import Vue from 'vue';
import VueRouter from 'vue-router';
import ChatRoom from '../components/ChatRoom'

Vue.use(VueRouter);
export default new VueRouter({
    mode: 'history',
    linkActiveClass: 'active',
    routes: [
        {
            path: '/admin/chat?room=:id',
            name: 'ChatRoom',
            component: ChatRoom,
            props: true
        },
    ]

})
