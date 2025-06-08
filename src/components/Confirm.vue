<template>
  <div class="confirm-modal-outer">
    <div class="confirm-modal">
      <div class="confirm-modal-body">
        <div class="confirm-modal-title" v-if="title">
          {{ title }}
        </div>
        <div class="confirm-modal-text" v-if="message">
          {{ message }}
        </div>
        <div class="confirm-modal-buttons d-flex flex-nowrap justify-content-end gap-2">
          <button type="button" class="btn btn-secondary" @click="emits('cancel')">Отменить</button>
          <button type="button" class="btn btn-success" @click="emits('accept')">Принять</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>

const emits = defineEmits([
    'accept',
    'cancel'
]);
const props = defineProps<{
  title?:string,
  message?:string,
}>();


</script>

<style scoped>
.confirm-modal-outer{
  position: fixed;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.2);
  z-index: 100;
  left: 0;
  top: 0;
}
.confirm-modal-title{
  font-size: 2rem;
}
.confirm-modal-text{
  margin: 1rem 0;
}
.confirm-modal{
  border-radius: 1rem;
  padding: 2rem 3rem;
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  background-color: #FFF;
}
</style>