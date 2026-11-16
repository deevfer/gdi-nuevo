<script setup>
import { computed } from 'vue';

// El header y el footer siguen siendo componentes independientes.
const props = defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
});

const hero = computed(() => props.content.hero ?? {});
const context = computed(() => props.content.context ?? {});
const advantages = computed(() => props.content.advantages ?? {});
const operation = computed(() => props.content.operation ?? {});
const standards = computed(() => props.content.standards ?? {});
const ecosystem = computed(() => props.content.ecosystem ?? {});
const nextStep = computed(() => props.content.next_step ?? {});

const advantageCards = computed(() =>
    Array.isArray(advantages.value.cards) ? advantages.value.cards : []
);
const operationSteps = computed(() =>
    Array.isArray(operation.value.steps) ? operation.value.steps : []
);
const standardItems = computed(() =>
    Array.isArray(standards.value.items) ? standards.value.items : []
);
const ecosystemComponents = computed(() =>
    Array.isArray(ecosystem.value.components) ? ecosystem.value.components : []
);

const STATIC_BASE = '/images/gdi/plataforma';
const advantageIcons = ['ventaja-1.png', 'ventaja-2.png', 'ventaja-3.png', 'ventaja-4.png'];
const standardIcons = [
    'estandar-regulacion.png',
    'estandar-gobierno.png',
    'estandar-riesgos.png',
    'estandar-procesos.png',
];

