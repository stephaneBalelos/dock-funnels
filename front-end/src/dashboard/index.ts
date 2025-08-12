import { createApp } from 'vue'
import './style.css'
import './index.css'
import FormEditor from './FormEditor.vue'
import FormResponses from './FormResponses.vue'
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
            adminUrl: string;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const page = new URLSearchParams(window.location.search).get('page');
    console.log(page);
    if (page === 'dock-funnels-editor') {
        mountFormEditor();
    } else if (page === 'dock-funnels-responses') {
        mountFormResponses();
    }
});

function mountFormEditor() {
    const app = createApp(FormEditor);
    app.use(PrimeVue, {
        theme: {
            preset: Aura,
            options: {
                darkModeSelector: false, // Selector for dark mode, leave empty to disable
            }
        }
    });
    app.use(ToastService);
    app.directive('tooltip', Tooltip);
    app.provide('ajaxUrl', window.DockFunnelsAdmin?.ajaxUrl);
    app.provide('nonce', window.DockFunnelsAdmin?.nonce);
    app.provide('editFormId', new URLSearchParams(window.location.search).get('form_id') || null);
    app.mount('#dock-funnels-editor');
}

function mountFormResponses() {
    const app = createApp(FormResponses);
    app.use(PrimeVue, {
        theme: {
            preset: Aura,
            options: {
                darkModeSelector: false, // Selector for dark mode, leave empty to disable
            }
        }
    });
    app.use(ToastService);
    app.directive('tooltip', Tooltip);
    app.provide('ajaxUrl', window.DockFunnelsAdmin?.ajaxUrl);
    app.provide('adminUrl', window.DockFunnelsAdmin?.adminUrl);
    app.provide('nonce', window.DockFunnelsAdmin?.nonce);
    app.provide('formId', new URLSearchParams(window.location.search).get('form_id') || null);
    app.mount('#dock-funnels-responses');
}

