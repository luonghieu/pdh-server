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
            <div v-for="(message, index) in getListMessage" :key="`keyIndex-${index}`">
                <div v-if="message.isHeader == true" class="timeLine__dateHead">
                  <div class="timeLine__dateHeadContainer">
                    <div class="timeLine__dateHeadBody">
                      <span class="icoFontClock"></span>
                      <span class="timeLine__dateHeadText fa fa-clock-o"> {{message.date_data.substr(0,10)}}</span>
                    </div>
                  </div>
                </div>
                <div class="outgoing_msg" v-if="message.user_id == user_id">
                    <div class="sent_msg">
                        <div class="in_sendmess">
                        <div class="delete_message">
                            <!-- <button class="dell_mess" @click="confirmDelete(index, list_message, message.id)"><i
                                    class="fa fa-trash"></i></button> -->
                            <div class="on_mess" v-if="message.image">
                                <img width="100" :src="message.image"/>
                            </div>
                            <p :id="message.room_id" class="on_mess" ref="linkOut" v-if="message.message" :key='message.id' v-html="isLinkMessage(message.message)"></p>
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
            <div class="incoming_msg" v-if="(message.message || message.user.avatars) && (message.room_id == room_id || message.room_id == Id)">
                <div class="received_msg">
                        <div v-if="message.user.avatars" class="incoming_msg_img"><img class="img_received"
                                :src="message.user.avatars[0].thumbnail"></div>
                        <div class="received_withd_msg">
                            <div v-if="message.image">
                                <img width="100" :src="message.image"/>
                            </div>
                            <p :id="message.room_id" ref="linkIncom" v-if="message.message" v-html="isLinkMessage(message.message)"></p>
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
    "unreadMessage"
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
      Id: Number(this.roomId),
      isHidden: false,
      linkInMessage: "",
      realtime_id: 0,
      index: 0,
      messageUnread_id: "",
      isUnread: true,
      isCount: [],
      list_messageData: [],
      getListMessage: []
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
      }
    }

    if (this.isScroll) {
      this.scrollToEnd();
    }

    if (
      this.realtime_roomId == this.room_id ||
      this.realtime_roomId == this.Id
    ) {
      setTimeout(this.setTimeOut, 5000);
    } else {
      this.isUnread = true;
    }
    if (this.pageCm == this.totalpage) {
      this.pageCm = 1;
    }

    this.getListMessage = this.list_message;
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
        .delete("/api/v1/messages/" + this.message_id)
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
      this.$emit("interface", this.realtime_id);

      window.axios
        .get(`/api/v1/rooms/${Id}?paginate=${15}&page=${pageCm + 1}`)
        .then(getMessage => {
          const room = getMessage.data.data.data;
          let currentDate = new Date(
            this.getListMessage[this.getListMessage.length - 1].created_at
          );
          let date_data = this.getListMessage[this.getListMessage.length - 1]
            .created_at;
          let isHeader = { isHeader: true, date_data, user: { avatars: null } };
          room.forEach(messages => {
            let i = 0;
            for (i; i < messages.length; i++) {
              let newDate = new Date(messages[i].created_at);
              if (this.isFormatDate(currentDate, newDate)) {
                this.list_messageData.unshift(messages[i]);
              } else {
                currentDate = new Date(messages[i].created_at);
                date_data = messages[i].created_at;
                isHeader = {
                  isHeader: true,
                  date_data,
                  user: { avatars: null }
                };
                this.list_messageData.unshift(messages[i]);
                this.list_messageData.unshift(isHeader);
              }
            }
            this.list_messageData.unshift(isHeader);
            messages.forEach(items => {
              this.getListMessage.unshift(items);
            });
          });
          this.pageCm = getMessage.data.data.current_page;
          this.totalItem = getMessage.data.data.total;
          this.totalpage = getMessage.data.data.last_page;
        });
    },

    isFormatDate(date1, date2) {
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

    hiddenNewMessage() {
      this.scrollToEnd();
      this.isHidden = true;
    },

    setTimeOut() {
      this.isUnread = false;
    },

    scrollToEnd: function() {
      this.$nextTick(() => {
        const scroll = $(".msg_history")[0].scrollHeight;
        $(".msg_history").animate({ scrollTop: scroll });
      });
    },

    isLinkMessage(isLinkMessage) {
      let data = isLinkMessage.match(/\bhttps?:\/\/\S+/gi);
      if (data) {
        return "<a  href=" + data[0] + "> " + data[0] + "</a>";
      }

      return isLinkMessage.replace(/\n/g, "<br />");
    }
  }
};
</script>

<style scoped>
</style>
