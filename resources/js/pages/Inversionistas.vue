<script setup>
import { computed } from 'vue';
import { imageUrl, list } from '../utils/imageUrl';
import NextStepBanner from '../components/NextStepBanner.vue';

const props = defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
});

const hero = computed(() => props.content.hero ?? {});
const value = computed(() => props.content.value ?? {});
const strategies = computed(() => props.content.strategies ?? {});
const process = computed(() => props.content.process ?? {});
const governance = computed(() => props.content.governance ?? {});
const reporting = computed(() => props.content.reporting ?? {});
const nextStep = computed(() => props.content.next_step ?? {});

const valueCards = computed(() => list(value.value.cards));
const processSteps = computed(() => list(process.value.steps));
const governanceCards = computed(() => list(governance.value.cards));
const reportItems = computed(() => list(reporting.value.items));

const STATIC_BASE = '/images/gdi/inversionistas';
const governanceIcons = ['icono-comite.svg', 'icono-auditoria.svg', 'icono-riesgos.svg', 'icono-cumplimiento.svg'];

const heroDesktop = computed(() => imageUrl(hero.value.image_desktop || hero.value.image_mobile, `${STATIC_BASE}/hero.jpg`));
const heroMobile = computed(() => imageUrl(hero.value.image_mobile));
const strategiesImage = computed(() => imageUrl(strategies.value.background_image, `${STATIC_BASE}/estrategias.png`));
const reportingImage = computed(() => imageUrl(reporting.value.background_image, `${STATIC_BASE}/reporteria.png`));

function governanceIcon(card, index) {
    return imageUrl(card.icon, `${STATIC_BASE}/${governanceIcons[index % governanceIcons.length]}`);
}
</script>

<template>
    <main class="gdi-investors">
        <!-- 1. PORTADA -->
        <section id="inversionistas-portada" class="investors-hero" aria-labelledby="investors-hero-title">
            <picture class="investors-hero-picture">
                <source v-if="heroMobile" media="(max-width: 767px)" :srcset="heroMobile" />
                <img :src="heroDesktop" alt="" fetchpriority="high" />
            </picture>
            <div class="investors-hero-shade" aria-hidden="true"></div>
            <div class="investors-container investors-hero-copy">
                <p v-if="hero.eyebrow" class="investors-tag">{{ hero.eyebrow }}</p>
                <h1 id="investors-hero-title">{{ hero.title }}</h1>
                <p v-if="hero.description" class="investors-hero-description">{{ hero.description }}</p>
            </div>
        </section>

        <!-- 2. VALOR INSTITUCIONAL -->
        <section id="inversionistas-valor" class="investors-value" aria-labelledby="investors-value-title">
            <div class="investors-container">
                <p v-if="value.eyebrow" class="investors-eyebrow">{{ value.eyebrow }}</p>
                <h2 id="investors-value-title" class="investors-title">{{ value.title }}</h2>
                <p v-if="value.description" class="investors-intro">{{ value.description }}</p>
                <div class="value-grid">
                    <article v-for="(card, index) in valueCards" :key="`value-${index}`" class="value-card">
                        <h3>{{ card.title }}</h3>
                        <p v-if="card.description">{{ card.description }}</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- 3. ESTRATEGIAS -->
        <section id="inversionistas-estrategias" class="investors-strategies" aria-labelledby="investors-strategies-title">
            <img class="strategies-photo" :src="strategiesImage" alt="" loading="lazy" />
            <div class="strategies-shade" aria-hidden="true"></div>
            <div class="investors-container strategies-copy">
                <p v-if="strategies.eyebrow" class="investors-eyebrow strategies-eyebrow">{{ strategies.eyebrow }}</p>
                <p v-if="strategies.kicker" class="strategies-kicker">{{ strategies.kicker }}</p>
                <h2 id="investors-strategies-title">{{ strategies.title }}</h2>
                <a v-if="strategies.button_text && strategies.button_url" class="investors-outline-link" :href="strategies.button_url">
                    {{ strategies.button_text }}
                    <img :src="`${STATIC_BASE}/flecha-enlace.svg`" alt="" width="14" height="14" />
                </a>
            </div>
        </section>

        <!-- 4. PROCESO DE INVERSIÓN -->
        <section id="inversionistas-proceso" class="investors-process" aria-labelledby="investors-process-title">
            <div class="investors-container process-heading">
                <p v-if="process.eyebrow" class="investors-eyebrow">{{ process.eyebrow }}</p>
                <h2 id="investors-process-title" class="investors-title">{{ process.title }}</h2>
                <p v-if="process.description" class="investors-intro">{{ process.description }}</p>
            </div>
            <ol class="process-list">
                <li
                    v-for="(step, index) in processSteps"
                    :key="`process-${index}`"
                    class="process-row"
                    :class="{ 'process-row-light': index % 2 === 1 }"
                >
                    <div class="investors-container process-row-inner">
                        <div class="process-name">
                            <span class="process-number">{{ step.number }}</span>
                            <h3>{{ step.title }}</h3>
                        </div>
                        <p v-if="step.description">{{ step.description }}</p>
                    </div>
                </li>
            </ol>
        </section>

        <!-- 5. GOBIERNO Y CONTROL -->
        <section id="inversionistas-gobierno" class="investors-governance" aria-labelledby="investors-governance-title">
            <div class="investors-container">
                <p v-if="governance.eyebrow" class="investors-eyebrow">{{ governance.eyebrow }}</p>
                <h2 id="investors-governance-title" class="investors-title">{{ governance.title }}</h2>
                <p v-if="governance.description" class="investors-intro">{{ governance.description }}</p>
                <div class="governance-grid">
                    <article v-for="(card, index) in governanceCards" :key="`governance-${index}`" class="governance-card">
                        <img :src="governanceIcon(card, index)" alt="" width="48" height="48" loading="lazy" />
                        <h3>{{ card.title }}</h3>
                        <p v-if="card.description">{{ card.description }}</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- 6. REPORTERÍA -->
        <section id="inversionistas-reporteria" class="investors-reporting" aria-labelledby="investors-reporting-title">
            <img class="reporting-photo" :src="reportingImage" alt="" loading="lazy" />
            <div class="investors-container reporting-grid">
                <div class="reporting-copy">
                    <p v-if="reporting.eyebrow" class="investors-eyebrow">{{ reporting.eyebrow }}</p>
                    <h2 id="investors-reporting-title" class="investors-title">{{ reporting.title }}</h2>
                    <p v-if="reporting.paragraph_one">{{ reporting.paragraph_one }}</p>
                    <p v-if="reporting.paragraph_two">{{ reporting.paragraph_two }}</p>
                </div>
                <div v-if="reportItems.length" class="reporting-box">
                    <h3 v-if="reporting.list_title">{{ reporting.list_title }}</h3>
                    <ul>
                        <li v-for="(item, index) in reportItems" :key="`report-${index}`">{{ item.text }}</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- 7. SIGUIENTE PASO -->
        <NextStepBanner :content="nextStep" section-id="inversionistas-siguiente-paso" />
    </main>
