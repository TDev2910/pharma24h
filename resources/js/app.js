import { createApp, h } from "vue";
import { createInertiaApp, Link, router } from "@inertiajs/vue3";
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
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

createInertiaApp({
    resolve: (name) => {
        // Dùng resolvePageComponent để đảm bảo component được cache đúng cách bởi Vite/Inertia
        const page = resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        );

        page.then((module) => {
            // Chỉ gán Layout nếu Component chưa tự khai báo layout riêng
            if (name.startsWith("Admin/")) {
                module.default.layout = module.default.layout || AdminLayout;
            } else if (name.startsWith("Public/")) {
                module.default.layout = module.default.layout || PublicLayout;
            } else if (name.startsWith("Staff/")) {
                module.default.layout = module.default.layout || StaffLayout;
            } else if (name.startsWith("User/")) {
                module.default.layout = module.default.layout || UserLayout;
            }
        });

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

// Fix scroll về đầu trang sau khi chuyển trang
router.on("finish", () => {
    window.scrollTo(0, 0);
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
});
