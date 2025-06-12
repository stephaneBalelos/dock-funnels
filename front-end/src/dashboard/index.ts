import { createApp } from 'vue'
import './style.css'
import './index.css'
import App from './App.vue'

// DockFunnelsData is provided by the PHP script
declare global {
    interface Window {
        DockFunnelsData: {
            apiUrl: string;
        }
    }
}

console.log('DockFunnelsData:', window.DockFunnelsData);
if (!window.DockFunnelsData) {
    console.log('DockFunnelsData is not defined. Please ensure the PHP script is correctly enqueuing the data.');
}


createApp(App).mount('#app')
