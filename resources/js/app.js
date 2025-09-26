import { createApp, h } from 'vue';
import { createInertiaApp, Link, router } from '@inertiajs/vue3';
import Header from './Pages/Public/components/Header.vue';
import Footer from './Pages/Public/components/Footer.vue';

// Mount Header component
const headerApp = createApp(Header);
headerApp.mount('#header-app');

// Mount Footer component  
const footerApp = createApp(Footer);
footerApp.mount('#footer-app');

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
    return pages[`./Pages/${name}.vue`];
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) });
    app.use(plugin).component('Link', Link).mount(el);
  },
});

// Intercept links marked for SPA navigation from Blade header/footer
document.addEventListener('click', (event) => {
  const anchor = event.target.closest('a[data-inertia]');
  if (!anchor) return;
  if (event.defaultPrevented || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
  const href = anchor.getAttribute('href');
  if (!href || href.startsWith('http')) return; // only same-origin relative links
  event.preventDefault();
  router.visit(href);
});
