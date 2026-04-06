import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;

window.Pusher = Pusher;

if (import.meta.env.VITE_PUSHER_APP_KEY) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
        forceTLS: true,
        enabledTransports: ['ws', 'wss'],
    });
} else {
    console.warn('VITE_PUSHER_APP_KEY is missing. Real-time features will be disabled.');
}

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

/**
 * Inertia will automatically handle CSRF token via cookies,
 * but for manual axios requests, we ensure it uses the XSRF-TOKEN cookie.
 */
window.axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
window.axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';

