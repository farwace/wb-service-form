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
            <div class="col-md-6">
              <label for="worker" class="form-label">ID сотрудника<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="worker" type="number" inputmode="numeric" class="form-control" autofocus id="worker" name="worker" required>
                <span @click.prevent="worker = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="name" class="form-label">Имя<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="name" type="text" class="form-control" id="name" name="name" required>
                <span @click.prevent="name = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-12">
              <label for="square" class="form-label">Квадрат сотрудника<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="square" type="text" class="form-control" id="square" name="square" required>
                <span @click.prevent="square = ''" class="btn btn-warning">x</span>
              </div>
            </div>

            <!-- Ошибка: радиокнопки -->
            <div class="col-md-12">
              <label class="form-label d-block">Ошибка<sup class="text-danger">*</sup></label>
              <div class="d-flex flex-wrap gap-3">
                <div class="form-check" v-for="opt in errorOptions" :key="opt.value">
                  <input class="form-check-input" type="radio" name="error_type" :id="'err-' + opt.value"
                         :value="opt.value" v-model="errorType" @change="onTypeChange">
                  <label class="form-check-label" :for="'err-' + opt.value">{{ opt.label }}</label>
                </div>
              </div>
            </div>

            <!-- Другая причина (текст) — по умолчанию скрыто -->
            <div class="col-md-12" v-show="errorType === 'other'">
              <label for="error_note" class="form-label">Другая причина (текст)<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="errorNote" type="text" class="form-control" id="error_note" name="error_note">
                <span @click.prevent="errorNote = ''" class="btn btn-warning">x</span>
              </div>
            </div>

            <!-- Блок «Нет ШК»: фото + соседний ШК + БК, по умолчанию скрыто -->
            <template v-if="errorType === 'no_barcode'">
              <div class="col-md-12">
                <label for="no-barcode-photo" class="form-label">Фото (Нет ШК)<sup class="text-danger">*</sup></label>
                <div class="d-flex flex-column">
                  <input
                      ref="photoInput"
                      type="file"
                      accept="image/*"
                      capture="environment"
                      id="no-barcode-photo"
                      @change="onPhotoSelected"
                      class="form-control mb-2"
                  />
                  <div v-if="photoPreview" class="d-flex flex-wrap gap-2">
                    <img :src="photoPreview" style="max-width: 180px; max-height: 180px; border-radius:6px;" alt="фото">
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <label for="neighbor_barcode" class="form-label">Соседний ШК с этого поступления<sup class="text-danger">*</sup></label>
                <div class="d-flex">
                  <input v-model="neighborBarcode" type="text" class="form-control" id="neighbor_barcode" name="neighbor_barcode">
                  <span @click.prevent="neighborBarcode = ''" class="btn btn-warning">x</span>
                </div>
              </div>
              <div class="col-md-12">
                <label for="bk" class="form-label">БК<sup class="text-danger">*</sup></label>
                <div class="d-flex">
                  <input v-model="bk" type="text" class="form-control" id="bk" name="bk">
                  <span @click.prevent="bk = ''" class="btn btn-warning">x</span>
                </div>
              </div>
            </template>

            <div class="col-12">
              <div class="row">
                <div class="col-12">
                  <button :disabled="!canSubmit" type="button" @click.prevent="trySubmit" class="btn btn-dark w-100 fw-bold">Отправить</button>
                </div>
                <div class="col-12 small text-danger" v-if="!canSubmit && !isLoading && errorType">
                  Заполните все обязательные поля!
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
        v-if="warningText"
        title="Внимание!"
        :message="warningText"
        :show-cancel="false"
        @accept="() => warningText = undefined"
        @cancel="warningText = undefined;"
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

const worker = ref<string>();
const name = ref<string>();
const square = ref<string>();
const errorType = ref<string>('');
const errorNote = ref<string>();
const neighborBarcode = ref<string>();
const bk = ref<string>();

