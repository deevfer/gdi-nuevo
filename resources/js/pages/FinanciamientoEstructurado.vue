<script setup>
import { computed } from 'vue';
import NextStepBanner from '../components/NextStepBanner.vue';


const props = defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
});

const hero = computed(() => props.content.hero ?? {});
const problemSolution = computed(() => props.content.problem_solution ?? {});
const companies = computed(() => props.content.companies ?? {});
const process = computed(() => props.content.process ?? {});
const instruments = computed(() => props.content.instruments ?? {});
const targetProfile = computed(() => props.content.target_profile ?? {});
const nextStep = computed(() => props.content.next_step ?? {});

const processSteps = computed(() => Array.isArray(process.value.steps) ? process.value.steps : []);
const instrumentCards = computed(() => Array.isArray(instruments.value.cards) ? instruments.value.cards : []);
const profileCards = computed(() => Array.isArray(targetProfile.value.cards) ? targetProfile.value.cards : []);

const STATIC_BASE = '/images/gdi/financiamiento';
const instrumentIcons = ['emisiones-publicas.png', 'emisiones-privadas.png'];

// Filament guarda la ruta relativa dentro del disco public; los archivos
// exportados de Figma son fondos iniciales mientras el CMS no los sustituya.
function imageUrl(path, fallback = '') {
    if (!path || typeof path !== 'string' || !path.trim()) return fallback;
    const value = path.trim();
    if (/^https?:\/\//i.test(value) || value.startsWith('/')) return value;
    return `/storage/${value.replace(/^storage\//, '')}`;
}

const heroDesktop = computed(() => imageUrl(
    hero.value.image_desktop || hero.value.image_mobile,
    `${STATIC_BASE}/hero.png`,
));
const heroMobile = computed(() => imageUrl(hero.value.image_mobile));
const companiesImage = computed(() => imageUrl(
    companies.value.background_image,
    `${STATIC_BASE}/empresas.png`,
));
function instrumentIcon(card, index) {
    return imageUrl(card.icon, `${STATIC_BASE}/${instrumentIcons[index] ?? instrumentIcons[0]}`);
}
</script>

<template>
    <main class="gdi-financing">
        <!-- 1. PORTADA -->
        <section id="financiamiento-portada" class="financing-hero" aria-labelledby="financing-hero-title">
            <picture class="financing-hero-picture">
                <source v-if="heroMobile" media="(max-width: 767px)" :srcset="heroMobile" />
                <img :src="heroDesktop" alt="" fetchpriority="high" />
            </picture>
            <div class="financing-hero-shade" aria-hidden="true"></div>
            <div class="financing-container financing-hero-copy">
                <p v-if="hero.eyebrow" class="financing-hero-tag">{{ hero.eyebrow }}</p>
                <h1 id="financing-hero-title">{{ hero.title }}</h1>
                <p v-if="hero.description" class="financing-hero-description">{{ hero.description }}</p>
            </div>
        </section>

        <!-- 2. PROBLEMA / SOLUCIÓN -->
        <section id="financiamiento-problema-solucion" class="financing-problem-solution" aria-label="Problema y solución">
            <div class="financing-container problem-grid">
                <article class="problem-column">
                    <p v-if="problemSolution.problem_eyebrow" class="financing-eyebrow">{{ problemSolution.problem_eyebrow }}</p>
                    <h2>{{ problemSolution.problem_title }}</h2>
                    <p v-if="problemSolution.problem_paragraph_one">{{ problemSolution.problem_paragraph_one }}</p>
                    <p v-if="problemSolution.problem_paragraph_two">{{ problemSolution.problem_paragraph_two }}</p>
                </article>
                <article class="problem-column">
                    <p v-if="problemSolution.solution_eyebrow" class="financing-eyebrow">{{ problemSolution.solution_eyebrow }}</p>
                    <h2>{{ problemSolution.solution_title }}</h2>
                    <p v-if="problemSolution.solution_paragraph_one">{{ problemSolution.solution_paragraph_one }}</p>
                    <p v-if="problemSolution.solution_paragraph_two">{{ problemSolution.solution_paragraph_two }}</p>
                </article>
            </div>
        </section>

        <!-- 3. EMPRESAS -->
        <section id="financiamiento-empresas" class="financing-companies" aria-labelledby="financing-companies-title">
            <img class="companies-photo" :src="companiesImage" alt="" loading="lazy" />
            <div class="companies-shade" aria-hidden="true"></div>
            <div class="financing-container companies-copy">
                <p v-if="companies.eyebrow" class="financing-eyebrow light-eyebrow">{{ companies.eyebrow }}</p>
                <p v-if="companies.kicker" class="companies-kicker">{{ companies.kicker }}</p>
                <h2 id="financing-companies-title">{{ companies.title }}</h2>
                <a v-if="companies.button_text && companies.button_url" class="financing-link companies-link" :href="companies.button_url">
                    {{ companies.button_text }} <span aria-hidden="true">→</span>
                </a>
            </div>
        </section>

        <!-- 4. METODOLOGÍA -->
        <section id="financiamiento-metodologia" class="financing-process" aria-labelledby="financing-process-title">
            <div class="financing-container process-heading">
                <p v-if="process.eyebrow" class="financing-eyebrow">{{ process.eyebrow }}</p>
                <h2 id="financing-process-title" class="financing-section-title">{{ process.title }}</h2>
                <p v-if="process.description" class="financing-section-intro">{{ process.description }}</p>
            </div>
            <div class="process-steps">
                <article
                    v-for="(step, index) in processSteps"
                    :key="`financing-step-${index}`"
                    class="process-step"
                    :class="{ 'process-step-dark': index % 2 === 1 }"
                >
                    <div class="financing-container process-row">
                        <div class="process-name">
                            <span class="process-number">{{ step.number }}</span>
                            <h3>{{ step.title }}</h3>
                        </div>
                        <div class="process-details">
                            <p v-if="step.description">{{ step.description }}</p>
                            <div v-if="Array.isArray(step.items)" class="process-tags">
                                <span v-for="(item, itemIndex) in step.items" :key="`${index}-item-${itemIndex}`">
                                    {{ item.text }}
                                </span>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <!-- 5. TIPOS DE EMISIONES -->
        <section id="financiamiento-instrumentos" class="financing-instruments" aria-labelledby="financing-instruments-title">
            <div class="financing-container">
                <p v-if="instruments.eyebrow" class="financing-eyebrow">{{ instruments.eyebrow }}</p>
                <h2 id="financing-instruments-title" class="financing-section-title">{{ instruments.title }}</h2>
                <p v-if="instruments.description" class="financing-section-intro">{{ instruments.description }}</p>
                <div class="instrument-grid">
                    <article v-for="(card, index) in instrumentCards" :key="`instrument-${index}`" class="instrument-card">
                        <img :src="instrumentIcon(card, index)" alt="" loading="lazy" />
                        <h3>{{ card.title }}</h3>
                        <p v-if="card.description">{{ card.description }}</p>
                        <p v-if="card.secondary_description" class="instrument-detail">{{ card.secondary_description }}</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- 6. PERFIL OBJETIVO -->
        <section id="financiamiento-perfil" class="financing-target" aria-labelledby="financing-target-title">
            <div class="financing-container">
                <p v-if="targetProfile.eyebrow" class="financing-eyebrow">{{ targetProfile.eyebrow }}</p>
                <h2 id="financing-target-title" class="financing-section-title">{{ targetProfile.title }}</h2>
                <p v-if="targetProfile.description" class="financing-section-intro">{{ targetProfile.description }}</p>
                <div class="target-grid">
                    <article v-for="(card, index) in profileCards" :key="`profile-${index}`" class="target-card">
                        <h3>{{ card.title }}</h3>
                        <p>{{ card.description }}</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- 7. SIGUIENTE PASO -->
        <NextStepBanner :content="nextStep" section-id="financiamiento-siguiente-paso" />
    </main>
</template>

<style scoped>
/* Mobile-first. Todo el frontend de Financiamiento estructurado permanece en este archivo. */
.gdi-financing,
.gdi-financing * { box-sizing: border-box; }
.gdi-financing { color: #2b394d; font-family: 'Instrument Sans', Arial, sans-serif; overflow: clip; }
.financing-container { width: min(100% - 40px, 1200px); margin-inline: auto; }
.financing-eyebrow { margin: 0 0 22px; color: #8e98a3; font-size: 10px; line-height: 1.5; letter-spacing: .12em; text-transform: uppercase; }
.financing-section-title { margin: 0; color: #2b394d; font: 400 clamp(31px, 6vw, 48px)/1.1 Georgia, 'Times New Roman', serif; letter-spacing: -.025em; }
.financing-section-intro { max-width: 740px; margin: 16px 0 0; color: #6e7887; font-size: 14px; line-height: 1.65; }
.light-eyebrow { color: #e4d2c2; }
.financing-link { display: inline-flex; align-items: center; justify-content: center; gap: 10px; width: fit-content; min-height: 41px; padding: 10px 21px; border: 1px solid rgba(255,255,255,.8); border-radius: 999px; color: #fff; font-size: 11px; text-decoration: none; transition: background-color .2s ease, color .2s ease; }
.financing-link:hover { background: rgba(255,255,255,.12); }

/* 1. Hero */
.financing-hero { position: relative; min-height: 440px; display: flex; align-items: center; isolation: isolate; color: #fff; background: #172338; }
.financing-hero-picture,
.financing-hero-shade { position: absolute; inset: 0; width: 100%; height: 100%; }
.financing-hero-picture { z-index: -2; }
.financing-hero-picture img { width: 100%; height: 100%; object-fit: cover; object-position: 63% center; }
.financing-hero-shade { z-index: -1; background: linear-gradient(90deg, rgba(13, 24, 40, .45), rgba(13, 24, 40, .09)); }
.financing-hero-copy { padding-block: 80px; }
.financing-hero-tag { display: inline-block; margin: 0 0 24px; padding: 6px 15px; border-radius: 999px; background: #fff; color: #687686; font-size: 10px; line-height: 1.2; letter-spacing: .09em; }
.financing-hero h1 { max-width: 900px; margin: 0; font: 400 clamp(36px, 7.5vw, 68px)/1.08 Georgia, 'Times New Roman', serif; letter-spacing: -.025em; }
.financing-hero-description { max-width: 800px; margin: 20px 0 0; color: #d6dce6; font-size: 14px; line-height: 1.63; }

/* 2. Problema / solución */
.financing-problem-solution { padding-block: 70px; background: #fff; }
.problem-grid { display: grid; gap: 38px; }
.problem-column { position: relative; padding-left: 24px; }
/* Barra lateral corta en bronce que se desvanece hacia abajo, como en Figma. */
.problem-column::before { position: absolute; top: 0; left: 0; width: 4px; height: 96px; background: linear-gradient(180deg, #b68150, rgba(182,129,80,0)); content: ''; }
.problem-column h2 { margin: 0 0 22px; font: 400 clamp(30px, 5.5vw, 43px)/1.12 Georgia, 'Times New Roman', serif; }
.problem-column p:not(.financing-eyebrow) { max-width: 530px; margin: 15px 0 0; color: #6e7886; font-size: 14px; line-height: 1.7; }

/* 3. Empresas */
.financing-companies { position: relative; display: flex; align-items: center; min-height: 415px; isolation: isolate; overflow: hidden; background: #be926d; color: #fff; }
.companies-photo,
.companies-shade { position: absolute; inset: 0; height: 100%; width: 100%; }
.companies-photo { z-index: -2; object-fit: cover; object-position: center; }
.companies-shade { z-index: -1; background: linear-gradient(90deg, #b68150 0%, rgba(182,129,80,.86) 25%, rgba(182,129,80,.8) 50%, rgba(182,129,80,.7) 75%, rgba(182,129,80,.6) 100%); }
.companies-copy { padding-block: 68px; }
.companies-kicker { margin: 28px 0 23px; color: #f6e4d7; font-size: 11px; letter-spacing: .18em; text-transform: uppercase; }
.financing-companies h2 { max-width: 560px; margin: 0 0 30px; font: 400 clamp(34px, 6.8vw, 58px)/1.07 Georgia, 'Times New Roman', serif; letter-spacing: -.023em; }
.companies-link { text-transform: uppercase; font-size: 10px; letter-spacing: .1em; }

/* 4. Metodología */
.financing-process { background: #fdfdfd; }
.process-heading { padding-block: 60px 48px; }
.process-steps { display: grid; gap: 10px; background: #fff; }
.process-step { background: #b98555; color: #fff; }
.process-step-dark { background: #29384c; }
.process-row { display: grid; gap: 28px; padding-block: 44px; }
.process-name { display: flex; align-items: center; gap: 20px; }
.process-number { display: grid; place-items: center; flex: 0 0 56px; width: 56px; height: 56px; border-radius: 50%; background: #fff; color: #bc8959; font-size: 19px; }
.process-step-dark .process-number { color: #2b394d; }
.process-name h3 { margin: 0; color: #fff; font: 400 clamp(25px, 5vw, 34px)/1.12 Georgia, 'Times New Roman', serif; }
.process-details > p { max-width: 740px; margin: 0 0 20px; color: #f7e6d8; font-size: 13px; line-height: 1.6; }
.process-step-dark .process-details > p { color: #d5dde5; }
.process-tags { display: grid; gap: 10px; }
.process-tags span { display: flex; align-items: center; min-height: 58px; padding: 12px 16px; border-radius: 5px; background: #fff; color: #738091; font-size: 12px; line-height: 1.4; }

/* 5. Tipos de emisiones */
.financing-instruments { padding-block: 76px 85px; background: #fff; }
.instrument-grid { display: grid; gap: 20px; margin-top: 38px; }
.instrument-card { min-width: 0; padding: 34px 30px 38px; background: #29384c; color: #fff; }
.instrument-card img { width: 36px; height: 36px; margin-bottom: 19px; object-fit: contain; }
.instrument-card h3 { margin: 0 0 17px; color: #c08d59; font: 400 26px/1.15 Georgia, 'Times New Roman', serif; }
.instrument-card p { max-width: 420px; margin: 0 0 16px; color: #d8dfe8; font-size: 13px; line-height: 1.65; }
.instrument-card p:last-child { margin-bottom: 0; }
.instrument-card .instrument-detail { font-size: 12px; color: #bac5d3; }

/* 6. Perfil objetivo */
.financing-target { padding-block: 76px 80px; background: #f6f6f6; }
.target-grid { display: grid; gap: 16px; margin-top: 38px; }
.target-card { min-height: 165px; padding: 25px 24px; border: 1px solid #d1aa88; background: #fff; }
.target-card h3 { margin: 0 0 12px; color: #bb8959; font: 400 24px/1.18 Georgia, 'Times New Roman', serif; }
.target-card p { margin: 0; color: #737d89; font-size: 12px; line-height: 1.65; }


@media (min-width: 640px) {
    .financing-container { width: min(100% - 64px, 1200px); }
    .process-tags { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (min-width: 768px) {
    .financing-hero { min-height: 550px; }
    .financing-hero-copy { padding-block: 110px; }
    .financing-problem-solution { padding-block: 95px; }
    .problem-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8%; }
    .financing-companies { min-height: 515px; }
    .companies-copy { padding-block: 100px; }
    .process-heading { padding-block: 76px 65px; }
    .process-row { grid-template-columns: minmax(0, 240px) minmax(0, 1fr); gap: 50px; align-items: center; padding-block: 50px; }
    .process-name { display: block; }
    .process-number { margin-bottom: 18px; }
    .instrument-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 30px; }
    .target-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 26px; }
    .financing-instruments { padding-block: 100px 110px; }
    .financing-target { padding-block: 95px 110px; }
}
@media (min-width: 1100px) {
    .financing-container { width: min(100% - 92px, 1200px); }
    .financing-hero h1 { font-size: 66px; }
    .financing-companies h2 { font-size: 57px; }
}
@media (prefers-reduced-motion: reduce) {
    .gdi-financing * { transition: none !important; }
}
</style>
