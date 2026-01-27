import {fileURLToPath, URL} from 'node:url'

import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
    build: {
        outDir: '../public',
        rollupOptions: {
            input: 'src/main.ts',
            output: {
                entryFileNames: 'app.js',
                chunkFileNames: 'vendor.js',
                assetFileNames: 'assets/[name].[ext]'
            }
        }
    },
    plugins: [vue(), vueDevTools()], resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url))
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `@import "@/styles/custom-bootstrap.scss";`
            }
        }
    }
})
