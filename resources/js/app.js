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
  resolve: async (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue')
    const pagePath = `./Pages/${name}.vue`
    
    // if (!pages[pagePath]) {
    //   throw new Error(`Page not found: ${pagePath}`)
    // }
    
    const page = (await pages[pagePath]()).default
    
    // Gán layout mặc định cho các trang admin nếu page chưa set layout
    if (name.startsWith('Admin/') && !page.layout) {
      page.layout = AdminLayout
    }
    
    // Gán layout mặc định cho các trang Public nếu page chưa set layout
    if (name.startsWith('Public/') && !page.layout) {
      page.layout = PublicLayout
    }
    
    // Gán layout mặc định cho các trang Staff nếu page chưa set layout
    if (name.startsWith('Staff/') && !page.layout) {
      page.layout = StaffLayout
    }
    
    // Gán layout mặc định cho các trang User nếu page chưa set layout
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

// Intercept links 
document.addEventListener('click', (event) => {
  const anchor = event.target.closest('a[data-inertia]')
  if (!anchor) return
  if (event.defaultPrevented || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return
  const href = anchor.getAttribute('href')
  if (!href || href.startsWith('http')) return
  event.preventDefault()
  router.visit(href)
})

// Cách 2: Fix scroll cho toàn bộ project
router.on('finish', () => {
    window.scrollTo(0, 0);
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
})