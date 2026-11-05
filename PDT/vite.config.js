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
    build: {
        chunkSizeWarningLimit: 800,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('xlsx')) {
                            return 'vendor-xlsx';
                        }
                        if (id.includes('vue') || id.includes('vue-router') || id.includes('vue-i18n')) {
                            return 'vendor-vue';
                        }
                        if (id.includes('axios') || id.includes('qrcode')) {
                            return 'vendor-utils';
                        }
                        return 'vendor';
                    }
                },
            },
        },
    },
});
