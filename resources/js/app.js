import { createApp, h } from "vue";
import { createInertiaApp, Link, router } from "@inertiajs/vue3";
import "./bootstrap";
import { ZiggyVue } from "ziggy-js";

// UI Framework & Plugins
import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";
import ToastService from "primevue/toastservice";
import "primeicons/primeicons.css";

// Import Layouts (Đảm bảo các file này nằm trong @/Layouts, KHÔNG nằm trong @/Pages)
import AdminLayout from "@/Layouts/AdminLayout.vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import StaffLayout from "@/Layouts/StaffLayout.vue";
import UserLayout from "@/Layouts/UserLayout.vue";

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

// Caching glob cho hiệu năng
const pages = import.meta.glob("./Pages/**/*.vue");

createInertiaApp({
    resolve: async (name) => {
        const pagePath = `./Pages/${name}.vue`;

        if (!pages[pagePath]) {
            console.error(`KHÔNG TÌM THẤY COMPONENT: ${pagePath}`);
            return null;
        }

        const pageModule = await pages[pagePath]();
        const page = pageModule.default;

        /**
         * XỬ LÝ LAYOUT TỰ ĐỘNG (FIX LỖI ĐỆ QUY)
         * 1. Chỉ gán nếu page.layout chưa được định nghĩa thủ công trong file .vue
         * 2. Kiểm tra folder để gán Layout tương ứng
         */
        if (page.layout === undefined) {
            if (name.startsWith("Admin/")) {
                page.layout = AdminLayout;
            } else if (name.startsWith("Public/")) {
                page.layout = PublicLayout;
            } else if (name.startsWith("Staff/")) {
                page.layout = StaffLayout;
            } else if (name.startsWith("User/")) {
                page.layout = UserLayout;
            }
            // Mặc định nếu không thuộc folder nào bên trên (tùy chọn)
            // else { page.layout = PublicLayout; }
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        // Tránh lỗi khi hot-reload hoặc mount nhiều lần
        if (el.__vue_app__) {
            el.__vue_app__.unmount();
        }

        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, primevueOptions)
            .use(ToastService)
            .component("Link", Link); // Đăng ký Link global cho tiện sử dụng

        el.__vue_app__ = app.mount(el);
    },
});

// Bộ đánh chặn click chuột dành cho thẻ a [data-inertia] (Tối ưu cho click Logo/Mobile)
document.addEventListener("click", (event) => {
    const anchor = event.target.closest("a[data-inertia]");
    if (!anchor) return;
    
    const href = anchor.getAttribute("href");
    if (!href || href.startsWith("http")) return;
    
    event.preventDefault();
    router.visit(href);
});

// Xử lý scroll khi chuyển trang
router.on("finish", () => {
    window.scrollTo({ top: 0, behavior: "instant" });
});
