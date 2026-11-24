<script setup>
import { computed } from 'vue';
import GdiLogo from '../components/GdiLogo.vue';

// Laravel entrega el contenido de Filament mediante app.js.
const props = defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
});

const hero = computed(() => props.content.hero ?? {});
const infrastructure = computed(() => props.content.infrastructure ?? {});
const diagram = computed(() => infrastructure.value.diagram ?? {});
const operatingModel = computed(() => props.content.operating_model ?? {});
const audiences = computed(() => props.content.audiences ?? {});
const platformAccess = computed(() => props.content.platform_access ?? {});

const operatingCards = computed(() =>
    Array.isArray(operatingModel.value.cards) ? operatingModel.value.cards : []
);
const audienceCards = computed(() =>
    Array.isArray(audiences.value.cards) ? audiences.value.cards : []
);

const diagramCapital = computed(() =>
    Array.isArray(diagram.value.capital_items) ? diagram.value.capital_items : []
);
const diagramCenter = computed(() =>
    Array.isArray(diagram.value.center_items) ? diagram.value.center_items : []
);
const diagramSolutions = computed(() =>
    Array.isArray(diagram.value.solutions_items) ? diagram.value.solutions_items : []
);

// El CMS mantiene un solo campo para el título.
// El diseño original destaca sus últimas cuatro palabras.
const heroTitle = computed(() => {
    const words = String(hero.value.title ?? '').trim().split(/\s+/u).filter(Boolean);

    if (words.length <= 4) {
        return { normal: words.join(' '), accent: '' };
    }

    return {
        normal: words.slice(0, -4).join(' '),
        accent: words.slice(-4).join(' '),
    };
});

