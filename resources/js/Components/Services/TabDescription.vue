<template>
  <div class="tab-content transition-fade">
    <div class="form-section">
      <div class="form-field full-width">
        <label class="field-label">MÔ TẢ CHI TIẾT</label>
        <Editor 
            v-model="form.mo_ta" 
            editorStyle="height: 380px" 
            placeholder="Nhập mô tả chi tiết về dịch vụ..." 
            class="field-editor"
            :class="{ 'p-invalid': form.errors.mo_ta }" 
        />
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

<style scoped>
/* Đồng bộ label in hoa cho chuẩn y tế */
.field-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    margin-bottom: 10px;
    letter-spacing: 0.5px;
}

.transition-fade {
    animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
