<script setup>
import { computed } from 'vue';
import { imageUrl, list } from '../utils/imageUrl';
import InsightCard from '../components/InsightCard.vue';
import NextStepBanner from '../components/NextStepBanner.vue';

// Página de artículo. No existe en Figma: sigue el lenguaje visual del listado de Insights.
const props = defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
});

const article = computed(() => props.content.article ?? {});
const related = computed(() => list(props.content.related));
const listing = computed(() => props.content.listing ?? {});
const subscribe = computed(() => props.content.subscribe ?? {});
const readMore = computed(() => listing.value.read_more || 'Leer más');

const STATIC_BASE = '/images/gdi/insights';
</script>

<template>
    <main class="gdi-article">
        <article>
            <header class="article-header">
                <div class="article-container">
                    <a class="article-back" href="/insights">← Insights</a>
                    <p class="article-meta">
                        <time v-if="article.date" :datetime="article.date_iso">{{ article.date }}</time>
                        <span v-if="article.date && article.category" aria-hidden="true">•</span>
                        <a v-if="article.category" class="article-category" :href="`/insights?categoria=${encodeURIComponent(article.category)}`">
                            {{ article.category }}
                        </a>
                    </p>
                    <h1>{{ article.title }}</h1>
                    <p v-if="article.excerpt" class="article-excerpt">{{ article.excerpt }}</p>
                </div>
            </header>

            <div v-if="article.image" class="article-container article-image">
                <img :src="imageUrl(article.image)" alt="" />
            </div>

            <!-- Contenido redactado por el equipo en el editor del CMS. -->
            <div class="article-container article-body" v-html="article.body"></div>
        </article>

        <section v-if="related.length" class="article-related" aria-labelledby="article-related-title">
            <div class="article-container article-container-wide">
                <h2 id="article-related-title">Más insights</h2>
                <div class="related-grid">
                    <InsightCard v-for="item in related" :key="item.slug" :article="item" :read-more="readMore" />
                </div>
            </div>
        </section>

        <NextStepBanner :content="subscribe" section-id="articulo-suscripcion" :show-arrow="false" />
    </main>
</template>

<style scoped>
.gdi-article,
.gdi-article * { box-sizing: border-box; }
.gdi-article { --navy: #2a374a; --bronze: #b68150; color: var(--navy); font-family: 'Instrument Sans', Arial, sans-serif; overflow: clip; }
.article-container { width: min(100% - 40px, 820px); margin-inline: auto; }
.article-container-wide { width: min(100% - 40px, 1200px); }

.article-header { padding-block: 70px 50px; background: var(--navy); color: #fff; }
.article-back { display: inline-block; margin-bottom: 36px; color: #fff; font-size: 12px; letter-spacing: .1em; text-decoration: none; text-transform: uppercase; opacity: .8; }
.article-back:hover { opacity: 1; }
.article-meta { display: flex; flex-wrap: wrap; gap: 8px 12px; margin: 0 0 20px; font-size: 13px; }
.article-category { color: var(--bronze); text-decoration: none; }
.article-category:hover { text-decoration: underline; }
.article-header h1 { margin: 0; font: 400 clamp(34px, 6vw, 56px)/1.1 Georgia, 'Times New Roman', serif; letter-spacing: -.02em; }
.article-excerpt { margin: 24px 0 0; color: #d6dce6; font-size: 16px; line-height: 1.65; }

.article-image { margin-top: 50px; }
.article-image img { display: block; width: 100%; max-height: 520px; object-fit: cover; }

.article-body { padding-block: 50px 80px; font-size: 16px; line-height: 1.8; }
.article-body :deep(h2),
.article-body :deep(h3) { margin: 1.8em 0 .6em; font-family: Georgia, 'Times New Roman', serif; font-weight: 400; line-height: 1.2; }
.article-body :deep(h2) { font-size: 32px; }
.article-body :deep(h3) { font-size: 25px; }
.article-body :deep(p) { margin: 0 0 1.2em; }
.article-body :deep(a) { color: var(--bronze); }
.article-body :deep(blockquote) { margin: 1.6em 0; padding-left: 22px; border-left: 3px solid var(--bronze); font: italic 21px/1.5 Georgia, 'Times New Roman', serif; }
.article-body :deep(img) { max-width: 100%; height: auto; }
.article-body :deep(ul),
.article-body :deep(ol) { padding-left: 1.3em; }

.article-related { padding-block: 70px; background: var(--navy); }
.article-related h2 { margin: 0 0 36px; color: #fff; font: 400 clamp(30px, 5vw, 44px)/1.1 Georgia, 'Times New Roman', serif; }
.related-grid { display: grid; gap: 32px; }

@media (min-width: 640px) {
    .article-container { width: min(100% - 64px, 820px); }
    .article-container-wide { width: min(100% - 64px, 1200px); }
    .related-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (min-width: 768px) {
    .article-header { padding-block: 110px 70px; }
}
@media (min-width: 1100px) {
    .article-container-wide { width: min(100% - 96px, 1200px); }
    .related-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 48px; }
}
</style>
