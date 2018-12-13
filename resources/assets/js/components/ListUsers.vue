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
            <div v-for="(room, index) in filteredCasts" :key="index">
                    <router-link :to="{ name: 'ChatRoom', params: { id: room.id }}" v-on:click.native="setRoomId(room)">
                            <div class="chat_list">
                                <div class="chat_people">
                                    <div class="chat_img">
                                        <img class="img_avatar" :src="getImgUrl(room.thumbnail)">
                                    </div>
                                    <div class="chat_ib">
                                        <h5 class="chat_id fa fa-id-badge"> {{room.owner_id}}</h5>
                                        <h5 class="chat_nickname"><i v-bind:class="room.gender == 2 ? 'fa fa-female' : 'fa fa-male' "></i> {{room.nickname}}</h5>
                                    </div>
                                </div>
                            <span v-for="(unread, index) in unreads" :key="index" v-if="unread.id ==
                                room.id && unread.count > 0" v-bind:class="unread.count == 0 || room.id == room_id ||
                                room.id == roomId  ? 'notification' : 'notify-chat'">{{unread.count}}</span>
                            </div>
                    </router-link>
            </div>
        </div>
        <div class="inbox_chat inbox_guest" id="guest">
            <div v-for="(room, index) in filteredGuests" :key="index">
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
                            <span v-for="(unread, index) in unreads" :key="index" v-if="unread.id ==
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
    "unreadMessage",
    "getRoom",
    'roomGuests',
    'roomCasts',
    'storagePath'
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
      currentTab: 1,
      unreads: []
    };
  },
  created() {
      this.mutableRoomGuests = this.roomGuests;
      this.mutableRoomCasts = this.roomCasts;
      this.unreads = this.unreadMessage;
  },
  methods: {
    setRoomId(room) {
        this.room_id = room.id;
        this.$emit('updateUnreadMessage', this.room_id);
    },
    getImgUrl(thumbnail) {
      const pattern = /(http|https):?/;
      if(!pattern.test(thumbnail)) {
        return this.storagePath + thumbnail;
      }

      return thumbnail;
    },
  },
  watch: {
      unreadMessage(newVal, oldVal) {
          this.unreads = newVal;
      }
  },
  computed: {
    filteredGuests() {
      let search_array = this.roomGuests;
      let searchName = this.searchName;

      if (!searchName) {
        return search_array;
      }

      searchName = searchName.trim().toLowerCase();

      search_array = search_array.filter(item => {
          const nickname = item.nickname.trim().toLowerCase();
          return nickname.indexOf(searchName) > -1 || item.id.toString().indexOf(searchName) > -1;
      });

      return search_array;
    },
    filteredCasts () {
      let search_array = this.roomCasts;
      let searchName = this.searchName;
      if (!searchName) {
        return search_array;
      }

      searchName = searchName.trim().toLowerCase();

      search_array = search_array.filter(item => {
        const nickname = item.nickname.trim().toLowerCase();
        return nickname.indexOf(searchName) > -1 || item.id.toString().indexOf(searchName) > -1;
      });

      return search_array;
    }
  }
};
</script>

<style scoped>
</style>
