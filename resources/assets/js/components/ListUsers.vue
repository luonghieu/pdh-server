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
                        <div class="chat_list" v-for="userDetail in value.users"
                             v-if="userDetail.id !== user_id  && userDetail.type === cast">

                            <div class="chat_people">
                                <div class="chat_img" v-if="userDetail.avatars"><img class="img_avatar"
                                                                                     :src="userDetail.avatars[0]">
                                </div>
                                <span v-bind:class="userDetail.is_online === 1 ? 'is_online' : 'is_offline' "></span>
                                <div class="chat_ib">
                                    <h5>{{userDetail.nickname}}</h5>
                                    <p v-if="value.latest_message.message">{{value.latest_message.message}}</p>
                                </div>
                            </div>
                            <span v-if="value.unread_count >= 1" class="notify-chat">{{value.unread_count}}</span>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
        <div class="inbox_chat inbox_guest" id="guest">
            <div v-for="value in filteredData">
                <div v-bind:class="value.unread_count >= 1 ? 'active_chat' : '' ">
                    <router-link :to="{ name: 'ChatRoom', params: { id: value.id }}">
                        <div class="chat_list" v-for="userDetail in value.users"
                             v-if="userDetail.id !== user_id && userDetail.type === guest">
                            <div class="chat_people">
                                <div class="chat_img" v-if="userDetail.avatars"><img class="img_avatar"
                                                                                     :src="userDetail.avatars[0]">
                                </div>
                                <span v-bind:class="userDetail.is_online === 1 ? 'is_online' : 'is_offline' "></span>
                                <div class="chat_ib">
                                    <h5>{{userDetail.nickname}}</h5>
                                    <p v-if="value.latest_message.message">{{value.latest_message.message}}</p>
                                </div>
                            </div>
                            <span v-if="value.unread_count >= 1" class="notify-chat">{{value.unread_count}}</span>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ListUsers",
        props: ['users', 'user_id'],
        data() {
            return {
                cast: 2,
                guest: 1,
                isActive: true,
                searchName: '',
            }
        },
        methods: {},

        computed: {
            filteredData: function () {
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