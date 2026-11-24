<script setup>
import { computed, nextTick, ref } from 'vue';
import { imageUrl, list } from '../utils/imageUrl';
import InsightCard from '../components/InsightCard.vue';
import NextStepBanner from '../components/NextStepBanner.vue';

const props = defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
});

const hero = computed(() => props.content.hero ?? {});
const listing = computed(() => props.content.listing ?? {});
const categoriesSection = computed(() => props.content.categories ?? {});
const subscribe = computed(() => props.content.subscribe ?? {});
const featured = computed(() => props.content.featured ?? null);
const articles = computed(() => list(props.content.articles));
const categories = computed(() => list(categoriesSection.value.items).map((item) => item.name).filter(Boolean));

const STATIC_BASE = '/images/gdi/insights';
const heroDesktop = computed(() => imageUrl(hero.value.image_desktop || hero.value.image_mobile, `${STATIC_BASE}/hero.png`));
const heroMobile = computed(() => imageUrl(hero.value.image_mobile));
const readMore = computed(() => listing.value.read_more || 'Leer más');

// Filtro por categoría: se refleja en la URL (?categoria=) para poder compartirlo.
const initialCategory = typeof window !== 'undefined'
    ? new URLSearchParams(window.location.search).get('categoria')
    : null;
const activeCategory = ref(initialCategory || null);

const filteredArticles = computed(() => {
    if (!activeCategory.value) return articles.value;
    const all = featured.value ? [featured.value, ...articles.value] : articles.value;
    return all.filter((article) => article.category === activeCategory.value);
});

