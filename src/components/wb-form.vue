<template>
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <h1 class="mb-3">Место хранения</h1>
        <form :class="{
          'opacity-50':isLoading
        }" @submit.prevent="onSubmit">
          <div class="row g-3">
            <div class="col-md-12">
              <label for="location" class="form-label">ШК</label>
              <div class="d-flex">
                <input v-model="location" type="text" class="form-control" autofocus id="location" name="location" required>
                <span @click.prevent="location = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-12">
              <label for="barcode1" class="form-label">Недостача</label>
              <div class="d-flex">
                <input v-model="barcode1" type="text" class="form-control" id="barcode1" name="barcode[]" >
                <span @click.prevent="barcode1 = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-12">
              <label for="barcode2" class="form-label">Излишек</label>
              <div class="d-flex">
                <input v-model="barcode2" type="text" class="form-control" id="barcode2" name="barcode[]" >
                <span @click.prevent="barcode2 = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-12">
              <label for="barcode3" class="form-label">через ДА</label>
              <div class="d-flex">
                <input v-model="barcode3" type="text" class="form-control" id="barcode3" name="barcode[]" >
                <span @click.prevent="barcode3 = ''" class="btn btn-warning">x</span>
              </div>
            </div>

            <div class="col-md-6">
              <label for="palette" class="form-label">Обезличка ШК</label>
              <div class="d-flex">
                <input v-model="palette" type="text" class="form-control" id="palette" name="palette">
                <span @click.prevent="palette = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="wb-box" class="form-label">Обезличка видео</label>
              <div class="d-flex">
                <input v-model="wbBox" type="text" class="form-control" id="wb-box" name="wb-box">
              </div>
            </div>
            <div class="col-md-6">
              <label for="count" class="form-label">ID сотрудника</label>
              <div class="d-flex">
                <input v-model="count" type="number" class="form-control" id="count" name="count">
                <span @click.prevent="count = ''" class="btn btn-warning">x</span>
              </div>
            </div>
            <div class="col-md-6">
              <label for="product-name" class="form-label">№ Стола Приемки</label>
              <div class="d-flex">
                <input v-model="productName" type="text" class="form-control" id="product-name" name="product-name">
                <span @click.prevent="productName = ''" class="btn btn-warning">x</span>
              </div>
            </div>

            <div class="col-12">
              <div class="row">
                <div class="col-12">
                  <button :disabled="isLoading" type="submit" class="btn btn-dark w-100 fw-bold" >Отправить</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import {ref} from "vue";

const location = ref('');
const barcode1 = ref('');
const barcode2 = ref('');
const barcode3 = ref('');
const palette = ref('');
const wbBox = ref('');
const count = ref('');
const productName = ref('');

const isLoading = ref(false);

const onSubmit = async (event:any) => {
  const formData = new FormData(event.target);
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
      location.value = '';
      barcode1.value = '';
      barcode2.value = '';
      barcode3.value = '';
      palette.value = '';
      wbBox.value = '';
      count.value = '';
      productName.value = '';
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