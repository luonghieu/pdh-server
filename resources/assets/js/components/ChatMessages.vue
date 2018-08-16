<template>
    <div class="msg_history">
        <div v-if="(realtime_roomId == room_id || realtime_roomId == Id) && pageCm > 1" @click="hiddenNewMessage" v-bind:class="isHidden == true ? 'unread_count' : ''">
            <div aria-hidden="true" class="new_mesage in_new">
                <a class="button_new" tabindex="0">
                    <i class="in_mess img in_message arrow_down fa fa-arrow-down" alt=""></i>
                    <div class="text_newmess">新しいメッセージ</div>
                </a>
            </div>
        </div>
        <div v-if="totalMessage > 15" class="loading_message" v-bind:class="totalpage == pageCm ? 'hidden_loadmess' : ''">
            <button class="loading_button" @click="loadMessage(pageCm)">もっと見る</button>
        </div>
        <transition-group>
            <div v-for="(message, index) in list_message" :key="`keyIndex-${index}`">
                <div class="outgoing_msg" v-if="message.user_id == user_id">
                    <div class="sent_msg">
                        <div class="in_sendmess">
                        <div class="delete_message">
                            <!-- <button class="dell_mess" @click="confirmDelete(index, list_message, message.id)"><i
                                    class="fa fa-trash"></i></button> -->
                            <div class="on_mess" v-if="message.image">
                                <img width="100" :src="message.image"/>
                            </div>
                            <p :id="message.room_id" class="on_mess" ref="linkOut" v-if="message.message" :key='message.id'>{{message.message}}</p>
                        </div>
                        <div style="clear:both"></div>
                        <!-- <confirm-delete :delete="selectedMessage" v-if='confirmModal' @confirm="deleteMessage"
                                        @cancel="cancelDelete" @close="closePopup"></confirm-delete> -->
                        <span class="time_date" v-if="message.created_at">{{message.created_at.substr(6,10)}}</span>
                        </div>
                    </div>
                </div>
        <div v-else>
            <div class="timeLine__unreadLine" v-if="message.setRead == true && (message.room_id == room_id || message.room_id == Id)" v-bind:class="isUnread == true ? '' : 'unread_count'">
                    <div class="timeLine__unreadLineBorder">
                        <div class="timeLine__unreadLineContainer">
                            <div class="timeLine__unreadLineBody">
                                <span class="timeLine__unreadLineText">未読メッセージ</span>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="incoming_msg" v-if="message.message && (message.room_id == room_id || message.room_id == Id)">
                <div class="received_msg">
                        <div v-if="message.user.avatars" class="incoming_msg_img"><img class="img_avatar"
                                :src="message.user.avatars[0].path"></div>
                        <div class="received_withd_msg">
                            <div v-if="message.image">
                                <img width="100" :src="message.image"/>
                            </div>
                            <p :id="message.room_id" ref="linkIncom" v-if="message.message">{{message.message}}</p>
                            <span class="time_incom" v-if="message.created_at">{{message.created_at.substr(6,10)}}</span>
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
  props: [
    "list_message",
    "user_id",
    "totalMessage",
    "roomId",
    "realtime_roomId",
    "countUnread_realtime",
  ],
  data() {
    return {
      confirmModal: false,
      selectedMessage: null,
      isScroll: true,
      listMessage: "",
      message_id: "",
      pageCm: 1,
      totalItem: 1,
      totalpage: 0,
      room_id: "",
      Id: this.roomId,
      isHidden: false,
      linkOut: "",
      linkInMessage: "",
      realtime_id: 0,
      index: 0,
      messageUnread_id: "",
      isUnread: true,
      isCount: [],
    };
  },

  updated() {
    this.room_id = this.$route.params.id;

    if (this.room_id) {
      this.Id = null;
    }
    if (this.realtime_roomId) {
      if (
        this.realtime_roomId == this.room_id ||
        (this.realtime_roomId == this.Id && this.pageCm < 2)
      ) {
        this.isScroll = true;
      } else {
        this.isScroll = false;
      }
    }
    if (this.isScroll) {
      this.scrollToEnd();
    }

   if(this.realtime_roomId == this.room_id || this.realtime_roomId == this.Id ){
       setTimeout(this.setTimeOut, 5000);
   } else {
       this.isUnread = true
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

      this.$emit("interface", this.realtime_id);

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
        });
    },

    hiddenNewMessage() {
      this.scrollToEnd();
      this.isHidden = true;
    },

    setTimeOut(){
        this.isUnread = false;
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
