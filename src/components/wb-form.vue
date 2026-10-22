<template>
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <h1 class="mb-3">{{ t('report.title') }}</h1>
        <form
            ref="formRef"
            :class="{'opacity-50':isLoading}" @submit.prevent="onSubmit">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="worker" class="form-label">{{ t('report.workerId') }}<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="worker" type="number" inputmode="numeric" class="form-control" autofocus id="worker" name="worker" required>
                <span @click.prevent="worker = ''" class="btn btn-warning">x</span>
              </div>
            </div>

            <!-- Ошибка: радиокнопки -->
            <div class="col-md-12">
              <label class="form-label d-block">{{ t('report.errorLabel') }}<sup class="text-danger">*</sup></label>
              <div class="d-flex flex-wrap gap-3">
                <div class="form-check" v-for="opt in errorOptions" :key="opt.value">
                  <input class="form-check-input" type="radio" name="error_type" :id="'err-' + opt.value"
                         :value="opt.value" v-model="errorType" @change="onTypeChange">
                  <label class="form-check-label" :for="'err-' + opt.value">{{ opt.label }}</label>
                </div>
              </div>
            </div>

            <!-- Другая причина (текст) -->
            <div class="col-md-12" v-if="showErrorNote">
              <label for="error_note" class="form-label">{{ t('report.otherReason') }}<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="errorNote" type="text" class="form-control" id="error_note" name="error_note">
                <span @click.prevent="errorNote = ''" class="btn btn-warning">x</span>
              </div>
            </div>

            <!-- ID (ШК) товара — фото — «Товар не в заказе» / «Неверный офис» -->
            <div class="col-md-12" v-if="showProductBarcode">
              <label for="product-barcode-photo" class="form-label">{{ t('report.productBarcode') }}<sup class="text-danger">*</sup></label>
              <div class="d-flex flex-column">
                <input ref="productBarcodeInput" type="file" accept="image/*" capture="environment"
                       id="product-barcode-photo" @change="onProductBarcodeSelected" class="form-control mb-2"/>
                <div v-if="productBarcodePreview" class="d-flex flex-wrap gap-2">
                  <img :src="productBarcodePreview" style="max-width: 180px; max-height: 180px; border-radius:6px;" alt="">
                </div>
              </div>
            </div>

            <!-- Соседний ID (ШК) с поступления — «Без ID (ШК)» -->
            <div class="col-md-12" v-if="showNeighbor">
              <label for="neighbor_barcode" class="form-label">{{ t('report.neighborBarcode') }}<sup class="text-danger">*</sup></label>
              <div class="d-flex">
                <input v-model="neighborBarcode" type="text" class="form-control" id="neighbor_barcode" name="neighbor_barcode">
                <span @click.prevent="neighborBarcode = ''" class="btn btn-warning">x</span>
              </div>
            </div>

            <!-- ТТН + номер — «Без ID (ШК)» -->
            <template v-if="showTtn">
              <div class="col-md-12">
                <label class="form-label d-block">{{ t('report.ttn') }}<sup class="text-danger">*</sup></label>
                <div class="d-flex flex-wrap gap-3">
                  <div class="form-check" v-for="opt in ttnOptions" :key="opt.value">
                    <input class="form-check-input" type="radio" name="ttn_type" :id="'ttn-' + opt.value"
                           :value="opt.value" v-model="ttnType">
                    <label class="form-check-label" :for="'ttn-' + opt.value">{{ opt.label }}</label>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <label for="ttn_number" class="form-label">{{ t('report.ttnNumber') }}<sup class="text-danger">*</sup></label>
                <div class="d-flex">
                  <input :value="ttnNumber" @input="onTtnInput" type="text" inputmode="numeric" class="form-control" id="ttn_number" name="ttn_number">
                  <span @click.prevent="ttnNumber = ''" class="btn btn-warning">x</span>
                </div>
              </div>
            </template>

            <!-- Фото товара — «Товар не в заказе» / «Неверный офис» / «Без ID (ШК)» -->
            <div class="col-md-12" v-if="showProductPhoto">
              <label for="product-photo" class="form-label">{{ t('report.productPhoto') }}<sup class="text-danger">*</sup></label>
              <div class="d-flex flex-column">
                <input ref="productPhotoInput" type="file" accept="image/*" capture="environment"
                       id="product-photo" @change="onProductPhotoSelected" class="form-control mb-2"/>
                <div v-if="productPhotoPreview" class="d-flex flex-wrap gap-2">
                  <img :src="productPhotoPreview" style="max-width: 180px; max-height: 180px; border-radius:6px;" alt="">
                </div>
              </div>
            </div>

            <!-- Фото ошибки с ТСД — «Неверный офис» -->
            <div class="col-md-12" v-if="showOfficeIdPhoto">
              <label for="office-id-photo" class="form-label">{{ t('report.officeIdPhoto') }}<sup class="text-danger">*</sup></label>
              <div class="d-flex flex-column">
                <input ref="officeIdPhotoInput" type="file" accept="image/*" capture="environment"
                       id="office-id-photo" @change="onOfficeIdPhotoSelected" class="form-control mb-2"/>
                <div v-if="officeIdPhotoPreview" class="d-flex flex-wrap gap-2">
                  <img :src="officeIdPhotoPreview" style="max-width: 180px; max-height: 180px; border-radius:6px;" alt="">
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="row">
                <div class="col-12">
                  <button :disabled="!canSubmit" type="button" @click.prevent="trySubmit" class="btn btn-dark w-100 fw-bold">{{ t('common.submit') }}</button>
                </div>
                <div class="col-12 small text-danger" v-if="!canSubmit && !isLoading && errorType">
                  {{ t('report.fillRequired') }}
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <Confirm
        v-if="successText"
        :title="t('common.successTitle')"
        :message="successText"
        @accept="() => successText = undefined"
        @cancel="successText = undefined;"
    />

    <Confirm
        v-if="warningText"
        :title="t('common.warningTitle')"
        :message="warningText"
        :show-cancel="false"
        @accept="() => warningText = undefined"
        @cancel="warningText = undefined;"
    />

    <Confirm
        v-if="errorText"
        :title="t('common.errorTitle')"
        :message="errorText"
        @accept="() => errorText = undefined"
        @cancel="errorText = undefined;"
    />

    <Confirm
        v-if="confirmSelect"
        :title="t('common.confirmSendTitle')"
        :message="t('common.confirmSendMessage')"
        @accept="() => makeSubmit()"
        @cancel="confirmSelect = false;"
    />
  </div>
