<template>
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <h1 class="mb-3">Отписки грузчиков</h1>
        <form
            ref="formRef"
            :class="{
          'opacity-50':isLoading
        }" @submit.prevent="onSubmit">
          <div class="row g-3">
            <div class="col-md-12">
              <label for="receipt-number" class="form-label">Номер поступления<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="receiptNumber" type="number" inputmode="numeric" class="form-control" autofocus id="receipt-number" name="receipt-number" required>
                <span @click.prevent="receiptNumber = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-12">
              <label for="reason" class="form-label">Причина разворота</label>
              <div class="d-flex">
                <input v-model="reason" type="text" class="form-control" id="reason" name="reason" >
                <span @click.prevent="reason = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="vehicle-number" class="form-label">Номер ТС</label>
              <div class="d-flex">
                <input v-model="vehicleNumber" type="text" class="form-control" id="vehicle-number" name="vehicle-number" >
                <span @click.prevent="vehicleNumber = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="gate-numbers" class="form-label">Номера ворот</label>
              <div class="d-flex">
                <input v-model="gateNumbers" type="text" class="form-control" id="gate-numbers" name="gate-numbers" >
                <span @click.prevent="gateNumbers = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-12">
              <label for="depersonalization-video" class="form-label">Видео</label>
              <div class="d-flex flex-column">
                <!-- Файл-инпут: сразу открывает камеру на планшете + позволяет выбрать несколько -->
                <input
                    ref="videoInput"
                    type="file"
                    accept="video/*"
                    capture="camcorder"
                    multiple
                    @change="onVideosSelected"
                    class="form-control mb-2"
                />

                <!-- Превью загруженных/записанных видео -->
                <div v-if="videoPreviews.length" class="d-flex flex-wrap gap-2">
                  <video
                      v-for="(src, i) in videoPreviews"
                      :key="i"
                      :src="src"
                      controls
                      style="max-width: 150px; max-height: 150px;"
                  ></video>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="row">
                <div class="col-12">
                  <button :disabled="!canSubmit" type="button" @click.prevent="trySubmit" class="btn btn-dark w-100 fw-bold" >Отправить</button>
                </div>
                <div class="col-12 small text-danger" v-if="!canSubmit && !isLoading">
                  Заполните все поля!
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <Confirm
        v-if="successText"
        title="Успешно!"
        :message="successText"
        @accept="() => successText = undefined"
        @cancel="successText = undefined;"
    />

    <Confirm
        v-if="errorText"
        title="Ошибка!"
        :message="errorText"
        @accept="() => errorText = undefined"
        @cancel="errorText = undefined;"
    />

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

const reason = ref<string>();
const receiptNumber = ref<string>();
const vehicleNumber = ref<string>();
const gateNumbers = ref<string>();



const confirmSelect = ref<boolean>(false);

const errorText = ref<string>();
const successText = ref<string>();

const videos = ref<File[]>([]);
const videoPreviews = ref<string[]>([]);
const videoInput = ref<HTMLInputElement|null>(null);

function onVideosSelected(e: Event) {
  const input = e.target as HTMLInputElement;
  if (!input.files) return;

  // очищаем старые превью
  videoPreviews.value.forEach(URL.revokeObjectURL);
  videos.value = Array.from(input.files);

  // создаём новые превью
  videoPreviews.value = videos.value.map(file =>
      URL.createObjectURL(file)
  );
}

const isLoading = ref(false);

const canSubmit = computed(() => {
  return reason.value &&
      videos.value.length > 0 &&
      // receiptNumber.value &&
      // vehicleNumber.value &&
      // gateNumbers.value &&
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

const clearData = () => {
  let val = undefined;
  receiptNumber.value = val;
  vehicleNumber.value = val;
  gateNumbers.value = val;
  reason.value = val;

  if(videoInput.value){
    videoInput.value.value = '';
  }
  videoPreviews.value = [];
  videos.value = [];
}

clearData();

const onSubmit = async () => {
  const formData = new FormData();

  formData.append('reason', reason.value || '');
  formData.append('receiptNumber', receiptNumber.value || '');
  formData.append('vehicleNumber', vehicleNumber.value || '');
  formData.append('gateNumbers', gateNumbers.value || '');

  // добавляем видеофайлы
  videos.value.forEach(file => {
    formData.append('videos[]', file);
  });

  isLoading.value = true;
  try{
    const res = await fetch(import.meta.env.VITE_API_URL + '/api/' + import.meta.env.VITE_ENDPOINT_CODE + '/v1.0/submit/' + import.meta.env.VITE_DIRECTION_CODE, {
      method: 'POST',
      body: formData,
      headers: {
        'Accept': 'application/json',
      }
    });
    isLoading.value = false;
    if(res.status != 200){
      const ddd = await res.json();
      errorText.value = 'Не удалось отправить запрос (' + ddd?.message? ddd.message : res.status + ')';
      return;
    }
    const data = await res.json();
    if(data.success){
      successText.value = 'Успешно отправлено!';
      clearData();
    }
    else{
      errorText.value = 'Не удалось отправить запрос';

    }
  }
  catch (e){
    isLoading.value = false;
    errorText.value = 'Ошибка при отправке запроса, попробуйте еще раз';
  }
}

</script>