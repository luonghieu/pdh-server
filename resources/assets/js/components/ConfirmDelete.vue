<template id="modal">
    <div class="modal_del" @click="onClose">
        <div class="modal-window">
            <h4>このメッセージを削除してもよろしいですか</h4>
            <div class="actions">
                <button class="cancel" @click="onCancel">キャンセル</button>
                <button class="confirm" @click="onConfirm">削除</button>
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
    },
    onClose(){
        this.$emit("close")
    }
  }
};
</script>

<style scoped>
</style>
