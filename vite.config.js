import react from '@vitejs/plugin-react';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Disable fast refresh eval to satisfy strict CSP (no unsafe-eval)
const reactPlugin = react({
    fastRefresh: false,
});

export default defineConfig({
    plugins: [
        reactPlugin,
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/public.jsx'],
            refresh: true,
        }),
    ],
});
