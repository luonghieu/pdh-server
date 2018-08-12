<template>
    <div class="msg_history">
        <div v-if="(realtime_roomId == room_id || realtime_roomId == Id) && pageCm > 1" @click="hiddenNewMessage" v-bind:class="isHidden == true ? 'unread_count' : ''">
        <div aria-hidden="true" class="_4wzq _4wzr">
        <a class="_5f0v _4wzs" tabindex="0">
            <i class="_4wzt img sp_UbXt4jt-HZi_2x sx_4d6394 fa fa-arrow-down" alt=""></i>
            <div class="_1bqr">新しいメッセージ</div>
        </a>
        </div>
        </div>
        <div v-if="totalMessage > 15" class="loading_message">
            <button class="loading_button" @click="loadMessage(pageCm)">もっと見る</button>
        </div>
        <transition-group>
            <div v-for="(message, index) in list_message" :key="`keyIndex-${index}`">
                <div class="outgoing_msg" v-if="message.user_id == user_id">
                    <div class="sent_msg">
                        <div class="delete_message">
                            <button class="dell_mess" @click="confirmDelete(index, list_message, message.id)"><i
                                    class="fa fa-trash"></i></button>
                            <div class="on_mess" v-if="message.image">
                                <img width="100" :src="message.image"/>
                            </div>
                            <p class="on_mess" v-if="message.message" :key='message.id'>{{message.message}}</p>
                        </div>
                        <confirm-delete :delete="selectedMessage" v-if='confirmModal' @confirm="deleteMessage"
                                        @cancel="cancelDelete" @close="closePopup"></confirm-delete>
                        <span class="time_date" v-if="message.created_at">{{message.created_at}}</span>
                    </div>
                </div>
                <div v-else>
                    <div class="incoming_msg" v-if="message.room_id == room_id || message.room_id == Id">
                    <div>
                    <div class="received_msg">
                        <div v-if="message.user.avatars" class="incoming_msg_img"><img class="img_avatar"
                                :src="message.user.avatars[0].path"></div>
                        <div class="received_withd_msg">
                            <div v-if="message.image">
                                <img width="100" :src="message.image"/>
                            </div>
                            <p v-if="message.message">{{message.message}}</p>
                            <span class="time_date" v-if="message.created_at">{{message.created_at}}</span>
                        </div>
                    </div>
                    </div>
                    </div>
                </div>
            </div>
        </transition-group>
    </div>
</template>

<script>
import ConfirmDelete from "./ConfirmDelete";

export default {
  name: "ChatMessage",
  components: { ConfirmDelete },
  props: ["list_message", "user_id", "totalMessage", "roomId", "realtime_roomId"],
  data() {
    return {
      confirmModal: false,
      selectedMessage: null,
      isScroll: true,
      listMessage: "",
      message_id: "",
      pageCm: 1,
      totalItem: 1,
      totalpage: 1,
      room_id: "",
      Id: this.roomId,
      isHidden: false
    };
  },

  updated() {
    this.room_id = this.$route.params.id;

    if (this.room_id) {
      this.Id = null;
    }

    if (this.isScroll) {
      this.scrollToEnd();
    }
  },

  methods: {
    confirmDelete(index, list_message, id) {
      this.selectedMessage = index;
      this.listMessage = list_message;
      this.message_id = id;
      this.confirmModal = true;
      this.isScroll = false;
    },

    cancelDelete() {
      this.confirmModal = false;
      this.selectedMessage = null;
      this.isScroll = false;
    },

    closePopup() {
      this.confirmModal = false;
      this.selectedMessage = null;
      this.isScroll = false;
    },

    deleteMessage() {
      this.confirmModal = false;
      this.$delete(this.listMessage, this.selectedMessage);
      window.axios
        .delete("../../api/v1/messages/" + this.message_id)
        .then(response => {});
      this.selectedMessage = null;
      this.isScroll = false;
    },

    loadMessage(pageCm) {
      let Id;
      if (this.roomId) {
        Id = this.roomId;
      } else {
        Id = this.$route.params.id;
      }
      this.isScroll = false;
      window.axios
        .get(`../../api/v1/rooms/${Id}?paginate=${15}&page=${pageCm + 1}`)
        .then(getMessage => {
          let temp = "";
          temp = getMessage.data.data.data;
          temp.forEach(messages => {
            messages.forEach(item => {
              this.list_message.unshift(item);
            });
          });
          this.pageCm = getMessage.data.data.current_page;
          this.totalItem = getMessage.data.data.total;
          this.totalpage = getMessage.data.data.last_page;
          this.$nextTick(() => {
            this.$refs.toolbarChat.scrollTop = 0;
          });
        });
    },

    hiddenNewMessage(){
        this.scrollToEnd();
        this.isHidden = true
    },

    scrollToEnd: function() {
      this.$nextTick(() => {
        const scroll = $(".msg_history")[0].scrollHeight;
        $(".msg_history").animate({ scrollTop: scroll });
      });
    }
  }
};
</script>

<style scoped>
</style>
