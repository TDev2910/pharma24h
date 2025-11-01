import { createApp, h } from 'vue'
import { createInertiaApp, Link, router } from '@inertiajs/vue3'
import PrimeVue from 'primevue/config'
import Aura from '@primeuix/themes/aura'
import ToastService from 'primevue/toastservice'
import 'primeicons/primeicons.css'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'

// Import Firebase modules
import './config/firebase'
import './services/FirebasePhoneAuth'

// Import axios - QUAN TRỌNG
import axios from 'axios'
window.axios = axios


// Setup CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}

const primevueOptions = {
  ripple: true,
  inputVariant: 'outlined', // hoặc 'filled'
  theme: {
    preset: Aura,
    options: {
      darkModeSelector: 'none', // ép light mode → input nền trắng
      cssLayer: false
    }
  }
}

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    const pagePath = `./Pages/${name}.vue`
    
    if (!pages[pagePath]) {
      throw new Error(`Page not found: ${pagePath}`)
    }
    
    const page = pages[pagePath].default
    
    // Gán layout mặc định cho các trang admin nếu page chưa set layout
    if (name.startsWith('Admin/') && !page.layout) {
      page.layout = AdminLayout
    }
    
    // Gán layout mặc định cho các trang Public nếu page chưa set layout
    if (name.startsWith('Public/') && !page.layout) {
      page.layout = PublicLayout
    }
    
    return page
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(PrimeVue, primevueOptions)
      .use(ToastService)     // ⚡ cho useToast()
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


