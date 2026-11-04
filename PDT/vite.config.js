import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue(),
    ],
    optimizeDeps: {
        exclude: ['vue-i18n'],
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            'vue-i18n': 'vue-i18n/dist/vue-i18n.esm-bundler.js',
            'vue-I18n': 'vue-i18n/dist/vue-i18n.esm-bundler.js',
            '@': '/resources/js',
        },
    },
});
