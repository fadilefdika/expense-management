import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss", 
                "resources/js/app.js",
                "resources/js/create.js",
                "resources/js/form.js"
            ],
            refresh: true,
            valetTls: false, // ⬅ TAMBAHKAN INI untuk cegah Valet
        }),
    ],
    server: {
        host: "localhost",
    },
});
