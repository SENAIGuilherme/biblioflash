import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/pages/home.css',
                'resources/css/pages/auth-login.css',
                'resources/css/pages/privacy.css',
                'resources/css/pages/search.css',
                'resources/css/pages/books.css',
                'resources/css/pages/admin-dashboard.css',
                'resources/css/pages/book-register.css',
                'resources/css/pages/books-management.css',
                'resources/css/pages/book-show.css',
                'resources/css/pages/rfid-panel.css',
                'resources/js/app.js',
                'resources/js/layouts/main.js',
                'resources/js/main.js',
                'resources/js/auth.js',
                'resources/js/kiosk.js',
                'resources/js/pages/home.js',
                'resources/js/pages/auth-login.js',
                'resources/js/pages/books-index.js',
                'resources/js/pages/profile.js',
                'resources/js/pages/index.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
