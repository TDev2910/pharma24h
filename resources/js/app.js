import { createApp, h } from "vue";
import { createInertiaApp, Link, router } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import "./bootstrap";
import { ZiggyVue } from "ziggy-js";

// UI Framework & Plugins
import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";
import ToastService from "primevue/toastservice";
import "primeicons/primeicons.css";

// Import Layouts
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

createInertiaApp({
    title: (title) => (title ? `${title} - Pharma24h` : "Pharma24h"),
    resolve: (name) => {
        return resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ).then((page) => {
            if (page.default.layout === undefined) {
                if (name.startsWith("Admin/")) {
                    page.default.layout = AdminLayout;
                } else if (name.startsWith("Public/")) {
                    page.default.layout = PublicLayout;
                } else if (name.startsWith("Staff/")) {
                    page.default.layout = StaffLayout;
                } else if (name.startsWith("User/")) {
                    page.default.layout = UserLayout;
                }
            }
            return page;
        });
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
    progress: {
        color: "#1a56db", // Màu xanh thương hiệu PCT Pharma
        showSpinner: true,
    },
});

// Xử lý scroll khi chuyển trang
router.on("finish", () => {
    window.scrollTo({ top: 0, behavior: "instant" });
});

