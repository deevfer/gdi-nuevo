<script setup>
import { computed } from 'vue';

// Gráfica de línea del rendimiento de un fondo. Los puntos vienen del CMS.
const props = defineProps({
    points: {
        type: Array,
        default: () => [],
    },
    label: {
        type: String,
        default: 'Gráfica de rendimiento',
    },
});

const WIDTH = 960;
const HEIGHT = 320;
const PAD = { top: 12, right: 20, bottom: 40, left: 50 };

const data = computed(() => props.points
    .map((point) => ({ label: String(point?.label ?? ''), value: Number(point?.value) }))
    .filter((point) => Number.isFinite(point.value)));

// Eje Y en pasos de 5, con al menos un paso de margen por arriba y por abajo.
const scale = computed(() => {
    const values = data.value.map((point) => point.value);
    if (!values.length) return { min: 95, max: 115, ticks: [95, 100, 105, 110, 115] };
    const min = Math.floor(Math.min(...values) / 5) * 5 - 5;
    const max = Math.ceil(Math.max(...values) / 5) * 5 + 5;
    const ticks = [];
    for (let tick = min; tick <= max; tick += 5) ticks.push(tick);
    return { min, max, ticks };
});

const plotWidth = WIDTH - PAD.left - PAD.right;
const plotHeight = HEIGHT - PAD.top - PAD.bottom;

function x(index) {
    const count = data.value.length;
    return PAD.left + (count > 1 ? (index / (count - 1)) * plotWidth : plotWidth / 2);
}

function y(value) {
    const { min, max } = scale.value;
    return PAD.top + (1 - (value - min) / (max - min)) * plotHeight;
}

const linePath = computed(() => data.value
    .map((point, index) => `${index ? 'L' : 'M'}${x(index).toFixed(1)},${y(point.value).toFixed(1)}`)
    .join(' '));
</script>

<template>
    <figure v-if="data.length" class="fund-chart" tabindex="0">
        <svg :viewBox="`0 0 ${WIDTH} ${HEIGHT}`" role="img" :aria-label="label">
            <g class="fund-chart-grid">
                <line :x1="PAD.left" :x2="PAD.left" :y1="PAD.top" :y2="HEIGHT - PAD.bottom" />
                <line :x1="PAD.left" :x2="WIDTH - PAD.right" :y1="HEIGHT - PAD.bottom" :y2="HEIGHT - PAD.bottom" />
                <g v-for="tick in scale.ticks" :key="`y-${tick}`">
                    <line :x1="PAD.left - 6" :x2="PAD.left" :y1="y(tick)" :y2="y(tick)" />
                    <text :x="PAD.left - 10" :y="y(tick)" text-anchor="end" dominant-baseline="middle">{{ tick }}</text>
                </g>
                <g v-for="(point, index) in data" :key="`x-${index}`">
                    <line :x1="x(index)" :x2="x(index)" :y1="HEIGHT - PAD.bottom" :y2="HEIGHT - PAD.bottom + 6" />
                    <text :x="x(index)" :y="HEIGHT - PAD.bottom + 22" text-anchor="middle">{{ point.label }}</text>
                </g>
            </g>
            <path class="fund-chart-line" :d="linePath" vector-effect="non-scaling-stroke" />
            <g class="fund-chart-points">
                <circle v-for="(point, index) in data" :key="`p-${index}`" :cx="x(index)" :cy="y(point.value)" r="10">
                    <title>{{ point.label }}: {{ point.value }}</title>
                </circle>
            </g>
        </svg>
    </figure>
</template>

<style scoped>
/* En móvil la gráfica conserva un ancho mínimo legible y se desplaza en horizontal. */
.fund-chart { margin: 0; overflow-x: auto; }
.fund-chart svg { display: block; width: 100%; min-width: 560px; height: auto; overflow: visible; }
.fund-chart-grid line { stroke: rgba(255,255,255,.55); stroke-width: 1; vector-effect: non-scaling-stroke; }
.fund-chart-grid text { fill: #fff; font: 15px 'Instrument Sans', Arial, sans-serif; }
.fund-chart-line { fill: none; stroke: #b68150; stroke-width: 2; stroke-linejoin: round; }
.fund-chart-points circle { fill: transparent; }
.fund-chart-points circle:hover { fill: rgba(182,129,80,.35); }
</style>
