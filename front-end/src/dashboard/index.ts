import { createApp } from 'vue'
import './style.css'
import './index.css'
import Dashboard from './Dashboard.vue'
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import ToastService from 'primevue/toastservice';
import Tooltip from 'primevue/tooltip';



// DockFunnelsData is provided by the PHP script
declare global {
    interface Window {
        DockFunnelsAdmin: {
            ajaxUrl: string;
            nonce: string;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const app = createApp(Dashboard)
    app.use(PrimeVue, {
        theme: {
            preset: Aura,
            options: {
                darkModeSelector: '', // Selector for dark mode, leave empty to disable
            }
        }
    })
    app.use(ToastService);
    app.directive('tooltip', Tooltip);
    app.provide('ajaxUrl', window.DockFunnelsAdmin?.ajaxUrl);
    app.provide('nonce', window.DockFunnelsAdmin?.nonce);
    app.provide('editFormId', new URLSearchParams(window.location.search).get('form_id') || null);
    app.mount('#dock-funnels-dashboard');
});
