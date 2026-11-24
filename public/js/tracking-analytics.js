/* Evento generate_lead de GA4 para el formulario de contacto; solo con consentimiento de analítica. */
(() => {
    const measurementId = window.GDI_ANALYTICS?.measurementId;
    if (!/^G-[A-Z0-9]+$/.test(measurementId || '') || window.GdiAnalytics) return;
    const savedLeads = new Set();
    const trackLead = ({ id } = {}) => {
        if (!/^[1-9][0-9]*$/.test(String(id || '')) || savedLeads.has(id) || typeof window.gtag !== 'function') return;
        try {
            // El ID solo evita duplicados en el navegador; nunca se envían a GA4 los datos del formulario.
            window.gtag('event', 'generate_lead', { send_to: measurementId, form_name: 'contact', lead_type: 'contact_submitted' });
            savedLeads.add(id);
        } catch (error) {}
    };
    window.GdiAnalytics = { trackLead };
    document.addEventListener('gdi:lead-saved', (event) => trackLead(event.detail));
})();
