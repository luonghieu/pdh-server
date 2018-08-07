<template>
    <div class="msg_history">
        <div v-if="totalMessage > 15" style="text-align: center">
            <button class="loading_button" @click="loadMessage(pageCm)">Load More</button>
        </div>
        <transition-group>
            <div v-for="(message, index) in list_message" :key="`keyIndex-${index}`">
                <div class="outgoing_msg" v-if="message.user_id == user_id">
                    <div class="sent_msg">
                        <div class="delete_message">
                            <div class="on_mess" v-if="message.image">
                                <img width="100" :src="message.image"/>
                            </div>
                            <p class="on_mess" v-if="message.message" :key='message.id'>{{message.message}}</p>
                            <button class="dell_mess" @click="confirmDelete(index, list_message, message.id)"><i
                                    class="fa fa-remove"></i></button>
                        </div>
                        <confirm-delete :delete="selectedMessage" v-if='confirmModal' @confirm='deleteMessage'
                                        @cancel="cancelDelete"></confirm-delete>
                        <span class="time_date" v-if="message.created_at">{{message.created_at}}</span>
                    </div>
                </div>
                <div class="incoming_msg" v-else>
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
        </transition-group>
    </div>
</template>

<script>
import ConfirmDelete from "./ConfirmDelete";

export default {
  name: "ChatMessage",
  components: { ConfirmDelete },
  props: ["list_message", "user_id", "totalMessage"],
  data() {
    return {
      confirmModal: false,
      selectedMessage: null,
      listMessage: "",
      message_id: "",
      pageCm: 1,
      totalItem: 1,
      totalpage: 1,
      index: 0
    };
  },

  updated() {
    this.scrollToEnd(this.index);
  },

  methods: {
    confirmDelete(index, list_message, id) {
      this.selectedMessage = index;
      this.listMessage = list_message;
      this.message_id = id;
      this.confirmModal = true;
    },

    cancelDelete() {
      this.confirmModal = false;
      this.selectedMessage = null;
    },

    deleteMessage() {
      this.confirmModal = false;
      this.$delete(this.listMessage, this.selectedMessage);
      window.axios
        .delete("../../api/v1/messages/" + this.message_id)
        .then(response => {});
      this.selectedMessage = null;
    },

    loadMessage(pageCm) {
      window.axios
        .get(
          `../../api/v1/rooms/${
            this.$route.params.id
          }?paginate=${15}&page=${pageCm + 1}`
        )
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
          this.index = null
        });
    },

    scrollToEnd: function(index) {
      this.index = 0;
      this.$nextTick(() => {
        const scroll = $(".msg_history")[index].scrollHeight;
        $(".msg_history").animate({ scrollTop: scroll });
      });
    }
  }
};
</script>

<style scoped>
</style>