async function selectCategory(category) {
    activeCategory.value = activeCategory.value === category ? null : category;

    const url = new URL(window.location.href);
    if (activeCategory.value) url.searchParams.set('categoria', activeCategory.value);
    else url.searchParams.delete('categoria');
    window.history.replaceState({}, '', url);

    await nextTick();
    document.getElementById('insights-articulos')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>

<template>
    <main class="gdi-insights">
        <!-- 1. PORTADA -->
        <section id="insights-portada" class="insights-hero" aria-labelledby="insights-hero-title">
            <picture class="insights-hero-picture">
                <source v-if="heroMobile" media="(max-width: 767px)" :srcset="heroMobile" />
                <img :src="heroDesktop" alt="" fetchpriority="high" />
            </picture>
            <div class="insights-container insights-hero-copy">
                <p v-if="hero.eyebrow" class="insights-tag">{{ hero.eyebrow }}</p>
                <h1 id="insights-hero-title">{{ hero.title }}</h1>
                <p v-if="hero.description" class="insights-hero-description">{{ hero.description }}</p>
            </div>
        </section>

        <!-- 2. DESTACADO -->
        <section v-if="featured && !activeCategory" id="insights-destacado" class="insights-featured" aria-labelledby="insights-featured-title">
            <div class="insights-container featured-grid">
                <a class="featured-image" :href="featured.url" tabindex="-1" aria-hidden="true">
                    <img v-if="featured.image" :src="imageUrl(featured.image)" alt="" loading="lazy" />
                </a>
                <div class="featured-copy">
                    <p v-if="listing.featured_label" class="insights-eyebrow">{{ listing.featured_label }}</p>
                    <h2 id="insights-featured-title">
                        <a :href="featured.url">{{ featured.title }}</a>
                    </h2>
                    <p class="insights-meta">
                        <img :src="`${STATIC_BASE}/icono-calendario.svg`" alt="" width="16" height="16" />
                        <time v-if="featured.date" :datetime="featured.date_iso">{{ featured.date }}</time>
                        <span v-if="featured.date && featured.category" aria-hidden="true">•</span>
                        <span v-if="featured.category" class="insights-category">{{ featured.category }}</span>
                    </p>
                    <p v-if="featured.excerpt" class="featured-excerpt">{{ featured.excerpt }}</p>
                    <a class="featured-link" :href="featured.url">
                        {{ readMore }}
                        <img :src="`${STATIC_BASE}/flecha-leer-20.svg`" alt="" width="20" height="20" />
                        <span class="visually-hidden">: {{ featured.title }}</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- 3. ARTÍCULOS -->
        <section id="insights-articulos" class="insights-list" :aria-label="activeCategory ? `Artículos de ${activeCategory}` : 'Artículos'">
            <div class="insights-container">
                <div v-if="activeCategory" class="active-filter">
                    <p>{{ activeCategory }}</p>
                    <button type="button" @click="selectCategory(activeCategory)">Ver todos</button>
                </div>
                <div v-if="filteredArticles.length" class="insights-grid">
                    <InsightCard
                        v-for="article in filteredArticles"
                        :key="article.slug"
                        :article="article"
                        :read-more="readMore"
                    />
                </div>
                <p v-else class="insights-empty">{{ listing.empty_text || 'Todavía no hay artículos.' }}</p>
            </div>
        </section>

        <!-- 4. CATEGORÍAS -->
        <section v-if="categories.length" id="insights-categorias" class="insights-categories" aria-labelledby="insights-categories-title">
            <div class="insights-container">
                <p v-if="categoriesSection.eyebrow" class="insights-eyebrow">{{ categoriesSection.eyebrow }}</p>
                <h2 id="insights-categories-title" class="insights-title">{{ categoriesSection.title }}</h2>
                <div class="category-list">
                    <button
                        v-for="(category, index) in categories"
                        :key="category"
                        type="button"
                        class="category-button"
                        :class="{ 'category-button-bronze': index % 2 === 1 }"
                        :aria-pressed="activeCategory === category"
                        @click="selectCategory(category)"
                    >
                        {{ category }}
                    </button>
                </div>
            </div>
        </section>

        <!-- 5. SUSCRIPCIÓN -->
        <NextStepBanner :content="subscribe" section-id="insights-suscripcion" :show-arrow="false" />
    </main>
</template>

<style scoped>
/* Mobile-first. El frontend del listado de Insights permanece en este archivo. */
.gdi-insights,
.gdi-insights * { box-sizing: border-box; }
.gdi-insights { --navy: #2a374a; --bronze: #b68150; color: var(--navy); font-family: 'Instrument Sans', Arial, sans-serif; overflow: clip; }
.insights-container { width: min(100% - 40px, 1200px); margin-inline: auto; }
.insights-eyebrow { margin: 0 0 20px; font-size: 11px; line-height: 1.5; letter-spacing: .12em; text-transform: uppercase; }
.insights-title { margin: 0; font: 400 clamp(31px, 6vw, 48px)/1.1 Georgia, 'Times New Roman', serif; letter-spacing: -.025em; }
.visually-hidden { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }

/* 1. Portada */
.insights-hero { position: relative; display: flex; align-items: center; min-height: 420px; isolation: isolate; background: var(--navy); color: #fff; }
.insights-hero-picture { position: absolute; inset: 0; z-index: -1; }
.insights-hero-picture img { width: 100%; height: 100%; object-fit: cover; object-position: top; opacity: .75; }
.insights-hero-copy { padding-block: 90px; }
.insights-tag { display: inline-block; margin: 0 0 26px; padding: 7px 16px; border-radius: 999px; background: #fff; color: rgba(42,55,74,.9); font-size: 11px; line-height: 1.2; letter-spacing: .12em; text-transform: uppercase; }
.insights-hero h1 { margin: 0; font: 400 clamp(42px, 8vw, 66px)/1.2 Georgia, 'Times New Roman', serif; letter-spacing: -.025em; }
.insights-hero-description { max-width: 770px; margin: 24px 0 0; font-size: 14px; line-height: 1.65; }

/* 2. Destacado */
.insights-featured { padding-block: 64px; background: #fff; }
.featured-grid { display: grid; gap: 36px; align-items: start; }
.featured-image { display: block; aspect-ratio: 505 / 611; overflow: hidden; background: var(--bronze); box-shadow: 0 20px 25px -5px rgba(0,0,0,.1), 0 8px 10px -6px rgba(0,0,0,.1); }
.featured-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s ease; }
.featured-image:hover img { transform: scale(1.03); }
.featured-copy h2 { margin: 0; font: 400 clamp(31px, 5.5vw, 48px)/1.04 Georgia, 'Times New Roman', serif; letter-spacing: -.025em; }
.featured-copy h2 a { color: inherit; text-decoration: none; }
.featured-copy h2 a:hover { text-decoration: underline; text-decoration-thickness: 1px; text-underline-offset: 6px; }
.insights-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 12px; margin: 24px 0 0; font-size: 13px; line-height: 1.5; }
.insights-category { color: var(--bronze); }
.featured-excerpt { margin: 22px 0 30px; font-size: 14px; line-height: 1.75; }
.featured-link { display: inline-flex; align-items: center; gap: 8px; padding-bottom: 4px; border-bottom: 2px solid rgba(42,55,74,.2); color: var(--navy); font-size: 13px; text-decoration: none; transition: border-color .2s ease; }
.featured-link:hover { border-color: var(--bronze); }

/* 3. Artículos */
.insights-list { padding-block: 70px; background: var(--navy); scroll-margin-top: 80px; }
.insights-grid { display: grid; gap: 32px; }
.active-filter { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 32px; color: #fff; }
.active-filter p { margin: 0; font: 400 clamp(26px, 5vw, 36px)/1.2 Georgia, 'Times New Roman', serif; }
.active-filter button { padding: 10px 22px; border: 1px solid #fff; border-radius: 999px; background: transparent; color: #fff; font: inherit; font-size: 12px; cursor: pointer; }
.active-filter button:hover { background: rgba(255,255,255,.12); }
.insights-empty { margin: 0; color: #fff; font-size: 14px; }

/* 4. Categorías */
.insights-categories { padding-block: 80px; background: #fff; }
.category-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-top: 40px; }
.category-button { min-height: 52px; padding: 12px 20px; border: 0; border-radius: 100px; background: var(--navy); color: #fff; font: inherit; font-size: 13px; cursor: pointer; transition: opacity .2s ease, box-shadow .2s ease; }
.category-button-bronze { background: var(--bronze); }
.category-button:hover { opacity: .88; }
.category-button[aria-pressed='true'] { box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--navy); }
.category-button-bronze[aria-pressed='true'] { box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--bronze); }
.category-button:focus-visible { outline: 2px solid var(--bronze); outline-offset: 3px; }

@media (min-width: 640px) {
    .insights-container { width: min(100% - 64px, 1200px); }
    .insights-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .category-list { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (min-width: 768px) {
    .insights-hero { min-height: 560px; }
    .insights-hero-copy { padding-block: 140px 110px; }
    .insights-featured { padding-block: 64px; }
    .featured-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 64px; }
    .insights-list { padding-block: 70px; }
}
@media (min-width: 1100px) {
    .insights-container { width: min(100% - 96px, 1200px); }
    .insights-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 48px; }
    .category-list { grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 16px; }
}
@media (prefers-reduced-motion: reduce) {
    .gdi-insights * { transition: none !important; }
}
</style>
