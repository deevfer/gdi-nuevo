import { createApp, h } from 'vue';
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';
import Home from './pages/Home.vue';

const appElement = document.getElementById('app');

if (appElement) {
    const sharedContent = window.gdiSharedContent ?? {};

    createApp({
        render() {
            return h('div', { class: 'gdi-site' }, [
                h(Header, { content: sharedContent.header ?? {} }),
                h(Home, { content: window.gdiHomeContent ?? {} }),
                h(Footer, { content: sharedContent.footer ?? {} }),
            ]);
        },
    }).mount(appElement);
}
