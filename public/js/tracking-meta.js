/* Meta Pixel con PageView y Lead; solo se carga con consentimiento de marketing. */
(() => {
    const id = window.GDI_META?.pixelId;
    if (!/^[0-9]+$/.test(id || '') || window.GdiMeta || !window.GdiConsent?.allows('marketing')) return;
    if (!window.fbq) {
        const fbq = window.fbq = function (...args) { fbq.callMethod ? fbq.callMethod(...args) : fbq.queue.push(args); };
        fbq.queue = [];
        fbq.loaded = true;
        fbq.version = '2.0';
        window._fbq = fbq;
    }
    // Sin autoConfig, escribir en el formulario nunca se interpreta como Lead.
    window.fbq('consent', 'grant');
    window.fbq('set', 'autoConfig', false, id);
    window.fbq('init', id);
    window.fbq('track', 'PageView');
    if (!document.querySelector('script[src*="connect.facebook.net/en_US/fbevents.js"]')) {
        const script = document.createElement('script');
        script.async = true;
        script.src = 'https://connect.facebook.net/en_US/fbevents.js';
        document.head.appendChild(script);
    }
    const seen = new Set();
    const track = (name, reference) => {
        if (!window.GdiConsent?.allows('marketing') || !/^[1-9][0-9]*$/.test(String(reference || '')) || seen.has(`${name}:${reference}`)) return;
        try {
            window.fbq('track', name, { form_name: 'contact' });
            seen.add(`${name}:${reference}`);
        } catch (error) {}
    };
    window.GdiMeta = { track };
    document.addEventListener('gdi:lead-saved', (event) => track('Lead', event.detail?.id));
})();
