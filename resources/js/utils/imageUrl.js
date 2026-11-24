// Filament guarda la ruta relativa dentro del disco public. Los archivos
// exportados de Figma son la imagen por defecto mientras el CMS no los sustituya.
export function imageUrl(path, fallback = '') {
    if (!path || typeof path !== 'string' || !path.trim()) return fallback;
    const value = path.trim();
    if (/^https?:\/\//i.test(value) || value.startsWith('/')) return value;
    return `/storage/${value.replace(/^storage\//, '')}`;
}

export function list(value) {
    return Array.isArray(value) ? value : [];
}
