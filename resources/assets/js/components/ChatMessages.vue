<template>
    <div class="msg_history">
        <div class="display_message" v-for="(message, index) in list_message">
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
                    <span class="time_date">{{created_at}}</span>
                </div>
            </div>
            <div class="incoming_msg" v-else>
                <div v-for="avatar in message.avatars" class="incoming_msg_img"><img :src="avatar.path"></div>
                <div class="received_msg">
                    <div class="received_withd_msg">
                        <div v-if="message.image">
                            <img width="100" :src="message.image"/>
                        </div>
                        <p v-if="message.message">{{message.message}}</p>
                        <span class="time_date"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ConfirmDelete from './ConfirmDelete'

    export default {
        name: "ChatMessage",
        components: {ConfirmDelete},
        props: ['list_message', 'user_id', 'created_at'],
        data() {
            return {
                confirmModal: false,
                selectedMessage: null,
                listMessage: '',
                message_id: ''
            }
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
                window.axios.delete('../../api/v1/messages/' + this.message_id).then(response => {
                });
                this.selectedMessage = null;
            }
        },

    }
</script>

<style scoped>

</style>