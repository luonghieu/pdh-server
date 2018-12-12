<template>
    <div class="col-md-9 col-sm-10 main ">
        <div class="messaging">
            <div class="inbox_msg">
                <h3 class="text-center nickname">{{nickName}}</h3>
                <list-users :user_id="user_id" :roomId="roomId" :realtime_message="realtime_message" :realtime_roomId="realtime_roomId"
                :unreadMessage="unreadMessage" :room-guests="roomGuests" :room-casts="roomCasts" @updateUnreadMessage="onRoomJoined"
                ></list-users>
                <div class="mesgs">
                    <chat-messages :list_message="list_messages" :user_id="user_id" :unreadMessage="unreadMessage"
                                   :totalMessage="totalMessage" :roomId="roomId" :realtime_roomId="realtime_roomId" @interface="handleNewMessage"></chat-messages>
                    <div class="type_msg">
                        <div class="input_msg_write">
                            <a name="bottom"></a>
                            <div v-if="!image">
                                <textarea name="mess" v-model="message" class="write_msg"
                                          placeholder="メッセージを入力してください*"
                                          @keydown.enter.exact.prevent
                                          @keyup.enter.exact="sendMessage"
                                          @keydown.enter.shift.exact="newline"
                                          ></textarea>
                                <input id="fileUpload" name="image" type="file" accept="image/*" style="display: none"
                                       @change="onFileChange">
                                <p style="color: red" v-for="(error, index) in errors" :key="index">{{error}}</p>
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
  props: ['rooms', 'unReads', 'roomUsers', 'avatars'],
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
      users: [],
      messageUnread_index: "",
      list_messageData: [],
      nickName: "",
      userId: "",
      unreadMessage: [],
      roomGuests: [],
      roomCasts: []
    };
  },

  watch: {
    $route() {
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
    this.roomId = Number(newUrl.searchParams.get("room"));
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
        if (this.realtime_roomId) {
            if (this.realtime_roomId != this.$route.params.id) {
                if (e.message.user.type == 1) {
                    const roomIndex = this.roomGuests.findIndex(i => i.id == this.realtime_roomId);
                    const room = this.roomGuests[roomIndex];
                    this.roomGuests.splice(roomIndex, 1);
                    this.roomGuests.unshift(room);
                } else {
                    const roomIndex = this.roomCasts.findIndex(i => i.id == this.realtime_roomId);
                    const room = this.roomCasts[roomIndex];
                    this.roomCasts.splice(roomIndex, 1);
                    this.roomCasts.unshift(room);
                }

                const index = this.unreadMessage.findIndex(i => i.id == this.realtime_roomId);
                if (index != -1) {
                    this.unreadMessage[index].count += 1;
                    this.messageUnread_index = this.unreadMessage[index].count;
                } else {
                    this.messageUnread_index = 1;
                    this.unreadMessage.push({ id: this.realtime_roomId, count: 1 });
                }
            }
        }
        this.list_messages.push(e.message);
      });
    },
    newline() {
        this.message = `${this.message}\n`;
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
      window.axios.get("/api/v1/rooms/" + id).then(response => {
        this.list_messageData = [];
        this.list_messages = [];
        const room = response.data.data.data;
        this.totalMessage = response.data.data.total;
        let setUnRead = { setRead: true, user: { avatars: null } };
        room.forEach(messages => {
          let currentDate = new Date(messages[0].created_at);
          let date_data = messages[0].created_at;
          let isHeader = { isHeader: true, date_data, user: { avatars: null } };
          let i = 0;
          for (i; i < messages.length; i++) {
            let newDate = new Date(messages[i].created_at);
            if (this.isSameDay(currentDate, newDate)) {
              this.list_messageData.unshift(messages[i]);
            } else {
              currentDate = new Date(messages[i].created_at);
              date_data = messages[i].created_at;
              isHeader = { isHeader: true, date_data, user: { avatars: null } };
              this.list_messageData.unshift(messages[i]);
              this.list_messageData.unshift(isHeader);
            }
          }
          if (this.messageUnread_index) {
            this.list_messageData.splice(
              this.list_messageData.length - this.messageUnread_index,
              0,
              setUnRead
            );
          }
          this.list_messageData.unshift(isHeader);
          this.list_messages = this.list_messageData;
          let mees_index = (this.messageUnread_index = null);
        });
      });
    },

    isSameDay(date1, date2) {
      let formatDate1 =
        date1.getMonth() +
        1 +
        "/" +
        date1.getDate() +
        "/" +
        date1.getFullYear();
      let formatDate2 =
        date2.getMonth() +
        1 +
        "/" +
        date2.getDate() +
        "/" +
        date2.getFullYear();

      return formatDate1 == formatDate2;
    },

    getRoom() {
      this.unreadMessage = [];
      const rooms = JSON.parse(this.rooms);
      const cloneRooms = rooms;
      let unreads = JSON.parse(this.unReads);
      for (let i of unreads) {
          for (let j = 0; j < cloneRooms.length; j++) {
              const room = cloneRooms[j];
              if (i.room_id == room.id) {
                this.messageUnread_index = i.total;
                this.unreadMessage.push({ id: room.id, count: i.total });
                const tempRoom = cloneRooms[j];
                  rooms.splice(j, 1);
                  rooms.unshift(tempRoom);
                  break;
              }
            }
        }

        this.roomGuests = rooms.filter(r => r.user_type == 1);
        this.roomCasts = rooms.filter(r => r.user_type == 2);
    },
    sendMessage() {
      if (this.id) {
        this.realtime_roomId = this.id;
      } else {
        this.realtime_roomId = this.roomId;
      }

      this.realtime_message = this.message;
      this.userId = this.user_id;
      let data = {
        message: this.message,
        type: this.type,
        is_manual: true
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
        data.append("is_manual", true);
      }

      let Id;
      if (this.roomId) {
        Id = this.roomId;
      } else {
        Id = this.$route.params.id;
      }

      window.axios
        .post("/api/v1/rooms/" + Id + "/messages", data, config)
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
        message = "画像形式は無効です";
        this.errors.push(message);
        return false;
      }

      let sizeMB = (size / (1024 * 1024)).toFixed(2);
      if (sizeMB > 5.12) {
        message = `(${sizeMB}MB). 画像サイズが大きすぎます 5MB以下の画像をアップロードしてください`;
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
    handleNewMessage(event) {
      this.realtime_roomId = event;
    },
    onRoomJoined(event) {
      this.roomId = event;
      const index = this.unreadMessage.findIndex(i => i.id == event);
      if (index != -1) {
          this.unreadMessage.splice(index , 1);
      }
    }
  }
};
</script>

<style scoped>
</style>
