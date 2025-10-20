<template>
  <div id="vchat-widget" class="vchat-container">
    <!-- vChat script sẽ tự động tạo widget ở đây -->
  </div>
</template>

<script setup>
import { onMounted } from 'vue'

onMounted(() => {
  // Script vChat từ Nhanh.vn
 
  
  // Thêm script vào head
  document.head.appendChild(script)
  
  // Điều chỉnh vị trí vChat widget sau khi load
  setTimeout(() => {
    const vchatWidget = document.querySelector('.vchat-widget, [class*="vchat"], #vchat-widget');
    if (vchatWidget) {
      // Tính toán vị trí chính xác, tránh scrollbar
      const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
      const rightPosition = 20 + scrollbarWidth;
      
      vchatWidget.style.right = rightPosition + 'px';
      vchatWidget.style.bottom = '90px';
      vchatWidget.style.position = 'fixed';
      vchatWidget.style.zIndex = '999';
      
      console.log('vChat widget position adjusted for scrollbar:', rightPosition + 'px');
    }
  }, 2000);
  
  console.log('vChat widget script loaded')
})
</script>

<style scoped>

</style>
