<template>
  <div id="vchat-widget" class="vchat-container">
    <!-- vChat script sẽ tự động tạo widget ở đây -->
  </div>
</template>

<script setup>
import { onMounted } from 'vue'

onMounted(() => {
  // Script vChat từ Nhanh.vn
  const script = document.createElement('script')
  script.type = 'text/javascript'
  script.async = true
  script.defer = true
  script.textContent = `
    var __vnp = {
      code: 28492,
      key: '',
      secret: '06ec0ef78d8d33e06025493767e8d7bb'
    };
    (function() {
      var ga = document.createElement('script');
      ga.type = 'text/javascript';
      ga.async = true;
      ga.defer = true;
      ga.src = '//core.vchat.vn/code/tracking.js?v=53557';
      var s = document.getElementsByTagName('script');
      s[0].parentNode.insertBefore(ga, s[0]);
    })();
  `
  
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
.vchat-container {
  /* Container cho vChat widget */
  position: relative;
  z-index: 1000;
}

/* Custom styles cho vChat widget nếu cần */
:deep(.vchat-widget) {
  position: fixed !important;
  bottom: 20px !important;
  right: 20px !important;
  z-index: 999 !important;
}

/* Điều chỉnh vị trí để không che nút message */
@media (min-width: 768px) {
  :deep(.vchat-widget) {
    bottom: 90px !important; /* Để có khoảng cách với nút message */
  }
}
</style>
