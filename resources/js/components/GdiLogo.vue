<script setup>
// Logo de GDI Valores exportado de Figma en 8 piezas vectoriales.
// Cada pieza se coloca con los mismos márgenes porcentuales (arriba, derecha, abajo, izquierda) del diseño.
const BASE = '/images/gdi/marca';
const parts = [
    [0.25, 20.61, 70.48, 66.37],
    [0.95, 11.09, 46.05, 64.14],
    [7.56, 4.45, 27.46, 64.81],
    [63.64, 2.23, 7.08, 84.75],
    [39.21, 0, 7.79, 75.23],
    [20.62, 0.67, 14.4, 68.6],
    [0, 40.53, 6.67, 0],
    [79.76, 40.8, 0, 25.4],
].map(([top, right, bottom, left]) => ({
    top: `${top}%`,
    left: `${left}%`,
    width: `${100 - left - right}%`,
    height: `${100 - top - bottom}%`,
}));
</script>

<template>
    <span class="gdi-logo" role="img" aria-label="GDI Valores">
        <img
            v-for="(style, index) in parts"
            :key="index"
            :src="`${BASE}/logo-parte-${index + 1}.svg`"
            alt=""
            :style="style"
        />
    </span>
</template>

<style scoped>
.gdi-logo { position: relative; display: inline-block; width: 244px; max-width: 100%; aspect-ratio: 244 / 92; }
.gdi-logo img { position: absolute; display: block; max-width: none; }
</style>
