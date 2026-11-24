/* Conversión de Google Ads al enviar el formulario de contacto; solo con consentimiento de marketing. */
(() => {
    const config = window.GDI_ADS;
    if (!/^AW-[0-9]+$/.test(config?.adsId || '') || !/^[A-Za-z0-9_-]+$/.test(config?.leadLabel || '') || window.GdiAds) return;
    const seen = new Set();
    const trackLead = ({ id } = {}) => {
        if (!/^[1-9][0-9]*$/.test(String(id || '')) || seen.has(id) || typeof window.gtag !== 'function' || !window.GdiConsent?.allows('marketing')) return;
        try {
            window.gtag('event', 'conversion', { send_to: `${config.adsId}/${config.leadLabel}` });
            seen.add(id);
        } catch (error) {}
    };
    window.GdiAds = { trackLead };
    document.addEventListener('gdi:lead-saved', (event) => trackLead(event.detail));
})();
