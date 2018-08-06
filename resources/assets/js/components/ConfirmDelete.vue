<template id="modal">
    <div class="modal_del">
        <div class="modal-window">
            <h4>Are you sure want to delete?</h4>
            <div class="actions">
                <button class="cancel" @click="onCancel">Cancel</button>
                <button class="confirm" @click="onConfirm">Delete</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ConfirmDelete",
        template: "#modal",
        props: ["delete"],
        methods: {
            onConfirm() {
                this.$emit("confirm");
            },
            onCancel() {
                this.$emit("cancel");
            }
        }
    }
</script>

<style scoped>

</style>