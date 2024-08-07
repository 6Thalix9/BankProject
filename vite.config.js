import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['/home/ubuntu/BankProject/BerkeBank/resources/css/app.css', '/home/ubuntu/BankProject/BerkeBank/resources/js/app.js'],
            refresh: true,
        }),
    ],
});


