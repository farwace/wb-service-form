<template>
  <div class="file-field w-100">
    <!-- Скрытый нативный input: клик по кнопке программно открывает выбор файла -->
    <input
        ref="inputRef"
        type="file"
        :accept="accept"
        :capture="capture as any"
        :multiple="multiple"
        class="file-field__native"
        @change="onChange"
    />
    <button type="button" class="file-field__btn" @click="openPicker">
      <span class="file-field__icon" aria-hidden="true">
        <svg v-if="icon === 'video'" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
          <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 0 12.5v-9zm13.5 2.939 2.273-1.7a.5.5 0 0 1 .817.382v7.758a.5.5 0 0 1-.817.382l-2.273-1.7V6.439z"/>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
          <path d="M4.5 3a2.5 2.5 0 0 1 5 0v1h3a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-9a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3V3z"/>
          <circle cx="7" cy="8" r="2.5" fill="#fff"/>
        </svg>
      </span>
      <span class="file-field__text">
        <span class="file-field__line">{{ t(attachKey) }}</span>
      </span>
    </button>
  </div>
</template>

<script lang="ts" setup>
import {ref} from "vue";
import {useI18n} from "vue-i18n";

withDefaults(defineProps<{
  accept?: string;
  capture?: 'environment' | 'user' | 'camera' | 'camcorder' | '';
  multiple?: boolean;
  icon?: 'photo' | 'video';
  attachKey?: string;
}>(), {
  accept: 'image/*',
  capture: 'environment',
  multiple: false,
  icon: 'photo',
  attachKey: 'common.attachPhoto',
});

const emit = defineEmits<{ (e: 'change', files: File[]): void }>();

const {t} = useI18n();
const inputRef = ref<HTMLInputElement | null>(null);

function openPicker() {
  inputRef.value?.click();
}

function onChange(e: Event) {
  const input = e.target as HTMLInputElement;
  const files = Array.from(input.files ?? []);
  emit('change', files);
  // Сбрасываем значение, чтобы повторный выбор того же файла снова вызывал change
  input.value = '';
}
</script>

<style lang="scss" scoped>
.file-field {
  display: inline-block;

  &__native {
    position: absolute;
    width: 1px;
    height: 1px;
    opacity: 0;
    pointer-events: none;
    overflow: hidden;
  }

  &__btn {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    padding: 10px 14px;
    background-color: #fff;
    border: 1px dashed #6c757d;
    border-radius: 6px;
    cursor: pointer;
    transition: border-color .15s ease-in-out, background-color .15s ease-in-out;

    &:active,
    &:hover {
      background-color: #f1f3f5;
      border-color: #343a40;
    }
  }

  &__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #343a40;
    flex-shrink: 0;
  }

  &__text {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
  }

  &__line {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.3;
    color: #212529;
  }
}
</style>
