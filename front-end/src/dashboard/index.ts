import { createApp } from 'vue'
import './style.css'
import './index.css'
import App from './App.vue'
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';

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
    const app = createApp(App)
    app.use(PrimeVue, {
        theme: {
            preset: Aura,
            options: {
                darkModeSelector: '', // Selector for dark mode, leave empty to disable
            }
        }
    })
    app.provide('ajaxUrl', window.DockFunnelsAdmin?.ajaxUrl);
    app.provide('nonce', window.DockFunnelsAdmin?.nonce);
    app.mount('#app')
    console.log('DockFunnelsAdmin mounted');
});
