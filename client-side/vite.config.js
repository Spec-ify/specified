import { defineConfig } from 'vite';
import {basename} from "path";

// vite.config.js
export default defineConfig({
    base: "/client-side/bundle",
    build: {
        outDir: "./bundle",
        cssCodeSplit: true,
        sourcemap: true,
        rollupOptions: {
            // overwrite default .html entry
            input: [ './head-imports.html' ],
            output: {
                /*manualChunks: {
                    jquery: ["jquery"],
                    mdb: ["mdb-ui-kit"],
                    datatables: ["datatables.net-bs5"]
                }*/
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id.toString().split('node_modules/')[1].split('/')[0].toString();
                    } else {
                        return basename(id).split(".")[0];
                    }
                }
            }
        },
    },
})
