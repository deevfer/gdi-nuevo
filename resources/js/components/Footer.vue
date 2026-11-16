<script setup>
import { computed } from 'vue';

// El footer se comparte entre todas las páginas.
const props = defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
});

const defaultGroups = [
    {
        title: 'Plataforma',
        links: [
            { label: 'Acerca de GDI', url: '/plataforma' },
            { label: 'Para Inversionistas', url: '/inversionistas' },
            { label: 'Contacto', url: '/contacto' },
        ],
    },
    {
        title: 'Servicios',
        links: [
            { label: 'Estrategias de Inversión', url: '/estrategias-de-inversion' },
            { label: 'Capital Solutions', url: '/financiamiento-estructurado' },
        ],
    },
    {
        title: 'Recursos',
        links: [
            { label: 'Insights', url: '/insights' },
            { label: 'Contacto', url: '/contacto' },
        ],
    },
];

const brandName = computed(() => props.content.brand_name || 'GDI Valores');
const description = computed(() => props.content.description ||
    'Plataforma institucional regulada para la administración de capital y estructuración de financiamiento.'
);
const groups = computed(() => Array.isArray(props.content.groups)
    ? props.content.groups : defaultGroups
);
const copyright = computed(() => props.content.copyright ||
    `© ${new Date().getFullYear()} GDI Valores. Todos los derechos reservados.`
);
const bottomNote = computed(() => props.content.bottom_note || 'Miembro del ecosistema Aura Capital');
</script>

<template>
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-top">
                <div class="footer-about">
                    <a href="/" class="footer-brand">{{ brandName }}</a>
                    <p>{{ description }}</p>
                </div>

                <div
                    v-for="(group, index) in groups"
                    :key="`${group.title}-${index}`"
                    class="footer-column"
                >
                    <h2>{{ group.title }}</h2>
                    <ul>
                        <li v-for="(link, linkIndex) in (group.links || [])" :key="`${link.url}-${linkIndex}`">
                            <a :href="link.url || '#'">{{ link.label }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>{{ copyright }}</span>
                <span>{{ bottomNote }}</span>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.site-footer,
.site-footer * { box-sizing: border-box; }

.site-footer {
    width: 100%;
    min-height: 437px;
    padding: 76px 0 72px;
    color: #f5f5f5;
    background: #2a374a;
}

.footer-inner {
    width: min(100% - 96px, 1440px);
    margin: 0 auto;
}

.footer-top {
    display: grid;
    grid-template-columns: minmax(260px, 1.6fr) minmax(120px, .75fr) minmax(180px, 1.2fr) minmax(135px, 1fr);
    gap: clamp(28px, 3.7vw, 58px);
    min-height: 228px;
    padding-bottom: 82px;
}

.footer-brand {
    color: #b68150;
    font: 400 32px/1.2 Georgia, 'Times New Roman', serif;
    text-decoration: none;
}

.footer-about p {
    max-width: 335px;
    margin: 22px 0 0;
    color: #c6ccd3;
    font-size: 15px;
    line-height: 1.78;
}

.footer-column h2 {
    margin: 4px 0 22px;
    color: #ffffff;
    font: 400 16px/1.3 Georgia, 'Times New Roman', serif;
}

.footer-column ul {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.footer-column a {
    color: #929ba7;
    font-size: 15px;
    line-height: 1.4;
    text-decoration: none;
    transition: color .15s ease;
}

.footer-column a:hover { color: #ffffff; }

.footer-column a:focus-visible,
.footer-brand:focus-visible {
    outline: 2px solid #b68150;
    outline-offset: 4px;
}

.footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    padding-top: 30px;
    border-top: 1px solid rgba(255, 255, 255, .13);
    color: #c6ccd3;
    font-size: 15px;
    line-height: 1.5;
}

@media (max-width: 1000px) {
    .footer-top {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 32px;
        min-height: 0;
        padding-bottom: 56px;
    }

    .footer-about { grid-column: 1 / -1; }
}

@media (max-width: 600px) {
    .site-footer { min-height: 0; padding: 52px 0 32px; }
    .footer-inner { width: min(100% - 40px, 1440px); }

    .footer-top {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 36px 24px;
        padding-bottom: 42px;
    }

    .footer-about { grid-column: 1 / -1; }
    .footer-brand { font-size: 29px; }
    .footer-about p { margin-top: 16px; font-size: 14px; }
    .footer-column h2 { margin-bottom: 16px; }
    .footer-column a { font-size: 14px; }
    .footer-bottom { flex-direction: column; align-items: flex-start; gap: 12px; font-size: 13px; }
}

@media (max-width: 380px) {
    .footer-top { grid-template-columns: 1fr; }
}

@media (prefers-reduced-motion: reduce) {
    .site-footer * { transition: none !important; }
}
</style>