// Filament FileUpload guarda normalmente rutas relativas al disco public.
// Los recursos exportados de Figma son el fondo por defecto hasta que se
// reemplacen desde el administrador.
function imageUrl(path, fallback = '') {
    if (!path || typeof path !== 'string') return fallback;
    const value = path.trim();
    if (!value) return fallback;
    if (/^https?:\/\//i.test(value)) return value;
    if (value.startsWith('/')) return value;
    return `/storage/${value.replace(/^storage\//, '')}`;
}

const heroDesktop = computed(() =>
    imageUrl(hero.value.image_desktop || hero.value.image_mobile, `${STATIC_BASE}/hero.png`)
);
const heroMobile = computed(() => imageUrl(hero.value.image_mobile));
const contextImage = computed(() => imageUrl(context.value.image, `${STATIC_BASE}/contexto.png`));
const ecosystemImage = computed(() =>
    imageUrl(ecosystem.value.background_image, `${STATIC_BASE}/ecosistema.png`)
);
function advantageIcon(card, index) {
    return imageUrl(card.icon, `${STATIC_BASE}/${advantageIcons[index % advantageIcons.length]}`);
}
function standardIcon(item, index) {
    return imageUrl(item.icon, `${STATIC_BASE}/${standardIcons[index % standardIcons.length]}`);
}
</script>

<template>
    <main class="gdi-platform">
        <!-- 1. PORTADA -->
        <section id="plataforma-portada" class="platform-hero" aria-labelledby="platform-hero-title">
            <picture class="hero-picture">
                <source v-if="heroMobile" media="(max-width: 767px)" :srcset="heroMobile" />
                <img :src="heroDesktop" alt="" fetchpriority="high" />
            </picture>
            <div class="hero-shade" aria-hidden="true"></div>
            <div class="platform-container hero-content">
                <p v-if="hero.eyebrow" class="hero-eyebrow">{{ hero.eyebrow }}</p>
                <h1 id="platform-hero-title" class="hero-title">{{ hero.title }}</h1>
                <p v-if="hero.description" class="hero-description">{{ hero.description }}</p>
            </div>
        </section>

        <!-- 2. CONTEXTO -->
        <section id="plataforma-contexto" class="platform-context" aria-labelledby="platform-context-title">
            <div class="platform-container context-grid">
                <div class="context-copy">
                    <p v-if="context.eyebrow" class="section-eyebrow">{{ context.eyebrow }}</p>
                    <h2 id="platform-context-title" class="section-title context-title">{{ context.title }}</h2>
                    <p v-if="context.paragraph_one">{{ context.paragraph_one }}</p>
                    <p v-if="context.paragraph_two">{{ context.paragraph_two }}</p>
                </div>
                <div class="context-visual">
                    <img :src="contextImage" alt="Representación abstracta de una estructura integrada" loading="lazy" />
                </div>
            </div>
        </section>

        <div class="platform-dark-block">
            <!-- 3. VENTAJAS -->
            <section id="plataforma-ventajas" class="platform-advantages" aria-labelledby="platform-advantages-title">
                <div class="platform-container">
                    <p v-if="advantages.eyebrow" class="section-eyebrow eyebrow-light">{{ advantages.eyebrow }}</p>
                    <h2 id="platform-advantages-title" class="section-title light-title">{{ advantages.title }}</h2>
                    <p v-if="advantages.description" class="section-intro light-description">{{ advantages.description }}</p>

                    <div class="advantages-grid">
                        <article
                            v-for="(card, index) in advantageCards"
                            :key="`advantage-${index}`"
                            class="advantage-card"
                            :class="{ 'card-gold': index % 2 === 0, 'card-white': index % 2 !== 0 }"
                        >
                            <img :src="advantageIcon(card, index)" alt="" class="advantage-icon" loading="lazy" />
                            <h3>{{ card.title }}</h3>
                            <p>{{ card.description }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <!-- 4. FUNCIONAMIENTO -->
            <section id="plataforma-funcionamiento" class="platform-operation" aria-labelledby="platform-operation-title">
                <div class="platform-container">
                    <p v-if="operation.eyebrow" class="section-eyebrow eyebrow-light">{{ operation.eyebrow }}</p>
                    <h2 id="platform-operation-title" class="section-title light-title">{{ operation.title }}</h2>
                    <p v-if="operation.description" class="section-intro light-description">{{ operation.description }}</p>

                    <div class="operation-grid">
                        <template v-for="(step, index) in operationSteps" :key="`step-${index}`">
                            <article class="operation-card" :class="{ 'operation-highlight': index % 2 === 0 }">
                                <span class="operation-number">{{ step.number }}</span>
                                <h3>{{ step.title }}</h3>
                                <p>{{ step.description }}</p>
                            </article>
                            <span v-if="index < operationSteps.length - 1" class="operation-arrow" aria-hidden="true">➜</span>
                        </template>
                    </div>
                </div>
            </section>
        </div>

        <!-- 5. ESTÁNDARES -->
        <section id="plataforma-estandares" class="platform-standards" aria-labelledby="platform-standards-title">
            <div class="platform-container">
                <p v-if="standards.eyebrow" class="section-eyebrow">{{ standards.eyebrow }}</p>
                <h2 id="platform-standards-title" class="section-title">{{ standards.title }}</h2>
                <p v-if="standards.description" class="section-intro">{{ standards.description }}</p>

                <div class="standards-grid">
                    <article v-for="(item, index) in standardItems" :key="`standard-${index}`" class="standard-item">
                        <div class="standard-icon" :class="{ 'standard-icon-gold': index % 2 === 1 }">
                            <img :src="standardIcon(item, index)" alt="" loading="lazy" />
                        </div>
                        <div>
                            <h3>{{ item.title }}</h3>
                            <p>{{ item.description }}</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- 6. ECOSISTEMA -->
        <section id="plataforma-ecosistema" class="platform-ecosystem" aria-labelledby="platform-ecosystem-title">
            <img :src="ecosystemImage" class="ecosystem-bg" alt="" loading="lazy" />
            <div class="ecosystem-shade" aria-hidden="true"></div>
            <div class="platform-container ecosystem-grid">
                <div class="ecosystem-copy">
                    <h2 id="platform-ecosystem-title" class="section-title light-title">{{ ecosystem.title }}</h2>
                    <p v-if="ecosystem.paragraph_one">{{ ecosystem.paragraph_one }}</p>
                    <p v-if="ecosystem.paragraph_two">{{ ecosystem.paragraph_two }}</p>
                    <a v-if="ecosystem.button_text && ecosystem.button_url" :href="ecosystem.button_url" class="ecosystem-link">
                        {{ ecosystem.button_text }} <span aria-hidden="true">→</span>
                    </a>
                    <span v-else-if="ecosystem.button_text" class="ecosystem-link ecosystem-link-disabled">
                        {{ ecosystem.button_text }} <span aria-hidden="true">→</span>
                    </span>
                </div>
                <div class="ecosystem-panel">
                    <h3>{{ ecosystem.components_title }}</h3>
                    <ul>
                        <li v-for="(item, index) in ecosystemComponents" :key="`component-${index}`">
                            <span class="ecosystem-dot" aria-hidden="true"></span>
                            <div>
                                <strong>{{ item.title }}</strong>
                                <p>{{ item.description }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- 7. SIGUIENTE PASO -->
        <section id="plataforma-siguiente-paso" class="platform-next" aria-labelledby="platform-next-title">
            <div class="platform-container">
                <p v-if="nextStep.eyebrow" class="section-eyebrow eyebrow-light">{{ nextStep.eyebrow }}</p>
                <h2 id="platform-next-title" class="section-title light-title">{{ nextStep.title }}</h2>
                <p v-if="nextStep.description" class="section-intro next-description">{{ nextStep.description }}</p>
                <div class="next-actions">
                    <a v-if="nextStep.button_primary_text && nextStep.button_primary_url" :href="nextStep.button_primary_url" class="platform-button primary-button">
                        {{ nextStep.button_primary_text }} <span aria-hidden="true">→</span>
                    </a>
                    <a v-if="nextStep.button_secondary_text && nextStep.button_secondary_url" :href="nextStep.button_secondary_url" class="platform-button secondary-button">
                        {{ nextStep.button_secondary_text }} <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </section>
    </main>
</template>

<style scoped>
/* Único archivo Vue para las siete secciones de Plataforma. Mobile-first. */
.gdi-platform,
.gdi-platform * { box-sizing: border-box; }
.gdi-platform { width: 100%; overflow: clip; color: #29374b; font-family: 'Instrument Sans', Arial, sans-serif; }
.platform-container { width: min(100% - 40px, 1200px); margin-inline: auto; }
.section-eyebrow { margin: 0 0 22px; color: #8a96a5; font-size: 10px; font-weight: 500; letter-spacing: .1em; text-transform: uppercase; }
.section-title { margin: 0; color: #2b384b; font: 400 clamp(29px, 6.5vw, 43px)/1.13 Georgia, 'Times New Roman', serif; letter-spacing: -.025em; }
.section-intro { max-width: 850px; margin: 18px 0 0; color: #687380; font-size: 14px; line-height: 1.65; }
.eyebrow-light { color: #a4b0bc; }
.light-title { color: #fff; }
.light-description { color: #d3dce4; }

/* 1. Portada */
.platform-hero { position: relative; min-height: 460px; display: flex; align-items: center; isolation: isolate; background: #142338; color: #fff; }
.hero-picture, .hero-shade { position: absolute; inset: 0; width: 100%; height: 100%; }
.hero-picture { z-index: -2; }
.hero-picture img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
.hero-shade { z-index: -1; background: linear-gradient(90deg, rgba(7, 17, 31, .51), rgba(9, 21, 37, .2) 67%); }
.hero-content { padding-block: 92px; }
.hero-eyebrow { display: inline-flex; align-items: center; margin: 0 0 26px; padding: 6px 16px; color: #5e6c7c; background: #fff; border-radius: 30px; font-size: 11px; letter-spacing: .09em; }
.hero-title { max-width: 870px; margin: 0; color: #fff; font: 400 clamp(37px, 7.8vw, 70px)/1.08 Georgia, 'Times New Roman', serif; letter-spacing: -.025em; }
.hero-description { max-width: 800px; margin: 24px 0 0; color: #d9e1e9; font-size: 14px; line-height: 1.6; }

/* 2. Contexto */
.platform-context { background: #fff; padding-block: 68px; }
.context-grid { display: grid; gap: 38px; align-items: center; }
.context-copy p:not(.section-eyebrow) { margin: 22px 0 0; max-width: 505px; color: #76808b; font-size: 14px; line-height: 1.65; }
.context-title { max-width: 550px; }
.context-visual { width: 100%; min-height: 270px; aspect-ratio: 520 / 600; background: #ac6a3a; overflow: hidden; }
.context-visual img { width: 100%; height: 100%; object-fit: cover; }

/* 3 + 4. Bloque azul */
.platform-dark-block { background: #29384c; }
.platform-advantages { padding-block: 68px 54px; }
.advantages-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-top: 38px; }
.advantage-card { min-width: 0; min-height: 235px; padding: 22px 18px; }
.card-gold { background: #b98554; color: #fff; }
.card-white { background: #fff; color: #29384c; }
.advantage-icon { width: 34px; height: 34px; object-fit: contain; margin-bottom: 22px; }
.advantage-card h3 { margin: 0 0 12px; font: 400 clamp(17px, 3vw, 22px)/1.16 Georgia, 'Times New Roman', serif; }
.advantage-card p { margin: 0; font-size: 12px; line-height: 1.58; }
.card-gold p { color: #f8ebe0; }
.card-white p { color: #6c7782; }

/* 4. Funcionamiento */
.platform-operation { padding-block: 34px 76px; }
.operation-grid { display: grid; grid-template-columns: 1fr; gap: 16px; margin-top: 42px; }
.operation-card { min-width: 0; min-height: 230px; padding: 26px 18px; text-align: center; border: 1px solid #c7cdd3; color: #fff; }
.operation-highlight { border-color: #b98554; background: rgba(185, 133, 84, .1); }
.operation-number { display: grid; place-items: center; width: 58px; height: 58px; margin: 0 auto 20px; border-radius: 50%; background: #fff; color: #2a384c; font-size: 20px; }
.operation-highlight .operation-number { background: #b98554; }
.operation-card h3 { margin: 0 0 12px; font: 400 21px/1.2 Georgia, 'Times New Roman', serif; }
.operation-highlight h3 { color: #c18d59; }
.operation-card p { max-width: 260px; margin: 0 auto; color: #cdd5de; font-size: 12px; line-height: 1.56; }
.operation-arrow { justify-self: center; color: #c18d59; font-size: 25px; line-height: 1; transform: rotate(90deg); }

/* 5. Estándares */
.platform-standards { padding-block: 72px 84px; background: #fff; }
.standards-grid { display: grid; gap: 28px; margin-top: 38px; }
.standard-item { display: grid; grid-template-columns: 46px 1fr; align-items: start; gap: 16px; min-width: 0; }
.standard-icon { width: 46px; height: 46px; display: grid; place-items: center; flex-shrink: 0; border-radius: 50%; background: #2a394c; }
.standard-icon-gold { background: #b98554; }
.standard-icon img { width: 23px; height: 23px; object-fit: contain; }
.standard-item h3 { margin: 0 0 8px; color: #445369; font: 400 21px/1.2 Georgia, 'Times New Roman', serif; }
.standard-item p { margin: 0; color: #737f8c; font-size: 12px; line-height: 1.58; }

/* 6. Ecosistema */
.platform-ecosystem { position: relative; isolation: isolate; overflow: hidden; padding-block: 80px; background: #0c2135; color: #fff; }
.ecosystem-bg, .ecosystem-shade { position: absolute; inset: 0; width: 100%; height: 100%; }
.ecosystem-bg { z-index: -2; object-fit: cover; object-position: center; }
.ecosystem-shade { z-index: -1; background: linear-gradient(90deg, rgba(5, 22, 38, .5), rgba(5, 22, 38, .16)); }
.ecosystem-grid { display: grid; gap: 40px; align-items: center; }
.ecosystem-copy .section-title { max-width: 470px; }
.ecosystem-copy p { max-width: 520px; color: #b8c5d0; font-size: 14px; line-height: 1.64; margin: 18px 0 0; }
.ecosystem-link { display: inline-block; margin-top: 20px; padding-bottom: 4px; color: #fff; text-decoration: none; font-size: 13px; border-bottom: 1px solid rgba(255,255,255,.8); }
.ecosystem-link-disabled { cursor: default; opacity: .75; }
.ecosystem-panel { padding: 34px 28px; background: rgba(32, 50, 70, .68); backdrop-filter: blur(2px); }
.ecosystem-panel h3 { max-width: 270px; margin: 0 0 30px; color: #fff; font: 400 32px/1.12 Georgia, 'Times New Roman', serif; }
.ecosystem-panel ul { list-style: none; margin: 0; padding: 0; }
.ecosystem-panel li { display: flex; gap: 13px; padding: 13px 0; border-bottom: 1px solid rgba(255,255,255,.1); }
.ecosystem-panel li:last-child { border-bottom: 0; }
.ecosystem-dot { width: 5px; height: 5px; border-radius: 50%; margin-top: 8px; flex: 0 0 5px; background: #bc8a57; }
.ecosystem-panel li strong { display: block; color: #f6f8fa; font-size: 14px; font-weight: 500; }
.ecosystem-panel li p { margin: 4px 0 0; color: #a7b8c7; font-size: 11px; line-height: 1.5; }

/* 7. Siguiente paso */
.platform-next { background: #b98554; padding-block: 74px 100px; }
.platform-next .eyebrow-light { color: #efdbc9; }
.platform-next .light-title { max-width: 1060px; }
.next-description { max-width: 1040px; color: #f5e6d9; }
.next-actions { display: flex; flex-direction: column; align-items: stretch; gap: 12px; margin-top: 32px; }
.platform-button { display: inline-flex; align-items: center; justify-content: center; gap: 11px; min-height: 48px; padding: 12px 23px; border-radius: 999px; font-size: 12px; text-decoration: none; text-align: center; transition: background-color .2s ease; }
.primary-button { background: #fff; color: #b98554; }
.primary-button:hover { background: #f7f0e9; }
.secondary-button { background: transparent; color: #fff; border: 1px solid rgba(255,255,255,.85); }
.secondary-button:hover { background: rgba(255,255,255,.12); }

@media (min-width: 640px) {
    .platform-container { width: min(100% - 64px, 1200px); }
    .next-actions { flex-direction: row; align-items: center; }
    .advantages-grid { gap: 18px; }
}
@media (min-width: 768px) {
    .platform-hero { min-height: 530px; }
    .hero-content { padding-block: 115px; }
    .platform-context { padding-block: 95px; }
    .context-grid { grid-template-columns: 1fr 1fr; gap: 7%; }
    .context-visual { min-height: 390px; }
    .advantages-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 22px; }
    .advantage-card { min-height: 280px; padding: 25px 20px; }
    .platform-advantages { padding-block: 86px 70px; }
    .platform-operation { padding-block: 48px 90px; }
    .operation-grid { grid-template-columns: minmax(0,1fr) 24px minmax(0,1fr) 24px minmax(0,1fr); align-items: center; gap: 18px; }
    .operation-card { min-height: 260px; }
    .operation-arrow { transform: none; }
    .standards-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); column-gap: 60px; row-gap: 45px; }
    .platform-standards { padding-block: 86px 102px; }
    .ecosystem-grid { grid-template-columns: minmax(0, .95fr) minmax(0, 1fr); gap: 9%; }
    .platform-ecosystem { padding-block: 120px; }
    .platform-next { padding-block: 80px 125px; }
}
@media (min-width: 1100px) {
    .platform-container { width: min(100% - 92px, 1200px); }
    .platform-hero { min-height: 555px; }
    .hero-title { font-size: 67px; }
    .context-title { font-size: 48px; }
    .platform-ecosystem .section-title { font-size: 52px; }
}
@media (prefers-reduced-motion: reduce) {
    .gdi-platform * { transition: none !important; }
}
</style>
