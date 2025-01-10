import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import vueDevTools from 'vite-plugin-vue-devtools';
import Inspector from 'vite-plugin-vue-inspector'; // OR

export default defineConfig({
    plugins: [
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        vueDevTools({ appendTo: 'app.ts' }),
        laravel({
            input: 'resources/js/app.ts',
            refresh: true,
        }),
        Inspector(),
    ],
});