</template>
<script lang="ts" setup>
import {computed, ref} from "vue";
import {useI18n} from "vue-i18n";
import Confirm from "@/components/Confirm.vue";

const {t} = useI18n();

const formRef = ref<HTMLFormElement>();

// Базовые поля
const worker = ref<string>();
const errorType = ref<string>('');

// Условные поля
const errorNote = ref<string>();
const neighborBarcode = ref<string>();
const ttnType = ref<string>('');
const ttnNumber = ref<string>('');

// Фото
const productBarcode = ref<File | null>(null);
const productBarcodePreview = ref<string>('');
const productBarcodeInput = ref<HTMLInputElement | null>(null);
const productPhoto = ref<File | null>(null);
const productPhotoPreview = ref<string>('');
const productPhotoInput = ref<HTMLInputElement | null>(null);
const officeIdPhoto = ref<File | null>(null);
const officeIdPhotoPreview = ref<string>('');
const officeIdPhotoInput = ref<HTMLInputElement | null>(null);

const confirmSelect = ref<boolean>(false);
const errorText = ref<string>();
const successText = ref<string>();
const warningText = ref<string>();
const isLoading = ref(false);

const errorOptions = computed(() => [
  {value: 'not_in_order', label: t('report.errNotInOrder')},
  {value: 'office_error', label: t('report.errOffice')},
  {value: 'no_barcode', label: t('report.errNoBarcode')},
  {value: 'other', label: t('report.errOther')},
]);

const ttnOptions = computed(() => [
  {value: 'wb_gi', label: t('report.ttnWbGi')},
  {value: 'wb_mp', label: t('report.ttnWbMp')},
  {value: 'grid_number', label: t('report.ttnGrid')},
]);

// Попапы-инструкции по типу ошибки (после успешной отправки) — ключи перевода
const ALERT_KEYS: Record<string, string> = {
  not_in_order: 'report.alertNotInOrder',
  office_error: 'report.alertOffice',
  no_barcode: 'report.alertNoBarcode',
};

// Условная видимость
const showErrorNote = computed(() => errorType.value === 'other');
const showProductBarcode = computed(() => ['not_in_order', 'office_error'].includes(errorType.value));
const showNeighbor = computed(() => errorType.value === 'no_barcode');
const showTtn = computed(() => errorType.value === 'no_barcode');
const showProductPhoto = computed(() => ['not_in_order', 'office_error', 'no_barcode'].includes(errorType.value));
const showOfficeIdPhoto = computed(() => errorType.value === 'office_error');

function onProductBarcodeSelected(e: Event) {
  const input = e.target as HTMLInputElement;
  if (!input.files || !input.files.length) return;
  if (productBarcodePreview.value) URL.revokeObjectURL(productBarcodePreview.value);
  productBarcode.value = input.files[0];
  productBarcodePreview.value = URL.createObjectURL(productBarcode.value);
}

