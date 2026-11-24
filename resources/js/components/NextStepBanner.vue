<script setup>
// Bloque de cierre color bronce con textura, compartido por las páginas internas.
defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
    sectionId: {
        type: String,
        default: 'siguiente-paso',
    },
    showArrow: {
        type: Boolean,
        default: true,
    },
});

const SHARED_BASE = '/images/gdi/compartido';
</script>

<template>
    <section :id="sectionId" class="next-step" :aria-labelledby="`${sectionId}-title`">
        <img class="next-step-texture" :src="`${SHARED_BASE}/textura.svg`" alt="" aria-hidden="true" />
        <div class="next-step-container">
            <p v-if="content.eyebrow" class="next-step-eyebrow">{{ content.eyebrow }}</p>
            <h2 :id="`${sectionId}-title`">{{ content.title }}</h2>
            <p v-if="content.description" class="next-step-description">{{ content.description }}</p>
            <a v-if="content.button_text && content.button_url" class="next-step-link" :href="content.button_url">
                {{ content.button_text }}
                <img v-if="showArrow" :src="`${SHARED_BASE}/flecha-boton.svg`" alt="" width="20" height="20" />
            </a>
        </div>
    </section>
</template>

<style scoped>
.next-step,
.next-step * { box-sizing: border-box; }
.next-step { position: relative; isolation: isolate; overflow: hidden; padding-block: 90px; background: #b68150; color: #fff; font-family: 'Instrument Sans', Arial, sans-serif; }
.next-step-texture { position: absolute; z-index: -1; top: 50%; right: -30%; width: 640px; max-width: none; height: auto; opacity: .5; mix-blend-mode: multiply; transform: translateY(-50%) scaleX(-1); pointer-events: none; }
.next-step-container { width: min(100% - 40px, 1200px); margin-inline: auto; }
.next-step-eyebrow { margin: 0 0 20px; font-size: 11px; line-height: 1.5; letter-spacing: .06em; text-transform: uppercase; }
.next-step h2 { max-width: 768px; margin: 0; font: 400 clamp(31px, 6vw, 48px)/1.12 Georgia, 'Times New Roman', serif; letter-spacing: -.025em; }
.next-step-description { max-width: 680px; margin: 18px 0 36px; font-size: 14px; line-height: 1.65; }
.next-step-link { display: inline-flex; align-items: center; gap: 10px; min-height: 48px; padding: 12px 32px; border-radius: 999px; background: #fff; color: #b68150; font-size: 13px; letter-spacing: .04em; text-decoration: none; transition: background-color .2s ease; }
.next-step-link:hover { background: #f4ede6; }

@media (min-width: 640px) {
    .next-step-container { width: min(100% - 64px, 1200px); }
}
@media (min-width: 768px) {
    .next-step { padding-block: 130px; }
    .next-step-texture { right: -8%; width: 700px; }
}
@media (min-width: 1100px) {
    .next-step-container { width: min(100% - 96px, 1200px); }
}
@media (prefers-reduced-motion: reduce) {
    .next-step-link { transition: none; }
}
</style>
