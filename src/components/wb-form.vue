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
              <label for="barcode" class="form-label">ШК<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="barcode" type="text" class="form-control" autofocus id="barcode" name="barcode" required>
                <span @click.prevent="barcode = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="shortage" class="form-label">Недостача<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="shortage" type="number" inputmode="numeric" class="form-control" id="shortage" name="shortage" >
                <span @click.prevent="shortage = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="surplus" class="form-label">Излишек<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="surplus" type="number" inputmode="numeric" class="form-control" id="surplus" name="surplus" >
                <span @click.prevent="surplus = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="through" class="form-label">через «Да»<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="through" type="number" inputmode="numeric" class="form-control" id="through" name="through" >
                <span @click.prevent="through = ''" class="btn btn-warning">x</span>
              </div>
            </div>

            <div class="col-md-6">
              <label for="depersonalization-barcode" class="form-label">Обезличка ШК<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="depersonalizationBarcode" inputmode="numeric" type="number" class="form-control" id="depersonalization-barcode" name="depersonalization-barcode">
                <span @click.prevent="depersonalizationBarcode = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="worker" class="form-label">ID сотрудника<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="worker" type="number" inputmode="numeric" class="form-control" id="worker" name="worker">
                <span @click.prevent="worker = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="table" class="form-label">№ Стола Приемки<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="table" type="number" inputmode="numeric"  class="form-control" id="table" name="table">
                <span @click.prevent="table = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="reason" class="form-label">Причина обезлички<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="reason" type="text" class="form-control" id="reason" name="reason">
                <span @click.prevent="reason = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="count" class="form-label">Количество<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="count" type="number" inputmode="numeric" class="form-control" id="count" name="count">
                <span @click.prevent="count = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-12">
              <label for="depersonalization-video" class="form-label">Обезличка видео<sup class="text-danger">*</sup></label>
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
                <div class="col-12 small text-danger" v-if="!canSubmit">
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
const barcode = ref<string>();
const shortage = ref<string>();
const surplus = ref<string>();
const through = ref<string>();
const depersonalizationBarcode = ref<string>();
const worker = ref<string>();
const table = ref<string>();
const reason = ref<string>();
const count = ref<string>();
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
  return barcode.value &&
      shortage.value &&
      surplus.value &&
      through.value &&
      depersonalizationBarcode.value &&
      videos.value.length > 0 &&
      //worker.value &&
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

const clearData = () => {
  let val = undefined;
  barcode.value = val;
  shortage.value = val;
  surplus.value = val;
  through.value = val;
  depersonalizationBarcode.value = val;
  worker.value = val;
  table.value = val;
  reason.value = val;
  count.value = val;

  if(videoInput.value){
    videoInput.value.value = '';
  }
  videoPreviews.value = [];
  videos.value = [];
}

clearData();

const onSubmit = async () => {
  const formData = new FormData();

  formData.append('barcode', barcode.value || '');
  formData.append('shortage', shortage.value || '');
  formData.append('surplus', surplus.value || '');
  formData.append('through', through.value || '');
  formData.append('depersonalization_barcode', depersonalizationBarcode.value || '');
  formData.append('worker', worker.value || '');
  formData.append('table', table.value || '');
  formData.append('reason', reason.value || '');
  formData.append('count', count.value || '');

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