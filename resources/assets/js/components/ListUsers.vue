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
            <div v-for="(room, index) in mutableRoomCasts" :key="index">
                    <router-link :to="{ name: 'ChatRoom', params: { id: room.id }}" v-on:click.native="setRoomId(room)">
                            <div class="chat_list">
                                <div class="chat_people">
                                    <div class="chat_img">
                                        <img class="img_avatar" :src="room.thumbnail">
                                    </div>
                                    <div class="chat_ib">
                                        <h5 class="chat_id fa fa-id-badge"> {{room.owner_id}}</h5>
                                        <h5 class="chat_nickname"><i v-bind:class="room.gender == 2 ? 'fa fa-female' : 'fa fa-male' "></i> {{room.nickname}}</h5>
                                    </div>
                                </div>
                            <span v-for="(unread, index) in unreadMessage" :key="index" v-if="unread.id ==
                                room.id && unread.count > 0" v-bind:class="unread.count == 0 || room.id == room_id ||
                                room.id == roomId  ? 'notification' : 'notify-chat'">{{unread.count}}</span>
                            </div>
                    </router-link>
            </div>
        </div>
        <!--<div class="inbox_chat inbox_guest" id="guest">-->
            <!--<div v-for="(value, index) in filteredData" :key="index">-->
                    <!--<router-link :to="{ name: 'ChatRoom', params: { id: value.id }}" v-on:click.native="setRoomId(value.id, value.unread_count, value.users)">-->
                        <!--<div v-bind:class="value.id == Id || value.id == room_id ? 'active_chat ' : ''">-->
                            <!--<div class="chat_list" v-for="(userDetail, index) in value.users" v-if="userDetail.type == guest" :key="index">-->
                                <!--<div class="chat_people">-->
                                    <!--<div class="chat_img" v-if=userDetail.avatars><img-->
                                            <!--class="img_avatar"-->
                                            <!--:src="userDetail.avatars[0] ? userDetail.avatars[0].thumbnail : ''">-->
                                    <!--</div>-->
                                    <!--<div class="chat_ib">-->
                                        <!--<h5 class="chat_id fa fa-id-badge"> {{userDetail.id}}</h5>-->
                                        <!--<h5 class="chat_nickname"><i v-bind:class="userDetail.gender == 2 ? 'fa fa-female' : 'fa fa-male' "></i> {{userDetail.nickname}}</h5>-->
                                    <!--</div>-->
                                <!--</div>-->
                                <!--<span v-for="(unread, index) in unreadMessage" :key="index" v-if="unread.id == value.id && unread.count > 0" v-bind:class="unread.count == 0 || value.id == Id || value.id == room_id  ? 'notification' : 'notify-chat'">{{unread.count}}</span>-->
                            <!--</div>-->
                        <!--</div>-->
                    <!--</router-link>-->
            <!--</div>-->
        <!--</div>-->
        <div class="inbox_chat inbox_guest" id="guest">
            <div v-for="(room, index) in mutableRoomGuests" :key="index">
                <div v-bind:class="(room_id == room.id || room.id == roomId) ? 'active_chat ' : ''">
                    <router-link :to="{ name: 'ChatRoom', params: { id: room.id }}" v-on:click.native="setRoomId(room)">
                        <div class="chat_list">
                            <div class="chat_people">
                                <div class="chat_img">
                                    <img class="img_avatar" :src="room.thumbnail">
                                </div>
                                <div class="chat_ib">
                                    <h5 class="chat_id fa fa-id-badge"> {{room.owner_id}}</h5>
                                    <h5 class="chat_nickname"><i v-bind:class="room.gender == 2 ? 'fa fa-female' :
                                'fa fa-male' "></i> {{room.nickname}}</h5>
                                </div>
                            </div>
                            <span v-for="(unread, index) in unreadMessage" :key="index" v-if="unread.id ==
                            room.id && unread.count > 0" v-bind:class="unread.count == 0 || room.id == room_id ||
                            room.id == roomId  ? 'notification' : 'notify-chat'">{{unread.count}}</span>
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
  props: [
    "user_id",
    "roomId",
    "realtime_message",
    "realtime_roomId",
    "users",
    "unreadMessage",
    "getRoom",
    'roomGuests',
    'roomCasts'
  ],
  data() {
    return {
      cast: 2,
      guest: 1,
      isActive: true,
      searchName: "",
      room_id: this.roomId,
      setUnread: 1,
      count: this.unreadMessage,
      nickName: "",
      unRead: 0,
      mutableRoomGuests: [],
      mutableRoomCasts: [],
      currentTab: 1
    };
  },
  created() {
      this.mutableRoomGuests = this.roomGuests;
      this.mutableRoomCasts = this.roomCasts;
  },
    watch: {
        searchName: function(val, oldVal) {
            let searchName = this.searchName;
            this.mutableRoomGuests = this.roomGuests;
            this.mutableRoomCasts = this.roomCasts;
            searchName = searchName.trim().toLowerCase();
            if (val) {
                if (this.currentTab == 1) {
                    this.mutableRoomGuests = this.mutableRoomGuests.filter(item => {
                        const nickname = item.nickname.trim().toLowerCase();
                        return nickname.indexOf(searchName) > -1 || item.id.toString().indexOf(searchName) > -1;
                    });
                } else {
                    this.mutableRoomCasts = this.mutableRoomCasts.filter(item => {
                        const nickname = item.nickname.trim().toLowerCase();
                        return nickname.indexOf(searchName) > -1 || item.id.toString().indexOf(searchName) > -1;
                    });
                }
            } else {
                this.mutableRoomGuests = this.roomGuests;
                this.mutableRoomCasts = this.roomCasts;
            }
        }
    },
  methods: {
  //   setRoomId(roomID, unReadCount, user) {
  //     this.room_id = null;
  //     this.Id = roomID;
  //     if (unReadCount > 0) {
  //       this.setUnread = 0;
  //     }
  //     user.forEach(item => {
  //       this.nickName = item.nickname;
  //     });
  //
  //     this.unRead = 0;
  //     let index = this.unreadMessage.findIndex(function(object) {
  //       return object.id === roomID;
  //     });
  //     this.$emit("interface", index);
  //
  //     if (this.unRead == 0) {
  //       this.$emit("interface", this.nickName);
  //     }
  //   }
  // },
    setRoomId(room, unReadCount, user) {
        this.room_id = room.id;
        // if (unReadCount > 0) {
        //     this.setUnread = 0;
        // }
        // user.forEach(item => {
        //     this.nickName = item.nickname;
        // });

        // this.unRead = 0;
        // let index = this.unreadMessage.findIndex(function(object) {
        //     return object.id === room;
        // });
        // this.$emit("interface", index);
        //
        // if (this.unRead == 0) {
        //     this.$emit("interface", this.nickName);
        // }
    }
  },
  computed: {
    filteredData: function() {
        console.log('123123');
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
