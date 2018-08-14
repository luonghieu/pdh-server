<template>
    <div class="col-md-9 col-sm-10 main ">
        <div class="messaging">
            <div class="inbox_msg">
                <h3 class="text-center nickname"></h3>
                <list-users :users="users" :user_id="user_id" :totalUser="totalUser" :roomId="roomId" :realtime_message="realtime_message" :realtime_roomId="realtime_roomId" :realtime_count="realtime_count"
                @interface="handleCountMessage"
                ></list-users>
                <div class="mesgs">
                    <chat-messages :list_message="list_messages" :user_id="user_id"
                                   :totalMessage="totalMessage" :roomId="roomId" :realtime_roomId="realtime_roomId"></chat-messages>
                    <div class="type_msg">
                        <div class="input_msg_write">
                            <a name="bottom"></a>
                            <div v-if="!image">
                                <textarea name="mess" v-model="message" class="write_msg"
                                          placeholder="Type a message*"></textarea>
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
      users: "",
      user_id: "",
      image: "",
      type: 2,
      none: true,
      file_upload: "",
      errors: [],
      timer: "",
      totalMessage: "",
      totalUser: "",
      roomId: "",
      id: "",
      realtime_message: "",
      realtime_roomId: "",
      realtime_count: 0
    };
  },

  watch: {
    $route(to, from) {
      this.id = this.$route.params.id;
      if(this.id){
          this.roomId = null
      }
      this.init(this.id);
      this.getMessagesInRoom(this.id);
    }
  },

  created() {
    this.getToken();
    this.getRoom();
    const url = window.location.href;
    const newUrl = new URL(url);
    this.roomId = newUrl.searchParams.get("room");
    if (this.roomId) {
      this.init(this.roomId);
      this.getMessagesInRoom(this.roomId);
    }
  },

  methods: {
    init(id) {
      window.Echo.leave("room." + id);
      window.Echo.private("room." + id).listen("MessageCreated", e => {
        this.realtime_message = e.message.message
        this.realtime_roomId = e.message.room_id
        this.realtime_count +=1
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

    getRoom() {
      window.axios.get("../../api/v1/rooms").then(response => {
        this.totalUser = response.data.data.total;
        const rooms = response.data.data.data;
        this.users = rooms;
      });
    },

    getMessagesInRoom(id) {
      window.axios.get("../../api/v1/rooms/" + id).then(response => {
        this.list_messages = [];
        const room = response.data.data.data;
        this.totalMessage = response.data.data.total;
        room.forEach(messages => {
          messages.forEach(item => {
            this.list_messages.unshift(item);
          });
        });
      });
    },

    sendMessage() {
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

      if (name.lastIndexOf(".") <= 0) {
        var message = "有効な画像を選択してください";
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
        var message = "画像形式は無効です";
        this.errors.push(message);
        return false;
      }

      let sizeMB = (size / (1024 * 1024)).toFixed(2);
      if (sizeMB > 5.12) {
        var message = `(${sizeMB}MB). 画像サイズが大きすぎます 5MB以下の画像をアップロードしてください`;
        this.errors.push(message);
        return false;
      }

      this.file_upload = e.target.files[0];
      this.createImage(files[0]);
    },

    createImage(file) {
      var image = new Image();
      var reader = new FileReader();
      var vm = this;

      reader.onload = e => {
        vm.image = e.target.result;
      };
      reader.readAsDataURL(file);
    },

    removeImage: function(e) {
      this.image = "";
    },

    handleCountMessage(event){
    this.realtime_count = event
    },

    // @keyup.enter="sendMessage"
  }
};
</script>

<style scoped>
</style>
