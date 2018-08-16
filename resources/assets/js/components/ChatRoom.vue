<template>
    <div class="col-md-9 col-sm-10 main ">
        <div class="messaging">
            <div class="inbox_msg">
                <h3 class="text-center nickname"></h3>
                <list-users :user_id="user_id" :roomId="roomId" :realtime_message="realtime_message" :realtime_roomId="realtime_roomId" :realtime_count="realtime_count"
                @interface="handleCountMessage" :users="users"
                ></list-users>
                <div class="mesgs">
                    <chat-messages :list_message="list_messages" :user_id="user_id"
                                   :totalMessage="totalMessage" :roomId="roomId" :realtime_roomId="realtime_roomId" @interface="handleNewMessage" :countUnread_realtime="countUnread_realtime"></chat-messages>
                    <div class="type_msg">
                        <div class="input_msg_write">
                            <a name="bottom"></a>
                            <div v-if="!image">
                                <textarea name="mess" v-model="message" class="write_msg"
                                          placeholder="メッセージを入力してください*"></textarea>
                                <input id="fileUpload" name="image" type="file" accept="image/*" style="display: none"
                                       @change="onFileChange">
                                <p style="color: red" v-for="error in errors">{{error}}</p>
                            </div>
                            <div v-else>
                                <img width="100" :src="image"/>
                                <button @click="removeImage"><i class="fa fa-remove" aria-hidden="true"></i></button>
                            </div>
                            <button @click="chooseFiles" class="file_send_btn" type="button"><i class="fa fa-paperclip"
                                                                                                aria-hidden="true"></i>
                            </button>
                            <button @click="sendMessage" class="msg_send_btn" type="submit"><i
                                    class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ChatMessages from "./ChatMessages";
import ListUsers from "./ListUsers";

export default {
  name: "ChatRoom",
  components: {
    ChatMessages,
    ListUsers
  },

  data() {
    return {
      message: "",
      list_messages: [],
      user_id: "",
      image: "",
      type: 2,
      none: true,
      file_upload: "",
      errors: [],
      timer: "",
      totalMessage: "",
      roomId: 0,
      id: "",
      realtime_message: "",
      realtime_roomId: "",
      realtime_count: 0,
      users: "",
      messageUnread_index: "",
      countUnread_realtime: 0,
    };
  },

  watch: {
    $route(to, from) {
      this.id = this.$route.params.id;
      if (this.id) {
        this.roomId = null;
      }
      this.getMessagesInRoom(this.id);
    }
  },

  created() {
    this.getToken();
    this.getRoom();
    this.init();
    const url = window.location.href;
    const newUrl = new URL(url);
    this.roomId = newUrl.searchParams.get("room");
    if (this.roomId) {
      this.getMessagesInRoom(this.roomId);
    }
  },

  methods: {
    init() {
      window.Echo.leave("user." + 1);
      window.Echo.private("user." + 1).listen("MessageCreated", e => {
        this.realtime_message = e.message.message;
        this.realtime_roomId = e.message.room_id;
        if(this.realtime_roomId == Number(this.roomId) || this.realtime_roomId == Number(this.id)) {
            this.realtime_count = 0;
        } else {
            this.realtime_count += 1;
        }
        this.list_messages.push(e.message);
      });
    },

    getToken() {
      const access_token = document.getElementById("token").value;
      this.user_id = document.getElementById("userId").value;
      window.axios.defaults.headers.common["Authorization"] =
        "Bearer " + access_token;
      Echo.connector.options.auth.headers["Authorization"] =
        "Bearer " + access_token;
    },

    getMessagesInRoom(id) {
      window.axios.get("../../api/v1/rooms/" + id).then(response => {
        this.list_messages = [];
        const room = response.data.data.data;
        this.totalMessage = response.data.data.total;
        let setUnRead = {setRead: true, user: {avatars: null}}
        room.forEach(messages => {
        if(this.messageUnread_index || this.countUnread_realtime) {
            messages.splice(this.messageUnread_index || this.countUnread_realtime, 0, setUnRead);
          }
            messages.forEach(item => {
            this.list_messages.unshift(item);
            let messUnread_realtime = this.countUnread_realtime = null;
            let mees_index = this.messageUnread_index = null;
          });
        });
      });
    },

    getRoom() {
      window.axios
        .get("../../api/v1/rooms/admin/get_users")
        .then(response => {
          const rooms = response.data.data;
          this.users = rooms;
          this.users.forEach(items => {
          if(items.unread_count > 0){
            this.messageUnread_index = items.unread_count;
        }
     })
    });
    },

    sendMessage() {
      if(this.id){
          this.realtime_roomId = this.id
      }
      else{
          this.realtime_roomId = this.roomId
      }

      this.realtime_message = this.message;
      this.userId = this.user_id;
      let data = {
        message: this.message,
        type: this.type
      };

      let config = {
        header: {
          "Content-Type": "multipart/form-data"
        }
      };

      if (this.image) {
        data = new FormData();
        data.append("image", this.file_upload);
        data.append("type", 3);
      }

      let Id;
      if (this.roomId) {
        Id = this.roomId;
      } else {
        Id = this.$route.params.id;
      }

      window.axios
        .post("../../api/v1/rooms/" + Id + "/messages", data, config)
        .then(response => {
          this.list_messages.push(response.data.data);
          this.message = "";
          if (this.image) {
            this.removeImage().click();
          }
        });

      const scroll = $(".msg_history")[0].scrollHeight;
      $(".msg_history").animate({ scrollTop: scroll });
    },

    chooseFiles() {
      document.getElementById("fileUpload").click();
    },

    onFileChange(e) {
      const files = e.target.files;
      const { name, size } = files[0];
      let message;
      if (name.lastIndexOf(".") <= 0) {
         message = "有効な画像を選択してください";
        this.errors.push(message);
        return false;
      }

      let ext = name.substring(name.lastIndexOf(".") + 1);
      if (
        ext !== "gif" &&
        ext !== "GIF" &&
        ext !== "jpeg" &&
        ext !== "JPEG" &&
        ext !== "jpg" &&
        ext !== "JPG" &&
        ext !== "png" &&
        ext !== "PNG"
      ) {
        message  = "画像形式は無効です";
        this.errors.push(message);
        return false;
      }

      let sizeMB = (size / (1024 * 1024)).toFixed(2);
      if (sizeMB > 5.12) {
        message  = `(${sizeMB}MB). 画像サイズが大きすぎます 5MB以下の画像をアップロードしてください`;
        this.errors.push(message);
        return false;
      }

      this.file_upload = e.target.files[0];
      this.createImage(files[0]);
    },

    createImage(file) {
      let image = new Image();
      let reader = new FileReader();
      let vm = this;

      reader.onload = e => {
        vm.image = e.target.result;
      };
      reader.readAsDataURL(file);
    },

    removeImage: function(e) {
      this.image = "";
    },

    handleCountMessage(event) {
        this.countUnread_realtime = event;
        if(this.event){
            this.realtime_count = 0;
        }
    },

    handleNewMessage(event) {
      this.realtime_roomId = event;
    }
  }
};
</script>

<style scoped>
</style>
