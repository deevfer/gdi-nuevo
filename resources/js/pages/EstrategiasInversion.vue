<script setup>
import { computed } from 'vue';
import { imageUrl, list } from '../utils/imageUrl';
import FundChart from '../components/FundChart.vue';
import NextStepBanner from '../components/NextStepBanner.vue';

const props = defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
});

const hero = computed(() => props.content.hero ?? {});
const decisions = computed(() => props.content.decisions ?? {});
const approach = computed(() => props.content.approach ?? {});
const fundsIntro = computed(() => props.content.funds_intro ?? {});
const funds = computed(() => list(props.content.funds?.items));
const nextStep = computed(() => props.content.next_step ?? {});

const principles = computed(() => list(decisions.value.principles));
const approachCards = computed(() => list(approach.value.cards));

const STATIC_BASE = '/images/gdi/estrategias';
const SHARED_BASE = '/images/gdi/compartido';
const approachIcons = ['icono-preservacion.svg', 'icono-liquidez.svg', 'icono-analisis.svg', 'icono-seguimiento.svg'];

const heroDesktop = computed(() => imageUrl(hero.value.image_desktop || hero.value.image_mobile, `${STATIC_BASE}/hero.png`));
const heroMobile = computed(() => imageUrl(hero.value.image_mobile));

function approachIcon(card, index) {
    return imageUrl(card.icon, `${STATIC_BASE}/${approachIcons[index % approachIcons.length]}`);
}