// Los FileUpload de Filament guardan rutas relativas en el disco "public".
function imageUrl(path) {
    if (!path) return '';
    const value = String(path);

    if (/^(https?:)?\/\//i.test(value)) return value;
    if (value.startsWith('/')) return value;

    return `/storage/${value.replace(/^storage\//, '')}`;
}

// Imágenes originales incluidas en /public: se verán también al clonar el repositorio.
// Si el CMS tiene una imagen cargada, esa imagen prevalece sobre la predeterminada.
const IMAGE_BASE = '/images/gdi/home';

const heroDesktop = computed(() =>
    imageUrl(hero.value.image_desktop || hero.value.image_mobile)
    || `${IMAGE_BASE}/hero-arquitectura.png`
);
const heroMobile = computed(() => imageUrl(hero.value.image_mobile));

function audienceImage(card, index) {
    return imageUrl(card.image) || `${IMAGE_BASE}/${index === 0 ? 'tarjeta-inversionistas.png' : 'tarjeta-empresas.png'}`;
}

const platformBackground = computed(() =>
    imageUrl(platformAccess.value.image) || `${IMAGE_BASE}/acceso-plataforma.png`
);
</script>

<template>
    <!-- Header.vue y Footer.vue son compartidos y NO se incluyen aquí. -->
    <main class="gdi-home">
        <!-- 1. PORTADA -->
        <section id="inicio" class="home-hero" aria-labelledby="home-hero-title">
            <picture class="hero-picture">
                <source v-if="heroMobile" media="(max-width: 767px)" :srcset="heroMobile" />
                <img :src="heroDesktop" alt="" />
            </picture>
            <div class="hero-shade" aria-hidden="true"></div>

            <div class="home-container hero-content">
                <h1 id="home-hero-title" class="hero-title">
                    {{ heroTitle.normal }} <span v-if="heroTitle.accent">{{ heroTitle.accent }}</span>
                </h1>

                <p v-if="hero.description" class="hero-description">
                    {{ hero.description }}
                </p>

                <div class="hero-actions">
                    <a
                        v-if="hero.button_primary_text"
                        :href="hero.button_primary_url || '#'"
                        class="hero-button hero-button-primary"
                    >
                        {{ hero.button_primary_text }}
                        <span aria-hidden="true">→</span>
                    </a>

                    <a
                        v-if="hero.button_secondary_text"
                        :href="hero.button_secondary_url || '#'"
                        class="hero-button hero-button-secondary"
                    >
                        {{ hero.button_secondary_text }}
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- 2. INFRAESTRUCTURA -->
        <section id="infraestructura" class="home-infrastructure" aria-labelledby="home-infrastructure-title">
            <div class="home-container">
                <p v-if="infrastructure.eyebrow" class="section-eyebrow">
                    {{ infrastructure.eyebrow }}
                </p>
                <h2 id="home-infrastructure-title" class="section-title infra-title">
                    {{ infrastructure.title }}
                </h2>
                <p v-if="infrastructure.description" class="section-intro">
                    {{ infrastructure.description }}
                </p>

                <div class="infrastructure-diagram" aria-label="Estructura integrada de GDI Valores">
                    <div class="diagram-columns">
                        <div class="diagram-side diagram-capital">
                            <h3>{{ diagram.capital_title }}</h3>
                            <ul>
                                <li v-for="(item, index) in diagramCapital" :key="`capital-${index}`">
                                    {{ item }}
                                </li>
                            </ul>
                        </div>

                        <div class="diagram-center">
                            <div class="diagram-brand">
                                <GdiLogo class="brand-logo" />
                            </div>
                            <h3>{{ diagram.center_title }}</h3>
                            <ul>
                                <li v-for="(item, index) in diagramCenter" :key="`center-${index}`">
                                    {{ item }}
                                </li>
                            </ul>
                        </div>

                        <div class="diagram-side diagram-solutions">
                            <h3>{{ diagram.solutions_title }}</h3>
                            <ul>
                                <li v-for="(item, index) in diagramSolutions" :key="`solutions-${index}`">
                                    {{ item }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div v-if="diagram.footer_title || diagram.footer_description" class="diagram-footer">
                        <p v-if="diagram.footer_title" class="diagram-footer-title">
                            {{ diagram.footer_title }}
                        </p>
                        <p v-if="diagram.footer_description" class="diagram-footer-copy">
                            {{ diagram.footer_description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. MODELO OPERATIVO -->
        <section id="modelo-operativo" class="home-operating" aria-labelledby="home-operating-title">
            <div class="home-container operating-content">
                <p v-if="operatingModel.eyebrow" class="section-eyebrow light-eyebrow">
                    {{ operatingModel.eyebrow }}
                </p>
                <h2 id="home-operating-title" class="section-title operating-title">
                    {{ operatingModel.title }}
                </h2>
                <p v-if="operatingModel.description" class="section-intro operating-intro">
                    {{ operatingModel.description }}
                </p>

                <div class="operating-grid">
                    <article v-for="(card, index) in operatingCards" :key="`operating-${index}`" class="operating-card">
                        <p class="operating-number">{{ card.number }}</p>
                        <h3>{{ card.title }}</h3>
                        <p>{{ card.description }}</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- 4. ACCESO DIFERENCIADO -->
        <section id="acceso-diferenciado" class="home-audiences" aria-labelledby="home-audiences-title">
            <div class="home-container">
                <p v-if="audiences.eyebrow" class="section-eyebrow">
                    {{ audiences.eyebrow }}
                </p>
                <h2 id="home-audiences-title" class="section-title audiences-title">
                    {{ audiences.title }}
                </h2>
                <p v-if="audiences.description" class="section-intro audiences-intro">
                    {{ audiences.description }}
                </p>

                <div class="audiences-grid">
                    <article
                        v-for="(card, index) in audienceCards"
                        :key="`audience-${index}`"
                        class="audience-card"
                        :class="index === 0 ? 'audience-investors' : 'audience-businesses'"
                    >
                        <img class="audience-background" :src="audienceImage(card, index)" alt="" loading="lazy" />
                        <div class="audience-shade" aria-hidden="true"></div>

                        <div class="audience-content">
                            <span class="audience-accent" aria-hidden="true"></span>
                            <h3>{{ card.title }}</h3>
                            <p v-if="card.description">{{ card.description }}</p>
                            <p v-if="card.secondary_description">{{ card.secondary_description }}</p>
                            <a v-if="card.button_text" :href="card.button_url || '#'" class="audience-link">
                                {{ card.button_text }}
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- 5. ACCESO A LA PLATAFORMA -->
        <section id="acceso-plataforma" class="home-platform" aria-labelledby="home-platform-title">
            <img class="platform-background" :src="platformBackground" alt="" loading="lazy" />
            <div class="home-container platform-content">
                <p v-if="platformAccess.eyebrow" class="platform-eyebrow">
                    {{ platformAccess.eyebrow }}
                </p>
                <h2 id="home-platform-title" class="section-title platform-title">
                    {{ platformAccess.title }}
                </h2>
                <p v-if="platformAccess.description" class="platform-description">
                    {{ platformAccess.description }}
                </p>
                <a
                    v-if="platformAccess.button_text"
                    class="platform-button"
                    :href="platformAccess.button_url || '#'"
                >
                    {{ platformAccess.button_text }}
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </section>
    </main>
</template>

<style scoped>
/* Estilos exclusivos de Inicio; Header y Footer tienen sus propios estilos. */
.gdi-home {
    --ink: #263346;
    --navy: #243145;
    --copper: #b98250;
    --body-font: 'Instrument Sans', Arial, sans-serif;
    --serif-font: Georgia, 'Times New Roman', serif;
    color: #171b21;
    font-family: var(--body-font);
    background-color: #ffffff;
}

.gdi-home *, .gdi-home *::before, .gdi-home *::after { box-sizing: border-box; }
.gdi-home h1, .gdi-home h2, .gdi-home h3, .gdi-home p { margin-top: 0; }
.gdi-home a { text-decoration: none; }
.home-container { width: min(100% - 96px, 1240px); margin-inline: auto; }
.section-eyebrow { margin-bottom: 22px; color: #58677a; font-size: 12px; font-weight: 400; letter-spacing: .055em; text-transform: uppercase; }
.section-title { max-width: 980px; margin-bottom: 22px; font: 400 clamp(33px, 5vw, 48px)/1.15 var(--serif-font); letter-spacing: -.025em; }
.section-intro { margin-bottom: 28px; color: #666a70; font-size: clamp(15px, 1.6vw, 18px); line-height: 1.4; }

/* 1. PORTADA */
.home-hero {
    position: relative;
    display: flex;
    align-items: center;
    min-height: 760px;
    isolation: isolate;
    overflow: hidden;
    color: #ffffff;
    background: linear-gradient(120deg, #304558 0%, #1b2938 70%, #101b27 100%);
}
.hero-picture, .hero-picture img, .hero-shade { position: absolute; inset: 0; width: 100%; height: 100%; }
.hero-picture { z-index: -2; }
.hero-picture img { display: block; object-fit: cover; object-position: center; }
.hero-shade { z-index: -1; background: linear-gradient(90deg, rgba(31,46,63,.55) 0%, rgba(30,44,59,.40) 47%, rgba(18,30,41,.16) 100%); }
.hero-content { padding-top: 118px; padding-bottom: 110px; }
.hero-title { max-width: 830px; margin-bottom: 30px; font: 400 clamp(42px, 5.2vw, 66px)/1.05 var(--serif-font); letter-spacing: -.027em; }
.hero-title span { color: #c8915d; }
.hero-description { max-width: 690px; margin-bottom: 0; font-size: clamp(15px, 1.55vw, 18px); line-height: 1.4; }
.hero-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; margin-top: 68px; }
.hero-button { display: inline-flex; min-width: 164px; min-height: 48px; align-items: center; justify-content: center; gap: 12px; border: 1px solid transparent; border-radius: 999px; padding: 12px 24px; font-size: 13px; transition: background .2s ease, border-color .2s ease, color .2s ease; }
.hero-button span, .platform-button span { font-size: 18px; line-height: 1; }
.hero-button-primary { background: #ffffff; color: #1f2937; }
.hero-button-primary:hover { background: #e8e9eb; }
.hero-button-secondary { border-color: rgba(255,255,255,.45); background: rgba(255,255,255,.025); color: white; }
.hero-button-secondary:hover { background: rgba(255,255,255,.12); }

/* 2. INFRAESTRUCTURA */
.home-infrastructure { padding: 66px 0 82px; background: #fff; }
.infra-title { max-width: 680px; color: var(--ink); }
.infrastructure-diagram { padding: 72px 72px 62px; color: #fff; background: #2a374a; }
.diagram-columns { display: grid; grid-template-columns: 1fr 1.45fr 1fr; min-height: 300px; align-items: start; }
.diagram-side { padding-top: 74px; text-align: center; }
.diagram-side h3 { margin: 0 0 16px; border-bottom: 1px solid rgba(255,255,255,.5); padding-bottom: 13px; font: 400 17px/1.2 var(--serif-font); letter-spacing: .11em; }
.diagram-side ul { display: grid; gap: 20px; margin: 0; padding: 0 8px; list-style: none; }
.diagram-side li { font-size: 13px; line-height: 1.25; font-weight: 600; }
.diagram-center { min-height: 305px; border-inline: 1px solid rgba(255,255,255,.6); text-align: center; }
.diagram-brand { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 116px; }
.brand-logo { width: min(100%, 230px); }
.diagram-center h3 { min-height: 64px; margin: 0; border-bottom: 1px solid rgba(255,255,255,.5); padding: 13px 12px; color: #c58f60; font: 400 18px/1.2 var(--serif-font); }
.diagram-center ul { display: grid; gap: 13px; margin: 0; padding: 15px 8px 0; list-style: none; }
.diagram-center li { font: 400 17px/1.2 var(--serif-font); }
.diagram-footer { margin-top: 32px; border-top: 1px solid rgba(255,255,255,.5); padding-top: 26px; text-align: center; }
.diagram-footer-title { margin-bottom: 3px; font-size: 13px; font-weight: 650; }
.diagram-footer-copy { max-width: 900px; margin: 0 auto; color: #c5cad0; font-size: 12px; line-height: 1.45; }

/* 3. MODELO OPERATIVO */
.home-operating { position: relative; isolation: isolate; overflow: hidden; padding: 75px 0 83px; color: #fff; background: #ba8656; }
.home-operating::before { position: absolute; z-index: -1; top: 0; bottom: 0; left: -208px; width: 748px; background: url('/images/gdi/compartido/textura.svg') center / 100% 100% no-repeat; opacity: .5; mix-blend-mode: multiply; content: ''; pointer-events: none; }

.light-eyebrow { color: #fff; }
.operating-title { max-width: 1100px; }
.operating-intro { max-width: 1100px; color: #fff; }
.operating-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 30px; margin-top: 32px; }
.operating-card { min-height: 340px; padding: 30px 32px; color: #253246; background: #fff; }
.operating-number { margin-bottom: 4px; font-size: 19px; }
.operating-card h3 { max-width: 240px; margin-bottom: 20px; font: 400 26px/1.12 var(--serif-font); }
.operating-card > p:last-child { margin: 0; color: #656565; font-size: 14px; line-height: 1.42; }

/* 4. ACCESO DIFERENCIADO */
.home-audiences { padding: 84px 0 94px; background: #fff; }
.audiences-title { max-width: 1100px; color: #171717; }
.audiences-intro { max-width: 1100px; }
.audiences-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 32px; margin-top: 28px; }
.audience-card { position: relative; display: flex; align-items: flex-end; min-height: 420px; isolation: isolate; overflow: hidden; color: white; background: #0b1f32; }
.audience-background, .audience-shade { position: absolute; inset: 0; width: 100%; height: 100%; }
.audience-background { z-index: -2; object-fit: cover; }
.audience-shade { z-index: -1; background: linear-gradient(180deg, rgba(9,28,48,.10) 0%, rgba(9,28,48,.22) 45%, rgba(4,15,29,.43) 100%); }
.audience-content { display: flex; width: 100%; flex-direction: column; align-items: flex-start; padding: 28px 30px; }
.audience-accent { display: block; width: 48px; height: 3px; margin-bottom: 15px; background: linear-gradient(90deg, #b68150, rgba(182,129,80,0)); }
.audience-content h3 { margin-bottom: 15px; font: 400 32px/1.1 var(--serif-font); }
.audience-content p { margin-bottom: 14px; color: #e3e8ee; font-size: 13px; line-height: 1.5; }
.audience-link { display: inline-flex; align-items: center; gap: 10px; margin-top: 18px; border-bottom: 1px solid rgba(255,255,255,.6); padding-bottom: 6px; color: #fff; font-size: 13px; font-weight: 600; }
.audience-link:hover { color: #e4bd97; border-color: #e4bd97; }

/* 5. ACCESO A LA PLATAFORMA */
.home-platform { position: relative; isolation: isolate; overflow: hidden; display: flex; align-items: center; min-height: 564px; padding: 66px 0 90px; background: #021226; color: #fff; text-align: left; }
.platform-background { position: absolute; z-index: -1; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; }
.platform-content { position: relative; display: flex; flex-direction: column; align-items: flex-start; }
.platform-eyebrow { margin-bottom: 72px; padding: 0; color: #bcc7d2; font-size: 11px; letter-spacing: .08em; text-transform: uppercase; }
.platform-title { max-width: 760px; margin-bottom: 20px; }
.platform-description { max-width: 930px; margin-bottom: 42px; color: #f2f5f8; font-size: 16px; line-height: 1.45; }
.platform-button { display: inline-flex; align-items: center; justify-content: center; gap: 12px; min-width: 215px; min-height: 48px; border-radius: 999px; padding: 12px 22px; color: #242b35; background: #fff; font-size: 13px; transition: background .2s ease; }
.platform-button:hover { background: #e7e9ed; }

/* TABLET */
@media (max-width: 1023px) {
    .home-container { width: min(100% - 48px, 1240px); }
    .infrastructure-diagram { padding: 44px 34px; }
    .operating-grid { gap: 15px; }
    .operating-card { padding: 25px 22px; }
    .audiences-grid { gap: 18px; }
    .audience-content { padding: 24px; }
}

/* MÓVIL — diseño mobile-first en distribución y espaciado */
@media (max-width: 767px) {
    .home-container { width: min(100% - 36px, 1240px); }
    .section-eyebrow { margin-bottom: 16px; font-size: 11px; }
    .section-title { margin-bottom: 18px; font-size: clamp(31px, 8vw, 39px); line-height: 1.16; }
    .section-intro { margin-bottom: 26px; font-size: 15px; line-height: 1.55; }

    .home-hero { min-height: 650px; align-items: flex-end; }
    .hero-picture img { object-position: center; }
    .hero-shade { background: linear-gradient(180deg, rgba(30,44,59,.22) 0%, rgba(29,43,58,.45) 38%, rgba(24,36,49,.84) 100%); }
    .hero-content { padding-top: 110px; padding-bottom: 54px; }
    .hero-title { max-width: 600px; margin-bottom: 24px; font-size: clamp(38px, 9vw, 52px); line-height: 1.07; }
    .hero-description { font-size: 15px; line-height: 1.5; }
    .hero-actions { align-items: stretch; flex-direction: column; gap: 12px; margin-top: 28px; }
    .hero-button { width: 100%; min-height: 50px; }

    .home-infrastructure { padding: 58px 0 65px; }
    .infrastructure-diagram { padding: 30px 22px; }
    .diagram-columns { grid-template-columns: 1fr; gap: 25px; }
    .diagram-side { padding-top: 0; }
    .diagram-side h3 { margin-bottom: 14px; }
    .diagram-side ul { gap: 12px; }
    .diagram-center { min-height: auto; border-inline: none; border-block: 1px solid rgba(255,255,255,.45); padding-block: 20px; }
    .diagram-brand { min-height: 98px; }
    .diagram-center h3 { min-height: auto; padding-block: 12px; }
    .diagram-center ul { gap: 11px; }
    .diagram-footer { margin-top: 26px; padding-top: 20px; }

    .home-operating { padding: 60px 0 66px; }
    .operating-grid { grid-template-columns: 1fr; gap: 16px; margin-top: 24px; }
    .operating-card { min-height: auto; padding: 26px; }
    .operating-card h3 { max-width: none; margin-bottom: 16px; }

    .home-audiences { padding: 62px 0 68px; }
    .audiences-grid { grid-template-columns: 1fr; gap: 20px; }
    .audience-card { min-height: 400px; }
    .audience-content { padding: 24px; }
    .audience-content h3 { font-size: 30px; }

    .home-platform { min-height: 500px; padding: 76px 0 84px; }
    .platform-background { object-position: 57% center; }
    .platform-eyebrow { margin-bottom: 50px; }
    .platform-title { margin-bottom: 22px; }
    .platform-description { font-size: 15px; line-height: 1.55; }
    .platform-button { width: min(100%, 300px); }
}

@media (prefers-reduced-motion: reduce) {
    .gdi-home a { transition: none; }
}
</style>
