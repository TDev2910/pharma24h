<template>
  <div class="tab-content">
    <div class="form-section">
      <div class="form-field full-width">
        <label class="field-label">Mô tả sản phẩm</label>
        <Editor v-model="form.mo_ta" editorStyle="height: 320px" placeholder="Nhập mô tả sản phẩm" class="field-editor" :class="{ 'p-invalid': form.errors.mo_ta }" />
        <small v-if="form.errors.mo_ta" class="p-error">{{ form.errors.mo_ta }}</small>
      </div>
    </div>
  </div>
</template>

<script setup>
import Editor from 'primevue/editor'

defineProps({
  form: { type: Object, required: true }
})
</script>