function onProductPhotoSelected(e: Event) {
  const input = e.target as HTMLInputElement;
  if (!input.files || !input.files.length) return;
  if (productPhotoPreview.value) URL.revokeObjectURL(productPhotoPreview.value);
  productPhoto.value = input.files[0];
  productPhotoPreview.value = URL.createObjectURL(productPhoto.value);
}

function onOfficeIdPhotoSelected(e: Event) {
  const input = e.target as HTMLInputElement;
  if (!input.files || !input.files.length) return;
  if (officeIdPhotoPreview.value) URL.revokeObjectURL(officeIdPhotoPreview.value);
  officeIdPhoto.value = input.files[0];
  officeIdPhotoPreview.value = URL.createObjectURL(officeIdPhoto.value);
}

// Разрешаем ввод только цифр
function onTtnInput(e: Event) {
  const input = e.target as HTMLInputElement;
  ttnNumber.value = input.value.replace(/\D/g, '');
  input.value = ttnNumber.value;
}

const canSubmit = computed(() => {
  if (!worker.value || !errorType.value || isLoading.value) {
    return false;
  }
  switch (errorType.value) {
    case 'other':
      return !!errorNote.value;
    case 'not_in_order':
      return !!(productBarcode.value && productPhoto.value);
    case 'office_error':
      return !!(productBarcode.value && officeIdPhoto.value && productPhoto.value);
    case 'no_barcode':
      return !!(neighborBarcode.value && ttnType.value && ttnNumber.value && productPhoto.value);
    default:
      return false;
  }
});

const clearProductBarcode = () => {
  if (productBarcodeInput.value) productBarcodeInput.value.value = '';
  if (productBarcodePreview.value) URL.revokeObjectURL(productBarcodePreview.value);
  productBarcodePreview.value = '';
  productBarcode.value = null;
};

const clearProductPhoto = () => {
  if (productPhotoInput.value) productPhotoInput.value.value = '';
  if (productPhotoPreview.value) URL.revokeObjectURL(productPhotoPreview.value);
  productPhotoPreview.value = '';
  productPhoto.value = null;
};

const clearOfficeIdPhoto = () => {
  if (officeIdPhotoInput.value) officeIdPhotoInput.value.value = '';
  if (officeIdPhotoPreview.value) URL.revokeObjectURL(officeIdPhotoPreview.value);
  officeIdPhotoPreview.value = '';
  officeIdPhoto.value = null;
};

// Сброс полей, не относящихся к выбранному типу ошибки
const onTypeChange = () => {
  errorNote.value = undefined;
  neighborBarcode.value = undefined;
  ttnType.value = '';
  ttnNumber.value = '';
  clearProductBarcode();
  clearProductPhoto();
  clearOfficeIdPhoto();
};

const clearData = () => {
  worker.value = undefined;
  errorType.value = '';
  errorNote.value = undefined;
  neighborBarcode.value = undefined;
  ttnType.value = '';
  ttnNumber.value = '';
  clearProductBarcode();
  clearProductPhoto();
  clearOfficeIdPhoto();
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

const onSubmit = async () => {
  const formData = new FormData();

  formData.append('worker', worker.value || '');
  formData.append('error_type', errorType.value || '');

  if (errorType.value === 'other') {
    formData.append('error_note', errorNote.value || '');
  }
  if (showProductBarcode.value && productBarcode.value) {
    formData.append('product_barcode', productBarcode.value);
  }
  if (showNeighbor.value) {
    formData.append('neighbor_barcode', neighborBarcode.value || '');
  }
  if (showTtn.value) {
    formData.append('ttn_type', ttnType.value || '');
    formData.append('ttn_number', ttnNumber.value || '');
  }
  if (showProductPhoto.value && productPhoto.value) {
    formData.append('product_photo', productPhoto.value);
  }
  if (showOfficeIdPhoto.value && officeIdPhoto.value) {
    formData.append('office_id_photo', officeIdPhoto.value);
  }

  const submittedType = errorType.value;

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
      errorText.value = t('common.submitErrorStatus', {n: ddd?.message ? ddd.message : res.status});
      return;
    }
    const data = await res.json();
    if (data.success) {
      if (ALERT_KEYS[submittedType]) {
        warningText.value = t(ALERT_KEYS[submittedType]);
      } else {
        successText.value = t('common.successSent');
      }
      clearData();
    } else {
      errorText.value = t('common.submitError');
    }
  } catch (e) {
    isLoading.value = false;
    errorText.value = t('common.networkError');
  }
};
</script>