function fundId(fund, index) {
    const slug = String(fund.code || fund.name || index)
        .toLowerCase()
        .normalize('NFD')
        .replace(/[̀-ͯ]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');
    return `fondo-${slug || index}`;
}
</script>

<template>
    <main class="gdi-strategies">
        <!-- 1. PORTADA -->
        <section id="estrategias-portada" class="strategies-hero" aria-labelledby="strategies-hero-title">
            <picture class="strategies-hero-picture">
                <source v-if="heroMobile" media="(max-width: 767px)" :srcset="heroMobile" />
                <img :src="heroDesktop" alt="" fetchpriority="high" />
            </picture>
            <div class="strategies-container strategies-hero-copy">
                <p v-if="hero.eyebrow" class="strategies-tag">{{ hero.eyebrow }}</p>
                <h1 id="strategies-hero-title">{{ hero.title }}</h1>
                <p v-if="hero.description" class="strategies-hero-description">{{ hero.description }}</p>
            </div>
        </section>

        <!-- 2. TOMA DE DECISIONES -->
        <section id="estrategias-decisiones" class="strategies-decisions" aria-labelledby="strategies-decisions-title">
            <div class="strategies-container decisions-grid">
                <div>
                    <p v-if="decisions.eyebrow" class="strategies-eyebrow">{{ decisions.eyebrow }}</p>
                    <h2 id="strategies-decisions-title" class="strategies-title">{{ decisions.title }}</h2>
                    <p v-if="decisions.description" class="strategies-intro">{{ decisions.description }}</p>
                </div>
                <div class="principles">
                    <article v-for="(item, index) in principles" :key="`principle-${index}`" class="principle-card">
                        <h3>{{ item.title }}</h3>
                        <p v-if="item.description">{{ item.description }}</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- 3. ENFOQUE DE INVERSIÓN -->
        <section id="estrategias-enfoque" class="strategies-approach" aria-labelledby="strategies-approach-title">
            <div class="strategies-container">
                <p v-if="approach.eyebrow" class="strategies-eyebrow">{{ approach.eyebrow }}</p>
                <h2 id="strategies-approach-title" class="strategies-title">{{ approach.title }}</h2>
                <div class="approach-grid">
                    <article
                        v-for="(card, index) in approachCards"
                        :key="`approach-${index}`"
                        class="approach-card"
                        :class="{ 'approach-card-light': index % 2 === 1 }"
                    >
                        <img :src="approachIcon(card, index)" alt="" width="48" height="48" loading="lazy" />
                        <h3>{{ card.title }}</h3>
                        <p v-if="card.description">{{ card.description }}</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- 4. INTRODUCCIÓN DE FONDOS -->
        <section id="estrategias-fondos" class="strategies-funds-intro" aria-labelledby="strategies-funds-title">
            <div class="strategies-container">
                <p v-if="fundsIntro.eyebrow" class="strategies-eyebrow">{{ fundsIntro.eyebrow }}</p>
                <h2 id="strategies-funds-title" class="strategies-title">{{ fundsIntro.title }}</h2>
                <p v-if="fundsIntro.description" class="strategies-intro">{{ fundsIntro.description }}</p>
            </div>
        </section>

        <!-- 5. FONDOS -->
        <section
            v-for="(fund, fundIndex) in funds"
            :id="fundId(fund, fundIndex)"
            :key="`fund-${fundIndex}`"
            class="fund"
            :aria-labelledby="`${fundId(fund, fundIndex)}-title`"
        >
            <div class="strategies-container">
                <p v-if="fund.code" class="fund-code">{{ fund.code }}</p>

                <div class="fund-overview">
                    <div class="fund-copy">
                        <h2 :id="`${fundId(fund, fundIndex)}-title`" class="fund-name">{{ fund.name }}</h2>
                        <p v-if="fund.subtitle" class="fund-subtitle">{{ fund.subtitle }}</p>
                        <p v-if="fund.paragraph_one">{{ fund.paragraph_one }}</p>
                        <p v-if="fund.paragraph_two">{{ fund.paragraph_two }}</p>
                    </div>
                    <aside v-if="list(fund.features).length" class="fund-features" :aria-label="fund.features_title || 'Características'">
                        <h3 v-if="fund.features_title">{{ fund.features_title }}</h3>
                        <dl>
                            <div
                                v-for="(feature, index) in list(fund.features)"
                                :key="`feature-${index}`"
                                :class="{ 'feature-highlight': feature.highlight }"
                            >
                                <dt>{{ feature.label }}</dt>
                                <dd>{{ feature.value }}</dd>
                            </div>
                        </dl>
                    </aside>
                </div>

                <div v-if="fund.profile_title || fund.profile_text" class="fund-profile">
                    <h3 v-if="fund.profile_title">{{ fund.profile_title }}</h3>
                    <p v-if="fund.profile_text">{{ fund.profile_text }}</p>
                </div>

                <div class="fund-panel">
                    <h3 v-if="fund.performance_title" class="fund-panel-title">{{ fund.performance_title }}</h3>
                    <p v-if="fund.performance_subtitle" class="fund-panel-subtitle">{{ fund.performance_subtitle }}</p>

                    <table v-if="list(fund.returns).length" class="fund-returns">
                        <thead>
                            <tr>
                                <th scope="col">{{ fund.returns_period_label || 'Periodo' }}</th>
                                <th scope="col">{{ fund.returns_value_label || 'Retorno (%)' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in list(fund.returns)" :key="`return-${index}`">
                                <th scope="row">{{ row.period }}</th>
                                <td>{{ row.value }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <FundChart
                        class="fund-chart-block"
                        :points="list(fund.chart)"
                        :label="`${fund.performance_title || 'Performance'} · ${fund.name}`"
                    />

                    <p v-if="fund.disclaimer" class="fund-disclaimer">
                        <img :src="`${SHARED_BASE}/icono-info.svg`" alt="" width="20" height="20" />
                        <span>{{ fund.disclaimer }}</span>
                    </p>
                </div>

                <div v-if="list(fund.faqs).length" class="fund-panel fund-faq">
                    <h3 v-if="fund.faq_title" class="fund-panel-title">{{ fund.faq_title }}</h3>
                    <p v-if="fund.faq_subtitle" class="fund-panel-subtitle">{{ fund.faq_subtitle }}</p>
                    <div class="faq-list">
                        <details v-for="(faq, index) in list(fund.faqs)" :key="`faq-${index}`" class="faq-item">
                            <summary>
                                <span>{{ faq.question }}</span>
                                <img :src="`${SHARED_BASE}/chevron.svg`" alt="" width="20" height="20" />
                            </summary>
                            <p v-if="faq.answer">{{ faq.answer }}</p>
                        </details>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. SIGUIENTE PASO -->
        <NextStepBanner :content="nextStep" section-id="estrategias-siguiente-paso" />
    </main>
</template>

<style scoped>
/* Mobile-first. El frontend de Estrategias de inversión permanece en este archivo. */
.gdi-strategies,
.gdi-strategies * { box-sizing: border-box; }
.gdi-strategies { --navy: #2a374a; --bronze: #b68150; --mist: #f8f9fa; color: var(--navy); font-family: 'Instrument Sans', Arial, sans-serif; overflow: clip; }
.strategies-container { width: min(100% - 40px, 1200px); margin-inline: auto; }
.strategies-eyebrow { margin: 0 0 20px; font-size: 11px; line-height: 1.5; letter-spacing: .06em; text-transform: uppercase; }
.strategies-title { margin: 0; font: 400 clamp(31px, 6vw, 48px)/1.12 Georgia, 'Times New Roman', serif; letter-spacing: -.025em; }
.strategies-intro { max-width: 760px; margin: 22px 0 0; font-size: 14px; line-height: 1.7; }

/* 1. Portada */
.strategies-hero { position: relative; display: flex; align-items: center; min-height: 460px; isolation: isolate; background: var(--navy); color: #fff; }
.strategies-hero-picture { position: absolute; inset: 0; z-index: -1; }
.strategies-hero-picture img { width: 100%; height: 100%; object-fit: cover; }
.strategies-hero-copy { padding-block: 90px; }
.strategies-tag { display: inline-block; margin: 0 0 26px; padding: 7px 16px; border-radius: 999px; background: #fff; color: var(--navy); font-size: 11px; line-height: 1.2; letter-spacing: .12em; text-transform: uppercase; }
.strategies-hero h1 { max-width: 900px; margin: 0; font: 400 clamp(36px, 7.5vw, 66px)/1.06 Georgia, 'Times New Roman', serif; letter-spacing: -.012em; }
.strategies-hero-description { max-width: 770px; margin: 24px 0 0; font-size: 14px; line-height: 1.65; }

/* 2. Toma de decisiones */
.strategies-decisions { padding-block: 64px; background: var(--mist); }
.decisions-grid { display: grid; gap: 40px; align-items: center; }
.principles { display: grid; gap: 20px; }
.principle-card { padding: 30px 28px; border: 1px solid var(--bronze); background: #fff; }
.principle-card h3 { margin: 0 0 12px; font: 400 20px/1.5 Georgia, 'Times New Roman', serif; letter-spacing: -.03em; }
.principle-card p { margin: 0; font-size: 13px; line-height: 1.55; }

/* 3. Enfoque de inversión */
.strategies-approach { padding-block: 10px 80px; background: var(--mist); }
.approach-grid { display: grid; gap: 20px; margin-top: 44px; }
.approach-card { padding: 34px 32px 38px; background: var(--bronze); color: #fff; }
.approach-card-light { background: #fff; color: var(--navy); }
.approach-card img { display: block; width: 48px; height: 48px; }
.approach-card h3 { margin: 22px 0 14px; font: 400 20px/1.4 Georgia, 'Times New Roman', serif; }
.approach-card p { margin: 0; font-size: 13px; line-height: 1.65; }

/* 4. Introducción de fondos */
.strategies-funds-intro { padding-block: 90px; background: var(--navy); color: #fff; }

/* 5. Fondos */
.fund { padding-block: 40px 70px; background: #fff; }
.fund + .fund { padding-top: 10px; }
.fund-code { margin: 0 0 26px; font-size: 13px; letter-spacing: .12em; }
.fund-overview { display: grid; gap: 40px; align-items: center; }
.fund-name { margin: 0; font: 400 clamp(32px, 6vw, 48px)/1.2 Georgia, 'Times New Roman', serif; }
.fund-subtitle { margin: 24px 0 0; font: 400 26px/1.2 Georgia, 'Times New Roman', serif; }
.fund-copy p:not(.fund-subtitle) { max-width: 700px; margin: 20px 0 0; font-size: 14px; line-height: 1.75; }
.fund-features { padding: 30px 30px 20px; border: 1px solid var(--navy); background: #fff; }
.fund-features h3 { margin: 0 0 22px; font: 400 20px/1.4 Georgia, 'Times New Roman', serif; }
.fund-features dl { display: grid; gap: 14px; margin: 0; }
.fund-features dl > div { display: flex; justify-content: space-between; gap: 16px; padding-block: 12px; border-bottom: 1px solid rgba(42,55,74,.1); font-size: 13px; line-height: 1.5; }
.fund-features dl > div:last-child { border-bottom: 0; }
.fund-features .feature-highlight { padding-left: 16px; border-bottom-color: var(--navy); border-left: 4px solid var(--navy); }
.fund-features dt { font-weight: 300; }
.fund-features dd { margin: 0; text-align: right; font-weight: 500; }

.fund-profile { margin-top: 50px; padding: 40px 28px; background: var(--bronze); color: #fff; }
.fund-profile h3 { margin: 0 0 20px; font: 400 clamp(24px, 4vw, 28px)/1.15 Georgia, 'Times New Roman', serif; }
.fund-profile p { max-width: 975px; margin: 0; font-size: 14px; line-height: 1.7; }

.fund-panel { padding: 40px 24px; background: var(--navy); color: #fff; }
.fund-panel-title { margin: 0; color: var(--bronze); font: 400 clamp(24px, 4vw, 28px)/1.15 Georgia, 'Times New Roman', serif; }
.fund-panel-subtitle { margin: 12px 0 0; font-size: 14px; line-height: 1.5; }

.fund-returns { width: 100%; margin-top: 40px; border-collapse: collapse; font-size: 13px; }
.fund-returns th,
.fund-returns td { padding: 18px 0; border-bottom: 1px solid var(--bronze); font-weight: 400; text-align: left; }
.fund-returns th:last-child,
.fund-returns td { text-align: right; }
.fund-returns thead th { padding-block: 16px; }

.fund-chart-block { margin-top: 44px; }
.fund-disclaimer { display: flex; align-items: flex-start; gap: 12px; margin: 32px 0 0; padding: 20px; background: #fff; color: var(--navy); font-size: 12px; line-height: 1.75; }
.fund-disclaimer img { flex: 0 0 20px; margin-top: 2px; }

.fund-faq { padding-top: 0; }
.faq-list { margin-top: 36px; }
.faq-item { border-bottom: 1px solid var(--bronze); }
.faq-item:last-child { border-bottom: 0; }
.faq-item summary { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding-block: 22px; cursor: pointer; list-style: none; font: 500 15px/1.5 Georgia, 'Times New Roman', serif; }
.faq-item summary::-webkit-details-marker { display: none; }
.faq-item summary img { flex: 0 0 20px; transition: transform .2s ease; }
.faq-item[open] summary img { transform: rotate(180deg); }
.faq-item summary:focus-visible { outline: 2px solid var(--bronze); outline-offset: 4px; }
.faq-item p { max-width: 820px; margin: 0 0 24px; color: #d6dce6; font-size: 13px; line-height: 1.7; }

@media (min-width: 640px) {
    .strategies-container { width: min(100% - 64px, 1200px); }
    .approach-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (min-width: 768px) {
    .strategies-hero { min-height: 600px; }
    .strategies-hero-copy { padding-block: 150px 110px; }
    .strategies-decisions { padding-block: 80px 70px; }
    .principle-card { padding: 38px 42px; }
    .strategies-approach { padding-block: 10px 110px; }
    .strategies-funds-intro { padding-block: 110px; }
    .fund { padding-block: 40px 90px; }
    .fund-profile { padding: 48px 52px; }
    .fund-panel { padding: 48px; }
    .fund-faq { padding-top: 0; }
}
@media (min-width: 1000px) {
    .decisions-grid { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 96px; }
    .fund-overview { grid-template-columns: minmax(0, 2fr) minmax(300px, 1fr); gap: 48px; }
}
@media (min-width: 1100px) {
    .strategies-container { width: min(100% - 96px, 1200px); }
    .approach-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 32px; }
    .approach-card { min-height: 330px; padding: 40px; }
}
@media (prefers-reduced-motion: reduce) {
    .gdi-strategies * { transition: none !important; }
}
</style>
