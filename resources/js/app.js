import { createApp, h } from 'vue'
import { createInertiaApp, Link, router } from '@inertiajs/vue3'
import PrimeVue from 'primevue/config'
import Aura from '@primeuix/themes/aura'
import ToastService from 'primevue/toastservice'
import 'primeicons/primeicons.css'
import AdminLayout from '@/Layouts/AdminLayout.vue'

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

// (Nếu bạn vẫn giữ header/footer là app riêng, dùng cùng 1 options)
import Header from './Pages/Public/components/Header.vue'
import Footer from './Pages/Public/components/Footer.vue'

const headerApp = createApp(Header)
headerApp.use(PrimeVue, primevueOptions).mount('#header-app')

const footerApp = createApp(Footer)
footerApp.use(PrimeVue, primevueOptions).mount('#footer-app')

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

// Intercept links (giữ nguyên của bạn)
document.addEventListener('click', (event) => {
  const anchor = event.target.closest('a[data-inertia]')
  if (!anchor) return
  if (event.defaultPrevented || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return
  const href = anchor.getAttribute('href')
  if (!href || href.startsWith('http')) return
  event.preventDefault()
  router.visit(href)
})
