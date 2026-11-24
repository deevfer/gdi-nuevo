<script setup>
import { imageUrl } from '../utils/imageUrl';

// Tarjeta de artículo de Insights (listado y artículos relacionados).
defineProps({
    article: {
        type: Object,
        required: true,
    },
    readMore: {
        type: String,
        default: 'Leer más',
    },
});

const STATIC_BASE = '/images/gdi/insights';
</script>

<template>
    <article class="insight-card">
        <a class="insight-card-image" :href="article.url" tabindex="-1" aria-hidden="true">
            <img v-if="article.image" :src="imageUrl(article.image)" alt="" loading="lazy" />
        </a>
        <div class="insight-card-body">
            <p class="insight-card-meta">
                <img :src="`${STATIC_BASE}/icono-calendario.svg`" alt="" width="16" height="16" />
                <time v-if="article.date" :datetime="article.date_iso">{{ article.date }}</time>
                <span v-if="article.date && article.category" aria-hidden="true">•</span>
                <span v-if="article.category" class="insight-card-category">{{ article.category }}</span>
            </p>
            <h3><a :href="article.url">{{ article.title }}</a></h3>
            <p v-if="article.excerpt" class="insight-card-excerpt">{{ article.excerpt }}</p>
            <a class="insight-card-link" :href="article.url">
                {{ readMore }}
                <img :src="`${STATIC_BASE}/flecha-leer-16.svg`" alt="" width="16" height="16" />
                <span class="visually-hidden">: {{ article.title }}</span>
            </a>
        </div>
    </article>
</template>

<style scoped>
.insight-card,
.insight-card * { box-sizing: border-box; }
.insight-card { display: flex; flex-direction: column; min-width: 0; overflow: hidden; background: #fff; color: #2a374a; font-family: 'Instrument Sans', Arial, sans-serif; }
.insight-card-image { display: block; height: 240px; overflow: hidden; background: #d9d4cd; }
.insight-card-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s ease; }
.insight-card:hover .insight-card-image img { transform: scale(1.04); }
.insight-card-body { display: flex; flex: 1; flex-direction: column; padding: 30px 30px 34px; }
.insight-card-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; margin: 0; font-size: 12px; line-height: 1.5; }
.insight-card-category { color: #b68150; }
.insight-card h3 { margin: 16px 0 0; font: 400 23px/1.25 Georgia, 'Times New Roman', serif; }
.insight-card h3 a { color: inherit; text-decoration: none; }
.insight-card h3 a:hover { text-decoration: underline; text-decoration-thickness: 1px; text-underline-offset: 4px; }
.insight-card-excerpt { margin: 16px 0 24px; font-size: 13px; line-height: 1.7; }
.insight-card-link { display: inline-flex; align-items: center; gap: 8px; width: fit-content; margin-top: auto; color: #2a374a; font-size: 13px; text-decoration: none; }
.insight-card-link:hover { color: #b68150; }
.visually-hidden { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }

@media (prefers-reduced-motion: reduce) {
    .insight-card * { transition: none !important; }
}
</style>
