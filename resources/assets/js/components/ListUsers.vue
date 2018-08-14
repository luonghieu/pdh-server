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
                    <router-link :to="{ name: 'ChatRoom', params: { id: value.id }}" v-on:click.native="setRoomId(value.id, value.unread_count)">
                        <div v-bind:class="value.id == Id || value.id == room_id  ? 'active_chat' : ''">
                            <div class="chat_list" v-for="userDetail in value.users"
                                 v-if="userDetail.type == cast">

                                <div class="chat_people">
                                    <div class="chat_img" v-if=userDetail.avatars><img
                                            class="img_avatar"
                                            :src="userDetail.avatars[0].path">
                                    </div>
                                    <div class="chat_ib">
                                        <h5 v-if="realtime_roomId == value.id && realtime_count > 0" v-bind:class="realtime_roomId == Id || realtime_roomId == room_id  ? '' : 'chat_ib_nickname' ">{{userDetail.nickname}}</h5>
                                         <h5 v-else v-bind:class="value.unread_count == 0 || value.id == Id || value.id == room_id  || setUnread == 0 ? '' : 'chat_ib_nickname' ">{{userDetail.nickname}}</h5>
                                        <p v-if="realtime_roomId == value.id ">{{realtime_message}}</p>
                                        <p v-else>{{value.latest_message.message}}</p>
                                    </div>
                                </div>
                                <span v-if="realtime_roomId == value.id && realtime_count > 0" v-bind:class="realtime_roomId == Id || realtime_roomId == room_id ? 'notification' : 'notify-chat'">{{realtime_count}}</span>
                                <span v-else v-bind:class="value.unread_count == 0 || value.id == Id || value.id == room_id || setUnread == 0 ? 'notification' : 'notify-chat'">{{value.unread_count}}</span>
                            </div>
                        </div>
                    </router-link>
            </div>
        </div>
        <div class="inbox_chat inbox_guest" id="guest">
            <div v-for="value in filteredData">
                    <router-link :to="{ name: 'ChatRoom', params: { id: value.id }}" v-on:click.native="setRoomId(value.id, value.unread_count)">
                        <div v-bind:class="value.id == Id || value.id == room_id ? 'active_chat ' : ''">
                            <div class="chat_list" v-for="userDetail in value.users" v-if="userDetail.type == guest">
                                <div class="chat_people">
                                    <div class="chat_img" v-if=userDetail.avatars><img
                                            class="img_avatar"
                                            :src="userDetail.avatars[0].path">
                                    </div>
                                    <div class="chat_ib">
                                        <h5 v-if="realtime_roomId == value.id && realtime_count > 0" v-bind:class="realtime_roomId == Id || realtime_roomId == room_id ? '' : 'chat_ib_nickname' ">{{userDetail.nickname}}</h5>
                                        <h5 v-else v-bind:class="value.unread_count == 0 || value.id == Id || value.id == room_id || setUnread == 0 ? '' : 'chat_ib_nickname' ">{{userDetail.nickname}}</h5>
                                        <p v-if="realtime_roomId == value.id ">{{realtime_message}}</p>
                                        <p v-else>{{value.latest_message.message}}</p>
                                    </div>
                                </div>
                                <span v-if="realtime_roomId == value.id && realtime_count > 0 " v-bind:class="realtime_roomId == Id || realtime_roomId == room_id ? 'notification' : 'notify-chat'">{{realtime_count}}</span>
                                <span v-else v-bind:class="value.unread_count == 0 || value.id == Id || value.id == room_id || setUnread == 0 ? 'notification' : 'notify-chat'">{{value.unread_count}}</span>
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
      count: 0,
      users: "",
      nickname: null
    };
  },

  created() {
    this.getRoom();
  },

  methods: {
    setRoomId(roomID, unReadCount) {
      this.room_id = null;
      this.Id = roomID;
      if (unReadCount > 0) {
        this.setUnread = 0;
      }
      if (this.realtime_roomId == roomID) {
        this.$emit("interface", this.count);
      }
    },

    getRoom() {
      window.axios
        .get("../../api/v1/rooms/admin/casts_guests")
        .then(response => {
          const rooms = response.data.data;
          this.users = rooms;
        });
    }
  },

  computed: {
    filteredData: function() {
      let search_array = this.users;
      let searchName = this.searchName;

      if (!searchName) {
        return search_array;
      }

      searchName = searchName.trim().toLowerCase();

      search_array = search_array.filter(item => {
        for (let value in item.users) {
          if (
            item.users[value].nickname.toLowerCase().indexOf(searchName) !== -1
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
