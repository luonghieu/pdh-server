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
                    <router-link :to="{ name: 'ChatRoom', params: { id: value.id }}" v-on:click.native="setRoomId">
                        <div v-bind:class="value.id == roomId || value.id == room_id  ? 'active_chat' : ''">
                            <div class="chat_list" v-for="userDetail in value.users"
                                 v-if="userDetail.id !== user_id  && userDetail.type == cast">

                                <div class="chat_people">
                                    <div class="chat_img" v-if=userDetail.avatars><img
                                            class="img_avatar"
                                            :src="userDetail.avatars[0].path">
                                    </div>
                                    <div class="chat_ib">
                                         <h5 v-bind:class="value.unread_count == 0 || value.id == Id || value.id == room_id ? '' : 'chat_ib_nickname' ">{{userDetail.nickname}}</h5>
                                        <p v-if="value.latest_message">{{value.latest_message.message}}</p>
                                    </div>
                                </div>
                                <span v-bind:class="value.unread_count == 0 || value.id == Id || value.id == room_id ? 'notification' : 'notify-chat'">{{value.unread_count}}</span>
                            </div>
                        </div>
                    </router-link>
            </div>
        </div>
        <div class="inbox_chat inbox_guest" id="guest">
            <div v-for="value in filteredData">
                    <router-link :to="{ name: 'ChatRoom', params: { id: value.id }}" v-on:click.native="setRoomId">
                        <div v-bind:class="value.id == Id || value.id == room_id ? 'active_chat ' : ''">
                            <div class="chat_list" v-for="userDetail in value.users" v-if="userDetail.id !== user_id && userDetail.type == guest">
                                <div class="chat_people">
                                    <div class="chat_img" v-if=userDetail.avatars><img
                                            class="img_avatar"
                                            :src="userDetail.avatars[0].path">
                                    </div>
                                    <div class="chat_ib">
                                        <h5 v-bind:class="value.unread_count == 0 || value.id == Id || value.id == room_id ? '' : 'chat_ib_nickname' ">{{userDetail.nickname}}</h5>
                                        <p v-if="value.latest_message">{{value.latest_message.message}}</p>
                                    </div>
                                </div>
                                <span v-bind:class="value.unread_count == 0 || value.id == Id || value.id == room_id ? 'notification' : 'notify-chat'">{{value.unread_count}}</span>
                            </div>
                        </div>
                    </router-link>
            </div>
        </div>
        <div class="loading_content" v-if="totalUser > 15">
            <button class="loading_button" @click="loadUser(pageCm)">もっと見る</button>
        </div>
    </div>
</template>

<script>
export default {
  name: "ListUsers",
  props: ["users", "user_id", "totalUser", "roomId"],
  data() {
    return {
      cast: 2,
      guest: 1,
      isActive: true,
      searchName: "",
      Id: "",
      pageCm: 1,
      totalItem: 1,
      totalpage: 1,
      room_id: this.roomId
    };
  },
  methods: {
    setRoomId(){
        this.room_id = null
    },

    loadUser(pageCm) {
      window.axios
        .get(`../../api/v1/rooms/?paginate=${15}&page=${pageCm + 1}`)
        .then(response => {
          let listUser = "";
          listUser = response.data.data.data;
          listUser.forEach(item => {
            this.users.push(item);
          });
          this.pageCm = getComment.data.data.current_page;
          this.totalItem = getComment.data.data.total;
          this.totalpage = getComment.data.data.last_page;
        });
    }
  },

  computed: {
    filteredData: function() {
      this.Id = this.$route.params.id;
      var search_array = this.users;
      var searchName = this.searchName;

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
