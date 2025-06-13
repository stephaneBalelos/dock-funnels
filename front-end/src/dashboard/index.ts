import { createApp } from 'vue'
import './style.css'
import './index.css'
import App from './App.vue'

// DockFunnelsData is provided by the PHP script
declare global {
    interface Window {
        DockFunnelsAdmin: {
            ajaxUrl: string;
            nonce: string;
        }
    }
}

console.log('DockFunnelsData:', window.DockFunnelsAdmin);
if (!window.DockFunnelsAdmin) {
    console.log('DockFunnelsData is not defined. Please ensure the PHP script is correctly enqueuing the data.');
}


createApp(App).mount('#app')
