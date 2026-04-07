import { createApp, h } from 'vue'
import { createInertiaApp, Link, router } from '@inertiajs/vue3'
import './bootstrap'


// Expose route globally
import { ZiggyVue } from 'ziggy-js';

import PrimeVue from 'primevue/config'
import Aura from '@primeuix/themes/aura'
import ToastService from 'primevue/toastservice'
import 'primeicons/primeicons.css'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import StaffLayout from '@/Layouts/StaffLayout.vue'
import UserLayout from '@/Layouts/UserLayout.vue'

// Import Firebase modules
import './library/firebase'
import './library/firebasePhoneAuth'

const primevueOptions = {
  ripple: true,
  inputVariant: 'outlined', 
  theme: {
    preset: Aura,
    options: {
      darkModeSelector: 'none', 
      cssLayer: false
    }
  }
}

//khởi tạo spa
createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    let page = pages[`./Pages/${name}.vue`]
    
    if (!page) {
        console.error(`Page not found: ./Pages/${name}.vue`)
        return null
    }

    page = page.default || page
    
    // Gán layout mặc định
    if (name.startsWith('Admin/') && !page.layout) {
      page.layout = AdminLayout
    }
    
    if (name.startsWith('Public/') && !page.layout) {
      page.layout = PublicLayout
    }
    
    if (name.startsWith('Staff/') && !page.layout) {
      page.layout = StaffLayout
    }
    
    if (name.startsWith('User/') && !page.layout) {
      page.layout = UserLayout
    }
    
    return page
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(PrimeVue, primevueOptions)
      .use(ToastService)    
      .component('Link', Link)
      .mount(el)
  }
})

// Cách 2: Fix scroll cho toàn bộ project
router.on('finish', () => {
    window.scrollTo(0, 0);
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
})