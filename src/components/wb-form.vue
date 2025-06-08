<template>
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <h1 class="mb-3">Отписки сотрудников</h1>
        <form
            ref="formRef"
            :class="{
          'opacity-50':isLoading
        }" @submit.prevent="onSubmit">
          <div class="row g-3">
            <div class="col-md-12">
              <label for="barcode" class="form-label">ШК</label>
              <div class="d-flex">
                <input v-model="barcode" type="text" class="form-control" autofocus id="barcode" name="barcode" required>
                <span @click.prevent="barcode = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="shortage" class="form-label">Недостача</label>
              <div class="d-flex">
                <input v-model="shortage" type="text" inputmode="numeric" class="form-control" id="shortage" name="shortage" >
                <span @click.prevent="shortage = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="surplus" class="form-label">Излишек</label>
              <div class="d-flex">
                <input v-model="surplus" type="text" inputmode="numeric" class="form-control" id="surplus" name="surplus" >
                <span @click.prevent="surplus = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="through" class="form-label">через «Да»</label>
              <div class="d-flex">
                <input v-model="through" type="text" inputmode="numeric" class="form-control" id="through" name="through" >
                <span @click.prevent="through = ''" class="btn btn-warning">x</span>
              </div>
            </div>

            <div class="col-md-6">
              <label for="depersonalization-barcode" class="form-label">Обезличка ШК</label>
              <div class="d-flex">
                <input v-model="depersonalizationBarcode" inputmode="numeric" type="text" class="form-control" id="depersonalization-barcode" name="depersonalization-barcode">
                <span @click.prevent="depersonalizationBarcode = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="worker" inputmode="numeric" class="form-label">ID сотрудника</label>
              <div class="d-flex">
                <input v-model="worker" type="number" class="form-control" id="worker" name="worker">
                <span @click.prevent="worker = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="table" inputmode="numeric" class="form-label">№ Стола Приемки</label>
              <div class="d-flex">
                <input v-model="table" type="text" class="form-control" id="table" name="table">
                <span @click.prevent="table = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="reason" class="form-label">Причина обезлички</label>
              <div class="d-flex">
                <input v-model="reason" type="number" class="form-control" id="reason" name="reason">
                <span @click.prevent="reason = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="count" inputmode="numeric" class="form-label">Количество</label>
              <div class="d-flex">
                <input v-model="count" type="text" class="form-control" id="count" name="count">
                <span @click.prevent="count = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-12">
              <label for="depersonalization-video" class="form-label">Обезличка видео</label>
              <div class="d-flex">
                <input v-model="depersonalizationVideo" type="text" class="form-control" id="depersonalization-video" name="depersonalization-video">
              </div>
            </div>

            <div class="col-12">
              <div class="row">
                <div class="col-12">
                  <button :disabled="!canSubmit" type="button" @click.prevent="trySubmit" class="btn btn-dark w-100 fw-bold" >Отправить</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <Confirm
        v-if="confirmSelect"
        title="Подтверждение отправки"
        message="Вы действительно хотите отправить форму?"
        @accept="() => makeSubmit()"
        @cancel="confirmSelect = false;"
    />
  </div>
</template>
<script lang="ts" setup>
import {computed, ref} from "vue";
import Confirm from "@/components/Confirm.vue";

const formRef = ref<HTMLFormElement>();
const barcode = ref<string>();
const shortage = ref<string>();
const surplus = ref<string>();
const through = ref<string>();
const depersonalizationBarcode = ref<string>();
const depersonalizationVideo = ref<string>();
const worker = ref<string>();
const table = ref<string>();
const reason = ref<string>();
const count = ref<string>();
const confirmSelect = ref<boolean>(false);

const isLoading = ref(false);

const canSubmit = computed(() => {
  return barcode.value &&
      shortage.value &&
      surplus.value &&
      through.value &&
      depersonalizationBarcode.value &&
      depersonalizationVideo.value &&
      worker.value &&
      table.value &&
      reason.value &&
      count.value &&
      !isLoading.value
});

const trySubmit = () => {
  confirmSelect.value = true;
}

const makeSubmit = () => {
  if(formRef.value){
    onSubmit();
  }
  confirmSelect.value = false;
}

const onSubmit = async () => {
  const formData = new FormData();

  isLoading.value = true;
  try{
    const res = await fetch('/add-result.php', {
      method: 'POST',
      body: formData
    });
    isLoading.value = false;
    if(res.status != 200){
      alert('Не удалось отправить запрос (' + res.status + ')');
      return;
    }
    const data = await res.json();
    if(data.success){
      alert('Успешно отправлено!')

    }
    else{
      alert('Не удалось отправить запрос');
    }
  }
  catch (e){
    isLoading.value = false;
    alert('Ошибка при отправке запроса, попробуйте еще раз')
  }
}

</script>