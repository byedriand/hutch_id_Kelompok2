import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    build: {
        minify: "terser",
        terserOptions: {
            compress: {
                drop_console: true,
            },
        },
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ["axios"],
                },
            },
        },
        chunkSizeWarningLimit: 1000,
        cssCodeSplit: true,
        sourcemap: false,
    },
    server: {
        hmr: {
            host: "localhost",
            port: 5173,
        },
    },
});