</template>

<style scoped>
/* Mobile-first. Todo el frontend de Inversionistas permanece en este archivo. */
.gdi-investors,
.gdi-investors * { box-sizing: border-box; }
.gdi-investors { --navy: #2a374a; --bronze: #b68150; color: var(--navy); font-family: 'Instrument Sans', Arial, sans-serif; overflow: clip; }
.investors-container { width: min(100% - 40px, 1200px); margin-inline: auto; }
.investors-eyebrow { margin: 0 0 20px; font-size: 11px; line-height: 1.5; letter-spacing: .06em; text-transform: uppercase; }
.investors-title { margin: 0; font: 400 clamp(31px, 6vw, 48px)/1.12 Georgia, 'Times New Roman', serif; letter-spacing: -.025em; }
.investors-intro { max-width: 760px; margin: 18px 0 0; font-size: 14px; line-height: 1.65; }

.investors-outline-link { display: inline-flex; align-items: center; gap: 12px; width: fit-content; min-height: 44px; padding: 12px 26px; border: 1px solid #fff; border-radius: 999px; color: #fff; font-size: 11px; letter-spacing: .14em; text-transform: uppercase; text-decoration: none; transition: background-color .2s ease; }
.investors-outline-link:hover { background: rgba(255,255,255,.12); }

/* 1. Portada */
.investors-hero { position: relative; display: flex; align-items: center; min-height: 460px; isolation: isolate; background: var(--navy); color: #fff; }
.investors-hero-picture,
.investors-hero-shade { position: absolute; inset: 0; width: 100%; height: 100%; }
.investors-hero-picture { z-index: -2; }
.investors-hero-picture img { width: 100%; height: 100%; object-fit: cover; }
.investors-hero-shade { z-index: -1; background: linear-gradient(147deg, rgba(182,129,80,.9) 0%, rgba(182,129,80,.85) 50%, #684932 100%); }
.investors-hero-copy { padding-block: 90px; }
.investors-tag { display: inline-block; margin: 0 0 26px; padding: 7px 16px; border-radius: 20px; background: #fff; color: var(--navy); font-size: 11px; line-height: 1.2; letter-spacing: .12em; text-transform: uppercase; }
.investors-hero h1 { max-width: 900px; margin: 0; font: 400 clamp(36px, 7.5vw, 66px)/1.06 Georgia, 'Times New Roman', serif; letter-spacing: -.012em; }
.investors-hero-description { max-width: 820px; margin: 24px 0 0; font-size: 14px; line-height: 1.65; }

/* 2. Valor institucional */
.investors-value { padding-block: 70px 80px; background: var(--navy); color: #fff; }
.value-grid { display: grid; gap: 20px; margin-top: 40px; }
.value-card { min-height: 190px; padding: 32px 30px; background: #fff; color: var(--navy); }
.value-card h3 { margin: 0 0 16px; font: 400 clamp(23px, 4vw, 28px)/1.2 Georgia, 'Times New Roman', serif; letter-spacing: -.02em; }
.value-card p { max-width: 420px; margin: 0; font-size: 14px; line-height: 1.65; }

/* 3. Estrategias */
.investors-strategies { position: relative; display: flex; align-items: center; min-height: 460px; isolation: isolate; overflow: hidden; background: var(--navy); color: #fff; }
.strategies-photo,
.strategies-shade { position: absolute; inset: 0; width: 100%; height: 100%; }
.strategies-photo { z-index: -2; object-fit: cover; transform: scaleX(-1); }
.strategies-shade { z-index: -1; background: linear-gradient(90deg, rgba(23,33,48,.55), rgba(23,33,48,.15)); }
.strategies-copy { padding-block: 80px; }
.strategies-eyebrow { letter-spacing: .1em; }
.strategies-kicker { margin: 0 0 30px; color: rgba(255,255,255,.75); font-size: 11px; line-height: 1.5; letter-spacing: .18em; text-transform: uppercase; }
.investors-strategies h2 { max-width: 560px; margin: 0 0 36px; font: 400 clamp(32px, 6vw, 50px)/1.1 Georgia, 'Times New Roman', serif; letter-spacing: -.02em; }

/* 4. Proceso de inversión */
.investors-process { padding-top: 60px; background: #fff; }
.process-heading { padding-bottom: 40px; }
.process-list { margin: 0; padding: 0; list-style: none; }
.process-row { border-top: 2px solid rgba(42,55,74,.13); background: var(--bronze); color: #fff; }
.process-row-light { background: #fff; color: var(--navy); }
.process-row-inner { display: grid; gap: 20px; padding-block: 36px; }
.process-name { display: flex; align-items: center; gap: 20px; }
.process-number { display: grid; place-items: center; flex: 0 0 64px; width: 64px; height: 64px; border-radius: 50%; background: #fff; color: var(--bronze); font: 400 24px/1 Georgia, 'Times New Roman', serif; }
.process-row-light .process-number { background: var(--bronze); color: #fff; }
.process-name h3 { margin: 0; font: 400 clamp(22px, 4.5vw, 28px)/1.2 Georgia, 'Times New Roman', serif; }
.process-row-inner > p { max-width: 640px; margin: 0; font-size: 14px; line-height: 1.65; }

/* 5. Gobierno y control */
.investors-governance { padding-block: 70px 80px; background: #fff; }
.governance-grid { display: grid; gap: 20px; margin-top: 36px; }
.governance-card { padding: 34px 32px 38px; background: var(--navy); color: #fff; }
.governance-card img { display: block; width: 48px; height: 48px; }
.governance-card h3 { margin: 22px 0 14px; color: var(--bronze); font: 400 20px/1.35 Georgia, 'Times New Roman', serif; }
.governance-card p { margin: 0; font-size: 13px; line-height: 1.6; }

/* 6. Reportería */
.investors-reporting { position: relative; isolation: isolate; overflow: hidden; padding-block: 70px; background: var(--navy); color: #fff; }
.reporting-photo { position: absolute; z-index: -1; inset: 0; width: 100%; height: 100%; object-fit: cover; }
.reporting-grid { display: grid; gap: 40px; align-items: center; }
.reporting-copy p:not(.investors-eyebrow) { max-width: 500px; margin: 22px 0 0; font-size: 14px; line-height: 1.65; }
.reporting-box { padding: 40px 32px; background: rgba(0,0,0,.15); }
.reporting-box h3 { max-width: 260px; margin: 0 0 30px; font: 400 clamp(32px, 6vw, 48px)/1.05 Georgia, 'Times New Roman', serif; }
.reporting-box ul { display: grid; gap: 14px; margin: 0; padding: 0; list-style: none; }
.reporting-box li { position: relative; padding-left: 18px; font-size: 14px; line-height: 1.5; }
.reporting-box li::before { content: ''; position: absolute; left: 0; top: .55em; width: 6px; height: 6px; border-radius: 50%; background: #fff; }


@media (min-width: 640px) {
    .investors-container { width: min(100% - 64px, 1200px); }
    .value-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 28px; }
    .governance-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (min-width: 768px) {
    .investors-hero { min-height: 600px; }
    .investors-hero-copy { padding-block: 140px 110px; }
    .investors-value { padding-block: 90px 110px; }
    .value-card { padding: 46px 50px; min-height: 240px; }
    .investors-strategies { min-height: 560px; }
    .investors-process { padding-top: 80px; }
    .process-row-inner { grid-template-columns: minmax(0, 4fr) minmax(0, 8fr); gap: 32px; align-items: center; padding-block: 40px; }
    .investors-governance { padding-block: 90px 110px; }
    .investors-reporting { padding-block: 100px; }
    .reporting-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 80px; }
    .reporting-box { padding: 50px 52px; }
}
@media (min-width: 1100px) {
    .investors-container { width: min(100% - 96px, 1200px); }
    .governance-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 32px; }
    .governance-card { padding: 40px; min-height: 330px; }
}
@media (prefers-reduced-motion: reduce) {
    .gdi-investors * { transition: none !important; }
}
</style>
