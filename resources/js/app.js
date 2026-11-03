
import { createApp } from 'vue';
import Home from './pages/Home.vue';

const appElement = document.getElementById('app');

if (appElement) {
    createApp(Home, {
        content: window.gdiHomeContent ?? {},
    }).mount(appElement);
}
