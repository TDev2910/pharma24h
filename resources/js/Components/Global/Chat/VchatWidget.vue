<template>
  <div id="vchat-widget" class="vchat-container">
  </div>
</template>

<script setup>
import { onMounted } from 'vue'

//cấu hình vchat widget
onMounted(() => {
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

  document.head.appendChild(script)
  setTimeout(() => {
    const vchatWidget = document.querySelector('.vchat-widget, [class*="vchat"], #vchat-widget');
    if (vchatWidget) {
      const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
      const rightPosition = 20 + scrollbarWidth;

      vchatWidget.style.right = rightPosition + 'px';
      vchatWidget.style.bottom = '90px';
      vchatWidget.style.position = 'fixed';
      vchatWidget.style.zIndex = '999';
    }
  }, 2000);

})
</script>

<style scoped></style>
