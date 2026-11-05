<script setup>
import { computed, ref } from 'vue';

// El header se comparte entre todas las páginas.
const props = defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
});

const mobileMenuOpen = ref(false);
const brandName = computed(() => props.content.brand_name || 'GDI Valores');

const defaultLinks = [
    { label: 'Inicio', url: '/' },
    { label: 'Plataforma', url: '/plataforma' },
    { label: 'Financiamiento Estructurado', url: '/financiamiento-estructurado' },
    { label: 'Inversionistas', url: '/inversionistas' },
    { label: 'Estrategias de inversión', url: '/estrategias-de-inversion' },
    { label: 'Insights', url: '/insights' },
    { label: 'Contacto', url: '/contacto' },
];

const links = computed(() =>
    Array.isArray(props.content.links) ? props.content.links : defaultLinks
);

const currentPath = typeof window !== 'undefined'
    ? window.location.pathname.replace(/\/$/, '') || '/'
    : '/';

function isCurrent(url) {
    if (typeof url !== 'string' || !url.startsWith('/')) return false;
    return (url.replace(/\/$/, '') || '/') === currentPath;
}

function closeMenu() {
    mobileMenuOpen.value = false;
}
</script>

<template>
    <header class="site-header" @keydown.esc="closeMenu">
        <div class="header-inner">
            <a href="/" class="site-brand" @click="closeMenu">
                {{ brandName }}
            </a>

            <button
                type="button"
                class="menu-toggle"
                :aria-expanded="mobileMenuOpen"
                aria-controls="site-navigation"
                :aria-label="mobileMenuOpen ? 'Cerrar menú' : 'Abrir menú'"
                @click="mobileMenuOpen = !mobileMenuOpen"
            >
                <span :class="{ 'line-top-open': mobileMenuOpen }"></span>
                <span :class="{ 'line-middle-open': mobileMenuOpen }"></span>
                <span :class="{ 'line-bottom-open': mobileMenuOpen }"></span>
            </button>

            <nav
                id="site-navigation"
                class="site-navigation"
                :class="{ 'is-open': mobileMenuOpen }"
                aria-label="Navegación principal"
            >
                <a
                    v-for="(link, index) in links"
                    :key="`${link.url}-${index}`"
                    :href="link.url || '#'"
                    :aria-current="isCurrent(link.url) ? 'page' : undefined"
                    @click="closeMenu"
                >
                    {{ link.label }}
                </a>
            </nav>
        </div>
    </header>
</template>

<style scoped>
.site-header, .site-header * { box-sizing: border-box; }
.site-header { position: relative; z-index: 30; width: 100%; background: #fff; border-bottom: 1px solid #e5e5e5; }
.header-inner { display: flex; align-items: center; justify-content: space-between; gap: 24px; width: min(100% - 64px, 1360px); min-height: 70px; margin: 0 auto; }
.site-brand { flex-shrink: 0; color: #161616; font: 400 24px/1.2 Georgia, 'Times New Roman', serif; text-decoration: none; white-space: nowrap; }
.site-navigation { display: flex; align-items: center; justify-content: flex-end; gap: clamp(14px, 1.65vw, 27px); }
.site-navigation a { color: #7b7b7b; font-size: 12px; font-weight: 400; line-height: 1.4; text-decoration: none; white-space: nowrap; transition: color .15s ease; }
.site-navigation a:hover, .site-navigation a[aria-current='page'] { color: #222; }
.site-navigation a:focus-visible, .site-brand:focus-visible, .menu-toggle:focus-visible { outline: 2px solid #b98251; outline-offset: 5px; }
.menu-toggle { display: none; }

@media (max-width: 1020px) {
    .header-inner { width: min(100% - 40px, 1360px); min-height: 66px; }
    .site-brand { font-size: 23px; }
    .menu-toggle { display: flex; width: 40px; height: 40px; padding: 9px 6px; flex-direction: column; justify-content: space-around; align-items: center; border: 0; background: transparent; cursor: pointer; }
    .menu-toggle span { display: block; width: 24px; height: 2px; background: #1d2631; transition: transform .2s ease, opacity .2s ease; }
    .menu-toggle .line-top-open { transform: translateY(7px) rotate(45deg); }
    .menu-toggle .line-middle-open { opacity: 0; }
    .menu-toggle .line-bottom-open { transform: translateY(-7px) rotate(-45deg); }
    .site-navigation { display: none; position: absolute; top: 100%; right: 0; left: 0; padding: 14px 20px 22px; flex-direction: column; align-items: stretch; gap: 0; background: #fff; border-bottom: 1px solid #dedede; box-shadow: 0 12px 24px rgba(0,0,0,.08); }
    .site-navigation.is-open { display: flex; }
    .site-navigation a { display: block; padding: 13px 6px; border-bottom: 1px solid #f0f0f0; font-size: 14px; white-space: normal; }
    .site-navigation a:last-child { border-bottom: 0; }
}
@media (prefers-reduced-motion: reduce) {
    .site-header * { transition: none !important; }
}
</style>
