<template>
  <div class="flex justify-end gap-2 px-4 py-3 bg-gray-50 border-t rounded-b-lg">
    <Button 
      type="button" 
      label="Hủy" 
      severity="secondary" 
      outlined
      @click="$emit('cancel')" 
    />
    <Button 
      type="button" 
      label="Lưu hàng hóa" 
      icon="pi pi-check"
      :loading="processing" 
      @click="$emit('save')" 
    />
  </div>
</template>

<script setup>
import Button from 'primevue/button'

defineProps({
  processing: { type: Boolean, default: false }
})

defineEmits(['cancel', 'save'])
</script>
