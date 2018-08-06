<template>
    <div class="col-md-9 col-sm-10 main ">
        <div class="messaging">
            <div class="inbox_msg">
                <h3 class="text-center nickname"></h3>
                <list-users :users="users" :user_id="user_id"></list-users>
                <div class="mesgs">
                    <chat-messages :list_message="list_messages" :user_id="user_id"
                                   :created_at="created_at"></chat-messages>
                    <div class="type_msg">
                        <div class="input_msg_write">
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
    import ChatMessages from './ChatMessages'
    import ListUsers from './ListUsers'

    export default {
        name: "ChatRoom",
        components: {
            ChatMessages,
            ListUsers
        },

        data() {
            return {
                message: '',
                list_messages: [],
                users: '',
                user_id: '',
                image: '',
                type: 2,
                picture: '',
                none: true,
                file_upload: '',
                errors: [],
                created_at: '',
                timer: ''
            }
        },

        watch: {
            '$route'(to, from) {
                let id = this.$route.params.id;
                this.init(id);
                this.getMessagesInRoom(id);
            }
        },

        created() {
            this.getToken();
            this.getRoom();
            this.timer = setInterval(this.getRoom, 20000)
        },

        methods: {
            init(id) {
                window.Echo.private('room.' + id)
                    .listen('MessageCreated', (e) => {
                        this.list_messages.push(e.message);
                    });
            },

            getToken() {

                const access_token = document.getElementById('token').value;
                this.user_id = document.getElementById('userId').value;
                window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + access_token;
                Echo.connector.options.auth.headers['Authorization'] = 'Bearer ' + access_token;

            },

            getRoom() {
                window.axios.get("../../api/v1/rooms")
                    .then(response => {
                        const rooms = response.data.data.data;
                        this.users = rooms
                        console.log(rooms)
                    });
            },

            getMessagesInRoom(id) {
                window.axios.get("../../api/v1/rooms/" + id)
                    .then(response => {
                        this.list_messages = [];
                        const room = response.data.data.data;
                        room.forEach(messages => {
                            messages.forEach(item => {
                                this.list_messages.push(item);
                            })
                        });
                    });
            },


            sendMessage() {

                this.picture = this.image;
                this.created_at = new Date().toLocaleTimeString().replace(/([\d]+:[\d]{2})(:[\d]{2})(.*)/, "$1$3");
                this.userId = this.user_id
                let data = {
                    message: this.message,
                    type: this.type,
                }

                let config = {
                    header: {
                        'Content-Type': 'multipart/form-data'
                    }
                }

                if (this.file_upload) {
                    data = new FormData();
                    data.append('image', this.file_upload);
                    data.append('type', 3);
                }

                window.axios.post('../../api/v1/rooms/' + this.$route.params.id + '/messages', data, config)
                    .then((response) => {
                        this.list_messages.push(response.data.data);
                        this.message = '';
                        this.removeImage().click();
                    });
            },

            chooseFiles() {
                document.getElementById("fileUpload").click()
            },

            onFileChange(e) {
                const files = e.target.files;
                const {name, size} = files[0];

                if (name.lastIndexOf(".") <= 0) {
                    var message = "Please choose a valid image";
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
                    var message = "Invalid image format.";
                    this.errors.push(message);
                    return false;
                }

                let sizeMB = (size / (1024 * 1024)).toFixed(2);
                if (sizeMB > 5.12) {
                    var message = `(${sizeMB}MB). Image size is too large. Please upload an image size less than 5MB`;
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

                reader.onload = (e) => {
                    vm.image = e.target.result;
                };
                reader.readAsDataURL(file);
            },

            removeImage: function (e) {
                this.image = '';
            },

            // @keyup.enter="sendMessage"

        }
    }
</script>

<style scoped>

</style>