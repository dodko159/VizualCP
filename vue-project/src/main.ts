import {createApp} from 'vue'
import App from './App.vue'

import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.css';
import "./styles/custom-bootstrap.scss";
import {createI18n} from "vue-i18n";
import sk from './locales/sk.json'
import {router} from "./router.js";

let vueAppInstance: ReturnType<typeof createApp> | null = null
const i18n = createI18n({
        legacy: false,
        locale: 'sk',
        fallbackLocale: 'sk',
        messages: {
            sk: sk
        }
    }
)

window.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById('openclose_btn_text_area_priceOffer');
    if (!btn) return;

    btn.addEventListener('click', () => {
        if (vueAppInstance) {
            vueAppInstance.unmount();
            document.querySelector('#vueApp')!.innerHTML = "";
            vueAppInstance = null;
        }

        const app = createApp(App);
        vueAppInstance = app;
        app
            .use(i18n)
            .use(router)
            .mount('#vueApp');
    });
});