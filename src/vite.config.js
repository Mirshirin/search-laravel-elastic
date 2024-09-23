import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0',  // برای دسترسی از خارج کانتینر
        port: 5173,       // پورت که برای Vite مشخص کرده‌اید
        hmr: {
            host: 'localhost',  // میزبان HMR
        },
    },
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
