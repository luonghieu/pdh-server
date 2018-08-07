<template>
    <div class="inbox_people">
        <div class="panel-body handling">
            <input type="text" class="form-control input_search" placeholder="名前" v-model="searchName">
            <i class="fa fa-search search_name" aria-hidden="true"></i>
        </div>
        <div class="chat_tab">
            <ul class="nav nav-tabs">
                <li class="active guests"><a data-toggle="tab" href="#guest">ゲスト</a></li>
                <li class="casts"><a data-toggle="tab" href="#cast">キャスト</a></li>
            </ul>
        </div>
        <div class="inbox_chat inbox_cast" id="cast">
            <div v-for="value in filteredData">
                <div v-bind:class="value.unread_count >= 1 ? 'active_chat' : '' ">
                    <router-link :to="{ name: 'ChatRoom', params: { id: value.id }}">
                        <div v-bind:class="value.id == roomId ? 'active_link' : ''">
                            <div class="chat_list" v-for="userDetail in value.users"
                                 v-if="userDetail.id !== user_id  && userDetail.type === cast">

                                <div class="chat_people">
                                    <div class="chat_img" v-if=userDetail.avatars><img
                                            class="img_avatar"
                                            :src="userDetail.avatars[0].path">
                                    </div>
                                    <span v-bind:class="userDetail.is_online === 1 ? 'is_online' : 'is_offline' "></span>
                                    <div class="chat_ib">
                                        <h5>{{userDetail.nickname}}</h5>
                                        <p v-if="value.latest_message">{{value.latest_message.message}}</p>
                                    </div>
                                </div>
                                <span v-if="value.unread_count >= 1" class="notify-chat">{{value.unread_count}}</span>
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
        <div class="inbox_chat inbox_guest" id="guest">
            <div v-for="value in filteredData">
                <div v-bind:class="value.unread_count >= 1 ? 'active_chat' : '' ">
                    <router-link :to="{ name: 'ChatRoom', params: { id: value.id }}">
                        <div v-bind:class="value.id == roomId ? 'active_link' : ''">
                            <div class="chat_list" v-for="userDetail in value.users" v-if="userDetail.id !== user_id">
                                <div class="chat_people">
                                    <div class="chat_img" v-if=userDetail.avatars><img
                                            class="img_avatar"
                                            :src="userDetail.avatars[0].path">
                                    </div>
                                    <span v-bind:class="userDetail.is_online === 1 ? 'is_online' : 'is_offline' "></span>
                                    <div class="chat_ib">
                                        <h5>{{userDetail.nickname}}</h5>
                                        <p v-if="value.latest_message">{{value.latest_message.message}}</p>
                                    </div>
                                </div>
                                <span v-if="value.unread_count >= 1" class="notify-chat">{{value.unread_count}}</span>
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
        <div class="loading_content" v-if="totalUser > 15">
            <button class="loading_button" @click="loadUser(pageCm)">Load More</button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ListUsers",
        props: ['users', 'user_id', 'totalUser'],
        data() {
            return {
                cast: 2,
                guest: 1,
                isActive: true,
                searchName: '',
                roomId: '',
                pageCm: 1,
                totalItem: 1,
                totalpage: 1,
            }
        },
        methods: {
            loadUser(pageCm) {
                window.axios
                    .get(`../../api/v1/rooms/?paginate=${15}&page=${pageCm + 1}`)
                    .then(response => {
                        let listUser = '';
                        listUser = response.data.data.data;
                        listUser.forEach(item => {
                            this.users.push(item);
                        });
                        this.pageCm = getComment.data.data.current_page;
                        this.totalItem = getComment.data.data.total;
                        this.totalpage = getComment.data.data.last_page;
                    })

            },

        },

        computed: {
            filteredData: function () {
                this.roomId = this.$route.params.id;
                var search_array = this.users;
                var searchName = this.searchName;

                if (!searchName) {
                    return search_array;
                }

                searchName = searchName.trim().toLowerCase();

                search_array = search_array.filter(item => {
                    for (let value in item.users) {
                        if (item.users[value].nickname.toLowerCase().indexOf(searchName) !== -1) {
                            return true;
                        }
                    }
                })
                return search_array;
            }
        }
    }
</script>

<style scoped>

</style>
