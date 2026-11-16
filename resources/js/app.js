import { createApp, h } from 'vue';
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';
import Home from './pages/Home.vue';
import Plataforma from './pages/Plataforma.vue';
import FinanciamientoEstructurado from './pages/FinanciamientoEstructurado.vue';

const appElement = document.getElementById('app');

if (appElement) {
    const sharedContent = window.gdiSharedContent ?? {};
    const pages = {
        home: Home,
        platform: Plataforma,
        financing: FinanciamientoEstructurado,
    };
    const currentPage = pages[window.gdiPageName] ?? Home;
    const currentContent = window.gdiPageContent ?? {};

    createApp({
        render() {
            return h('div', { class: 'gdi-site' }, [
                h(Header, { content: sharedContent.header ?? {} }),
                h(currentPage, { content: currentContent }),
                h(Footer, { content: sharedContent.footer ?? {} }),
            ]);
        },
    }).mount(appElement);
}
