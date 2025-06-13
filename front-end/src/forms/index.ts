import { createApp } from 'vue'
import '../style.css'
import './index.css'
import Form from './Form.vue'

// DockFunnelsFormData is provided by the PHP script
declare global {
    interface Window {
        DockFunnelsFormData: {
            apiUrl: string;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('DockFunnelsFormData:', window.DockFunnelsFormData);
    createApp(Form).mount('#dock-funnels-form')
});