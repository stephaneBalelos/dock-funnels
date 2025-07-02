import { createApp } from 'vue'
import '../style.css'
import './index.css'
import Form from './Form.vue'
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import { definePreset } from '@primeuix/themes';
import { palette } from '@primeuix/themes';
import { FormTestData } from '@/utils';



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

const designSettings = FormTestData.form_settings.design_settings

const customPrimary = palette(designSettings.colors.primary || '#0073aa')

const preset = definePreset(Aura, {
    semantic: {
        primary: {
            50: customPrimary[50],
            100: customPrimary[100],
            200: customPrimary[200],
            300: customPrimary[300],
            400: customPrimary[400],
            500: customPrimary[500],
            600: customPrimary[600],
            700: customPrimary[700],
            800: customPrimary[800],
            900: customPrimary[900],
            950: customPrimary[950],
        },
    }
})

document.addEventListener('DOMContentLoaded', () => {
    console.log('DockFunnelsFormData:', window.DockFunnelsForm);
    const app = createApp(Form)
    app.use(PrimeVue, {
        theme: {
            preset: preset,
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