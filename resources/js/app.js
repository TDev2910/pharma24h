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

// Quét toàn bộ trang một lần ở ngoài
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

        // XỬ LÝ LAYOUT: Chỉ gán khi bản thân component không khai báo layout: null
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

// Tự động cuộn về đầu trang sau khi chuyển trang thành công
router.on("finish", () => {
    window.scrollTo(0, 0);
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
});
