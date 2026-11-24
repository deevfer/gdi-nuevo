/* LinkedIn Insight Tag y sus conversiones; solo con consentimiento de marketing. */
(() => {
    const config = window.GDI_LINKEDIN;
    if (!config?.enabled || !/^[0-9]{1,20}$/.test(config.partnerId || '') || window.GdiLinkedIn) return;
    const seen = new Set();
    const track = (event, reference = 'visit') => {
        const id = config.conversions?.[event];
        if (!/^[0-9]{1,20}$/.test(id || '') || typeof window.lintrk !== 'function' || seen.has(`${id}:${reference}`)) return;
        try {
            window.lintrk('track', { conversion_id: Number(id) });
            seen.add(`${id}:${reference}`);
        } catch (error) {}
    };
    window.GdiLinkedIn = { track };
    window._linkedin_partner_id = config.partnerId;
    window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
    if (!window._linkedin_data_partner_ids.some((id) => String(id) === String(config.partnerId))) window._linkedin_data_partner_ids.push(config.partnerId);
    if (typeof window.lintrk !== 'function') {
        window.lintrk = function (...args) { window.lintrk.q.push(args); };
        window.lintrk.q = [];
    }
    if (!document.querySelector('script[src*="snap.licdn.com/li.lms-analytics/insight.min.js"]')) {
        const script = document.createElement('script');
        script.async = true;
        script.src = 'https://snap.licdn.com/li.lms-analytics/insight.min.js';
        document.head.appendChild(script);
    }
    document.addEventListener('click', (event) => {
        const link = event.target.closest?.('a[href]');
        if (!link) return;
        try {
            const target = new URL(link.href, window.location.href);
            if (target.origin === window.location.origin && target.pathname === '/contacto') track('contact');
        } catch (error) {}
    });
    document.addEventListener('gdi:lead-saved', (event) => {
        const id = event.detail?.id;
        if (!/^[1-9][0-9]*$/.test(String(id || ''))) return;
        track('lead', `contact:${id}`);
    });
})();
