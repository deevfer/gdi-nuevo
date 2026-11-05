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
.site-footer, .site-footer * { box-sizing: border-box; }
.site-footer { width: 100%; padding: 58px 0 28px; color: #f5f5f5; background: #0c0c0c; }
.footer-inner { width: min(100% - 64px, 1360px); margin: 0 auto; }
.footer-top { display: grid; grid-template-columns: minmax(220px, 1.6fr) repeat(3, minmax(130px, 1fr)); gap: clamp(26px, 4vw, 70px); padding-bottom: 72px; }
.footer-brand { color: #fff; font: 400 20px/1.3 Georgia, 'Times New Roman', serif; text-decoration: none; }
.footer-about p { max-width: 320px; margin: 22px 0 0; color: #777; font-size: 13px; line-height: 1.75; }
.footer-column h2 { margin: 0 0 20px; color: #f4f4f4; font: 400 15px/1.3 Georgia, 'Times New Roman', serif; }
.footer-column ul { display: flex; flex-direction: column; gap: 12px; margin: 0; padding: 0; list-style: none; }
.footer-column a { color: #777; font-size: 13px; line-height: 1.45; text-decoration: none; transition: color .15s ease; }
.footer-column a:hover { color: #fff; }
.footer-column a:focus-visible, .footer-brand:focus-visible { outline: 2px solid #b98251; outline-offset: 4px; }
.footer-bottom { display: flex; justify-content: space-between; align-items: center; gap: 20px; padding-top: 24px; border-top: 1px solid #292929; color: #707070; font-size: 12px; line-height: 1.5; }
@media (max-width: 900px) {
    .footer-top { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 32px; padding-bottom: 52px; }
    .footer-about { grid-column: 1 / -1; }
}
@media (max-width: 600px) {
    .site-footer { padding: 48px 0 24px; }
    .footer-inner { width: min(100% - 40px, 1360px); }
    .footer-top { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 36px 24px; padding-bottom: 42px; }
    .footer-about { grid-column: 1 / -1; }
    .footer-about p { margin-top: 14px; }
    .footer-column h2 { margin-bottom: 14px; }
    .footer-bottom { flex-direction: column; align-items: flex-start; gap: 12px; }
}
@media (max-width: 380px) {
    .footer-top { grid-template-columns: 1fr; }
}
@media (prefers-reduced-motion: reduce) {
    .site-footer * { transition: none !important; }
}
</style>