const photo = ref<File | null>(null);
const photoPreview = ref<string>('');
const photoInput = ref<HTMLInputElement | null>(null);

const confirmSelect = ref<boolean>(false);
const errorText = ref<string>();
const successText = ref<string>();
const warningText = ref<string>();

const errorOptions = [
  {value: 'not_in_order', label: 'Товар не в заказе'},
  {value: 'office_error', label: 'Ошибка офиса'},
  {value: 'no_barcode', label: 'Нет ШК'},
  {value: 'other', label: 'Другая причина'},
];

function onPhotoSelected(e: Event) {
  const input = e.target as HTMLInputElement;
  if (!input.files || !input.files.length) return;
  if (photoPreview.value) URL.revokeObjectURL(photoPreview.value);
  photo.value = input.files[0];
  photoPreview.value = URL.createObjectURL(photo.value);
}

const isLoading = ref(false);

const canSubmit = computed(() => {
  if (!worker.value || !name.value || !square.value || !errorType.value || isLoading.value) {
    return false;
  }
  if (errorType.value === 'other') {
    return !!errorNote.value;
  }
  if (errorType.value === 'no_barcode') {
    return !!photo.value && !!neighborBarcode.value && !!bk.value;
  }
  return true;
});

// Сброс условных полей при смене типа ошибки
const onTypeChange = () => {
  if (errorType.value !== 'no_barcode') {
    clearPhoto();
    neighborBarcode.value = undefined;
    bk.value = undefined;
  }
  if (errorType.value !== 'other') {
    errorNote.value = undefined;
  }
};

const trySubmit = () => {
  confirmSelect.value = true;
};

const makeSubmit = () => {
  if (formRef.value) {
    onSubmit();
  }
  confirmSelect.value = false;
};

const clearPhoto = () => {
  if (photoInput.value) photoInput.value.value = '';
  if (photoPreview.value) URL.revokeObjectURL(photoPreview.value);
  photoPreview.value = '';
  photo.value = null;
};

const clearData = () => {
  let val = undefined;
  worker.value = val;
  name.value = val;
  square.value = val;
  errorNote.value = val;
  neighborBarcode.value = val;
  bk.value = val;
  errorType.value = '';
  clearPhoto();
};

clearData();

const onSubmit = async () => {
  const formData = new FormData();

  formData.append('worker', worker.value || '');
  formData.append('name', name.value || '');
  formData.append('square', square.value || '');
  formData.append('error_type', errorType.value || '');

  if (errorType.value === 'other') {
    formData.append('error_note', errorNote.value || '');
  }
  if (errorType.value === 'no_barcode') {
    formData.append('neighbor_barcode', neighborBarcode.value || '');
    formData.append('bk', bk.value || '');
    if (photo.value) {
      formData.append('error_photo', photo.value);
    }
  }

  isLoading.value = true;
  try {
    const res = await fetch(import.meta.env.VITE_API_URL + '/api/' + import.meta.env.VITE_ENDPOINT_CODE + '/v1.0/submit/' + import.meta.env.VITE_DIRECTION_CODE, {
      method: 'POST',
      body: formData,
      headers: {
        'Accept': 'application/json',
      }
    });
    isLoading.value = false;
    if (res.status != 200) {
      const ddd = await res.json();
      errorText.value = 'Не удалось отправить запрос (' + (ddd?.message ? ddd.message : res.status) + ')';
      return;
    }
    const data = await res.json();
    if (data.success) {
      // Для «Товар не в заказе» — отдельное предупреждение о старшем смены
      if (errorType.value === 'not_in_order') {
        warningText.value = 'Дождитесь старшего смены! Решение по данной ошибке примет он!';
      } else {
        successText.value = 'Успешно отправлено!';
      }
      clearData();
    } else {
      errorText.value = 'Не удалось отправить запрос';
    }
  } catch (e) {
    isLoading.value = false;
    errorText.value = 'Ошибка при отправке запроса, попробуйте еще раз';
  }
};
</script>
