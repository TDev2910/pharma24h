import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5176,
        hmr: {
            host: 'localhost',
        },
        // Bắt buộc dùng polling trên Windows + Docker vì inotify không hoạt động
        watch: {
            usePolling: true,
            interval: 1000, // kiểm tra mỗi 1 giây
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/library/firebaseGoogleAuth.js',
                'resources/js/library/firebasePhoneAuth.js'
            ],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '@Admin': path.resolve(__dirname, 'resources/css/Admin'),
            '@Staff': path.resolve(__dirname, 'resources/css/Staff'),
        },
    },
});
