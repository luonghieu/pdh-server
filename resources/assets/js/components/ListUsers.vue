<template>
    <div class="inbox_people">
        <div class="panel-body handling">
            <input type="text" class="form-control input_search" placeholder="ユーザーID,名前" v-model="searchName">
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
                    <router-link :to="{ name: 'ChatRoom', params: { id: value.id }}" v-on:click.native="setRoomId(value.id, value.unread_count, value.users)">
                        <div v-bind:class="value.id == Id || value.id == room_id  ? 'active_chat' : ''">
                            <div class="chat_list" v-for="userDetail in value.users"
                                 v-if="userDetail.type == cast">

                                <div class="chat_people">
                                    <div class="chat_img" v-if=userDetail.avatars><img
                                            class="img_avatar"
                                            :src="userDetail.avatars[0].thumbnail">
                                    </div>
                                    <div class="chat_ib">
                                        <h5 class="chat_id fa fa-id-badge"> {{userDetail.id}}</h5>
                                        <h5 class="chat_nickname" v-bind:class="value.unread_count == 0 || value.id == Id || value.id == room_id  || setUnread == 0 || unread_realtime > 0 ? '' : 'chat_ib_nickname' "><i v-bind:class="userDetail.gender == 2 ? 'fa fa-female' : 'fa fa-male' "></i> {{userDetail.nickname}}</h5>
                                    </div>
                                </div>
                                <span v-bind:class="value.unread_count == 0 || value.id == Id || value.id == room_id || setUnread == 0 ? 'notification' : 'notify-chat'">{{value.unread_count}}</span>
                            </div>
                        </div>
                    </router-link>
            </div>
        </div>
        <div class="inbox_chat inbox_guest" id="guest">
            <div v-for="value in filteredData">
                    <router-link :to="{ name: 'ChatRoom', params: { id: value.id }}" v-on:click.native="setRoomId(value.id, value.unread_count, value.users)">
                        <div v-bind:class="value.id == Id || value.id == room_id ? 'active_chat ' : ''">
                            <div class="chat_list" v-for="userDetail in value.users" v-if="userDetail.type == guest">
                                <div class="chat_people">
                                    <div class="chat_img" v-if=userDetail.avatars><img
                                            class="img_avatar"
                                            :src="userDetail.avatars[0].thumbnail">
                                    </div>
                                    <div class="chat_ib">
                                        <h5 class="chat_id fa fa-id-badge"> {{userDetail.id}}</h5>
                                        <h5 class="chat_nickname" v-bind:class="value.unread_count == 0 || value.id == Id || value.id == room_id || setUnread == 0 || unread_realtime > 0 ? '' : 'chat_ib_nickname' "><i v-bind:class="userDetail.gender == 2 ? 'fa fa-female' : 'fa fa-male' "></i> {{userDetail.nickname}}</h5>
                                    </div>
                                </div>
                                <span v-bind:class="value.unread_count == 0 || value.id == Id || value.id == room_id || setUnread == 0 ? 'notification' : 'notify-chat'">{{value.unread_count}}</span>
                            </div>
                        </div>
                    </router-link>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: "ListUsers",
  props: [
    "user_id",
    "roomId",
    "realtime_message",
    "realtime_roomId",
    "realtime_count",
    "users",
  ],
  data() {
    return {
      cast: 2,
      guest: 1,
      isActive: true,
      searchName: "",
      Id: "",
      room_id: this.roomId,
      setUnread: 1,
      count: this.realtime_count,
      nickName: "",
      unread_realtime: 0,
      unRead: "",
    };
  },

  methods: {
    setRoomId(roomID, unReadCount, user) {
      this.room_id = null;
      this.Id = roomID;
      if (unReadCount > 0) {
        this.setUnread = 0;
      }
     user.forEach(item => {
         this.nickName = item.nickname
     });
    this.$emit('interface', this.nickName);
    }
  },

  computed: {
    filteredData: function() {
      this.unread_realtime = this.realtime_count;
      let search_array = this.users;
      let searchName = this.searchName;

      if (!searchName) {
        return search_array;
      }

      searchName = searchName.trim().toLowerCase();

      search_array = search_array.filter(item => {
        for (let value in item.users) {
          let userId = item.users[value].id.toString();
          if (
            item.users[value].nickname.toLowerCase().indexOf(searchName) !==
              -1 ||
            userId.toLowerCase().indexOf(searchName) !== -1
          ) {
            return true;
          }
        }
      });
      return search_array;
    }
  }
};
</script>

<style scoped>
</style>
