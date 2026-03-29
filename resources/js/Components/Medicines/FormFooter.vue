<template>
  <div class="flex justify-end gap-2">
    <Button type="button" label="Hủy" severity="secondary" @click="$emit('cancel')" />
    <Button type="button" label="Lưu thuốc" @click="$emit('save')" :loading="processing" />
  </div>
</template>

<script setup>
import Button from 'primevue/button'

defineProps({
  processing: {
    type: Boolean,
    default: false
  }
})

defineEmits(['cancel', 'save'])
</script>
