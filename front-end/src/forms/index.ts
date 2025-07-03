import { createApp } from 'vue'
import '../style.css'
import './index.css'
import Form from './Form.vue'
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';



// DockFunnelsFormData is provided by the PHP script
declare global {
    interface Window {
        DockFunnelsForm: {
            ajaxUrl: string;
            nonce: string;
            formId: number;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('DockFunnelsFormData:', window.DockFunnelsForm);
    const app = createApp(Form)
    app.use(PrimeVue, {
        theme: {
            preset: Aura,
            options: {
                darkModeSelector: '', // Selector for dark mode, leave empty to disable
            }
        }
    })
    app.provide('ajaxUrl', window.DockFunnelsForm?.ajaxUrl);
    app.provide('nonce', window.DockFunnelsForm?.nonce);
    app.provide('formId', window.DockFunnelsForm?.formId);
    app.mount('#dock-funnels-form')
    console.log('DockFunnelsForm app mounted');
});