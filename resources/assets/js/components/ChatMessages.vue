<template>
    <div class="msg_history">
        <div v-for="message in list_message">
            {{message.id}}
            <div class="outgoing_msg" v-if="message.user_id == user_id">
                <div class="sent_msg">
                    <div class="delete_message">
                        <div class="on_mess" v-if="message.image">
                            <img width="100" :src="message.image"/>
                        </div>
                        <p class="on_mess" v-if="message.message">{{message.message}}</p>
                        <div class="dell_mess"><a href="#" @click="delMessage" v-model="message.id" id="mess"
                                                  :data-content="message.id"><i class="fa fa-remove"></i></a></div>
                    </div>
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
    export default {
        name: "ChatMessage",
        props: ['list_message', 'user_id', 'created_at'],
        message_id: '',

        methods: {
            delMessage() {
                let meessage = document.getElementById('mess');
                let message_id = meessage.dataset.content;
                window.axios.delete('../../api/v1/messages/' + message_id).then(response => {});
            }
        }
    }
</script>

<style scoped>

</style>