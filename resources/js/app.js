import { createApp, h } from "vue";
import { createInertiaApp, Link, router } from "@inertiajs/vue3";
import "./bootstrap";

// Expose route globally
import { ZiggyVue } from "ziggy-js";

import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";
import ToastService from "primevue/toastservice";
import "primeicons/primeicons.css";

import AdminLayout from "@/Layouts/AdminLayout.vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import StaffLayout from "@/Layouts/StaffLayout.vue";
import UserLayout from "@/Layouts/UserLayout.vue";

// Import Firebase modules
import "./library/firebase";
import "./library/firebasePhoneAuth";

const primevueOptions = {
    ripple: true,
    inputVariant: "outlined",
    theme: {
        preset: Aura,
        options: {
            darkModeSelector: "none",
            cssLayer: false,
        },
    },
};

// 1. Quét toàn bộ trang một lần duy nhất ở ngoài (Fix Duplicate Layout)
const pages = import.meta.glob("./Pages/**/*.vue");

createInertiaApp({
    resolve: async (name) => {
        const pagePath = `./Pages/${name}.vue`;
        
        // 2. Nạp trang bất đồng bộ (Fix lỗi "dữ liệu thô" JSON)
        const pageModule = await pages[pagePath]();
        const page = pageModule.default;

        // Gán layout mặc định cho các thư mục tương ứng
        if (name.startsWith("Admin/")) {
            page.layout = page.layout || AdminLayout;
        } else if (name.startsWith("Public/")) {
            page.layout = page.layout || PublicLayout;
        } else if (name.startsWith("Staff/")) {
            page.layout = page.layout || StaffLayout;
        } else if (name.startsWith("User/")) {
            page.layout = page.layout || UserLayout;
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, primevueOptions)
            .use(ToastService)
            .component("Link", Link)
            .mount(el);
    },
});

// 3. Bộ đánh chặn click chuột (Để nhấn Logo không bị reload trang & thoát lỗi Duplicate)
document.addEventListener("click", (event) => {
    const anchor = event.target.closest("a[data-inertia]");
    if (!anchor) return;
    
    const href = anchor.getAttribute("href");
    if (!href || href.startsWith("http")) return;
    
    event.preventDefault();
    router.visit(href);
});

// Tự động cuộn về đầu trang sau khi chuyển trang thành công
router.on("finish", () => {
    window.scrollTo(0, 0);
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
});
