<script setup>
import { computed, reactive, ref } from 'vue';
import { imageUrl, list } from '../utils/imageUrl';

const props = defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
});

const hero = computed(() => props.content.hero ?? {});
const form = computed(() => props.content.form ?? {});
const info = computed(() => props.content.info ?? {});
const areas = computed(() => props.content.areas ?? {});
const legal = computed(() => props.content.legal ?? {});

const subjects = computed(() => list(form.value.subjects).map((item) => item.name).filter(Boolean));
const areaCards = computed(() => list(areas.value.cards));

const STATIC_BASE = '/images/gdi/contacto';
const heroDesktop = computed(() => imageUrl(hero.value.image_desktop || hero.value.image_mobile, `${STATIC_BASE}/hero.png`));
const heroMobile = computed(() => imageUrl(hero.value.image_mobile));
const phoneHref = computed(() => `tel:${String(info.value.phone || '').replace(/[^\d+]/g, '')}`);

// Un enlace como /contacto?asunto=... preselecciona el asunto si existe en la lista.
const requestedSubject = typeof window !== 'undefined'
    ? new URLSearchParams(window.location.search).get('asunto')
    : null;

const fields = reactive({
    name: '',
    email: '',
    company: '',
    phone: '',
    subject: requestedSubject && subjects.value.includes(requestedSubject) ? requestedSubject : '',
    message: '',
    website: '',
});
const errors = ref({});
const status = ref('idle'); // idle | sending | sent | error
const feedback = ref('');

// reCAPTCHA v3 (solo si está activo en el CMS): devuelve un token invisible para la persona.
async function recaptchaToken() {
    const siteKey = window.gdiRecaptcha;
    if (!siteKey) return null;
    for (let attempt = 0; attempt < 50 && !window.grecaptcha; attempt += 1) {
        await new Promise((resolve) => setTimeout(resolve, 100));
    }
    if (!window.grecaptcha) return null;
    return new Promise((resolve) => {
        window.grecaptcha.ready(() => {
            window.grecaptcha.execute(siteKey, { action: 'contact' }).then(resolve, () => resolve(null));
        });
    });
}

async function submit() {
    status.value = 'sending';
    errors.value = {};
    feedback.value = '';

    try {
        const token = await recaptchaToken();
        const response = await fetch('/contacto', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify(token ? { ...fields, 'g-recaptcha-response': token } : fields),
        });
        const data = await response.json().catch(() => ({}));

        if (response.ok) {
            // Los píxeles de medición (con consentimiento) registran el lead una sola vez por id.
            document.dispatchEvent(new CustomEvent('gdi:lead-saved', { detail: { source: 'contact', id: data.id } }));
            status.value = 'sent';
            feedback.value = data.message || form.value.success_message || 'Mensaje enviado.';
            Object.assign(fields, { name: '', email: '', company: '', phone: '', subject: '', message: '', website: '' });
            return;
        }

        status.value = 'error';
        if (response.status === 422) {
            errors.value = data.errors ?? {};
            feedback.value = data.errors?.['g-recaptcha-response']?.[0] || 'Revise los campos marcados.';
        } else if (response.status === 429) {
            feedback.value = 'Ha enviado varios mensajes seguidos. Intente de nuevo en un minuto.';
        } else if (response.status === 419) {
            feedback.value = 'La sesión expiró. Recargue la página e intente de nuevo.';
        } else {
            feedback.value = 'No se pudo enviar el mensaje. Intente de nuevo o escríbanos por email.';
        }
    } catch {
        status.value = 'error';
        feedback.value = 'No se pudo enviar el mensaje. Revise su conexión e intente de nuevo.';
    }
}

function fieldError(name) {
    return Array.isArray(errors.value[name]) ? errors.value[name][0] : '';
}
</script>

<template>
    <main class="gdi-contact">
        <!-- 1. PORTADA -->
        <section id="contacto-portada" class="contact-hero" aria-labelledby="contact-hero-title">
            <picture class="contact-hero-picture">
                <source v-if="heroMobile" media="(max-width: 767px)" :srcset="heroMobile" />
                <img :src="heroDesktop" alt="" fetchpriority="high" />
            </picture>
            <div class="contact-hero-shade" aria-hidden="true"></div>
            <div class="contact-container contact-hero-copy">
                <p v-if="hero.eyebrow" class="contact-tag">{{ hero.eyebrow }}</p>
                <h1 id="contact-hero-title">{{ hero.title }}</h1>
                <p v-if="hero.description" class="contact-hero-description">{{ hero.description }}</p>
            </div>
        </section>

        <!-- 2. FORMULARIO + INFORMACIÓN -->
        <section id="contacto-formulario" class="contact-main" aria-label="Formulario e información de contacto">
            <div class="contact-form-panel">
                <div class="contact-panel-inner">
                    <p v-if="form.eyebrow" class="contact-eyebrow">{{ form.eyebrow }}</p>
                    <h2 class="contact-title">{{ form.title }}</h2>

                    <form class="contact-form" data-contact-form novalidate @submit.prevent="submit">
                        <div class="field">
                            <label for="contact-name">Nombre completo</label>
                            <input id="contact-name" v-model="fields.name" type="text" name="name" autocomplete="name" required :aria-invalid="!!fieldError('name')" aria-describedby="contact-name-error" />
                            <p v-if="fieldError('name')" id="contact-name-error" class="field-error">{{ fieldError('name') }}</p>
                        </div>
                        <div class="field">
                            <label for="contact-email">Email</label>
                            <input id="contact-email" v-model="fields.email" type="email" name="email" autocomplete="email" required :aria-invalid="!!fieldError('email')" aria-describedby="contact-email-error" />
                            <p v-if="fieldError('email')" id="contact-email-error" class="field-error">{{ fieldError('email') }}</p>
                        </div>
                        <div class="field">
                            <label for="contact-company">Empresa</label>
                            <input id="contact-company" v-model="fields.company" type="text" name="company" autocomplete="organization" />
                        </div>
                        <div class="field">
                            <label for="contact-phone">Teléfono</label>
                            <input id="contact-phone" v-model="fields.phone" type="tel" name="phone" autocomplete="tel" />
                        </div>
                        <div v-if="subjects.length" class="field field-wide">
                            <label for="contact-subject">Asunto</label>
                            <div class="select-wrap">
                                <select id="contact-subject" v-model="fields.subject" name="subject">
                                    <option value="">Seleccione un asunto</option>
                                    <option v-for="subject in subjects" :key="subject" :value="subject">{{ subject }}</option>
                                </select>
                                <img :src="`${STATIC_BASE}/icono-desplegable.svg`" alt="" width="10" height="6" />
                            </div>
                        </div>
                        <div class="field field-wide">
                            <label for="contact-message">Mensaje</label>
                            <textarea id="contact-message" v-model="fields.message" name="message" rows="6" required :aria-invalid="!!fieldError('message')" aria-describedby="contact-message-error"></textarea>
                            <p v-if="fieldError('message')" id="contact-message-error" class="field-error">{{ fieldError('message') }}</p>
                        </div>

                        <!-- Campo trampa para bots: oculto a las personas. -->
                        <div class="field-trap" aria-hidden="true">
                            <label for="contact-website">No llenar</label>
                            <input id="contact-website" v-model="fields.website" type="text" name="website" tabindex="-1" autocomplete="off" />
                        </div>

                        <div class="field-wide form-actions">
                            <button type="submit" class="contact-submit" :disabled="status === 'sending'">
                                {{ status === 'sending' ? 'Enviando…' : (form.submit_text || 'Enviar mensaje') }}
                            </button>
                            <p v-if="feedback" class="form-feedback" :class="{ 'form-feedback-error': status === 'error' }" role="status">
                                {{ feedback }}
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <aside class="contact-info-panel" aria-labelledby="contact-info-title">
                <div class="contact-panel-inner">
                    <p v-if="info.eyebrow" class="contact-eyebrow">{{ info.eyebrow }}</p>
                    <h2 id="contact-info-title" class="contact-title contact-info-title">{{ info.title }}</h2>

                    <ul class="info-list">
                        <li v-if="info.office_text">
                            <span class="info-icon"><img :src="`${STATIC_BASE}/icono-oficinas.svg`" alt="" width="24" height="24" /></span>
                            <div>
                                <h3>{{ info.office_title }}</h3>
                                <p>{{ info.office_text }}</p>
                            </div>
                        </li>
                        <li v-if="info.phone">
                            <span class="info-icon"><img :src="`${STATIC_BASE}/icono-telefono.svg`" alt="" width="24" height="24" /></span>
                            <div>
                                <h3>{{ info.phone_title }}</h3>
                                <p><a :href="phoneHref">{{ info.phone }}</a></p>
                            </div>
                        </li>
                        <li v-if="info.email">
                            <span class="info-icon"><img :src="`${STATIC_BASE}/icono-email.svg`" alt="" width="24" height="24" /></span>
                            <div>
                                <h3>{{ info.email_title }}</h3>
                                <p><a :href="`mailto:${info.email}`">{{ info.email }}</a></p>
                            </div>
                        </li>
                    </ul>

                    <div v-if="info.hours_text" class="info-hours">
                        <h3>{{ info.hours_title }}</h3>
                        <p>{{ info.hours_text }}</p>
                    </div>
                </div>
            </aside>
        </section>

        <!-- 3. ÁREAS DE CONTACTO -->
        <section id="contacto-areas" class="contact-areas" aria-labelledby="contact-areas-title">
            <div class="contact-container">
                <p v-if="areas.eyebrow" class="contact-eyebrow">{{ areas.eyebrow }}</p>
                <h2 id="contact-areas-title" class="contact-title">{{ areas.title }}</h2>
                <div class="areas-grid">
                    <article v-for="(card, index) in areaCards" :key="`area-${index}`" class="area-card">
                        <h3>{{ card.title }}</h3>
                        <p v-if="card.description">{{ card.description }}</p>
                        <a v-if="card.email" :href="`mailto:${card.email}`">{{ card.email }}</a>
                    </article>
                </div>
            </div>
        </section>

        <!-- 4. AVISO LEGAL -->
        <section v-if="legal.text" id="contacto-aviso-legal" class="contact-legal" aria-labelledby="contact-legal-title">
            <div class="contact-container">
                <div class="legal-box">
                    <h2 id="contact-legal-title">{{ legal.title }}</h2>
                    <p>{{ legal.text }}</p>
                </div>
            </div>
        </section>
    </main>
</template>

<style scoped>
/* Mobile-first. Todo el frontend de Contacto permanece en este archivo. */
.gdi-contact,
.gdi-contact * { box-sizing: border-box; }
.gdi-contact { --navy: #2a374a; --bronze: #b68150; color: var(--navy); font-family: 'Instrument Sans', Arial, sans-serif; overflow: clip; }
.contact-container { width: min(100% - 40px, 1200px); margin-inline: auto; }
.contact-eyebrow { margin: 0 0 20px; font-size: 11px; line-height: 1.5; letter-spacing: .06em; text-transform: uppercase; }
.contact-title { margin: 0; font: 400 clamp(31px, 6vw, 48px)/1.1 Georgia, 'Times New Roman', serif; letter-spacing: -.02em; }

/* 1. Portada */
.contact-hero { position: relative; display: flex; align-items: center; min-height: 420px; isolation: isolate; background: var(--navy); color: #fff; }
.contact-hero-picture,
.contact-hero-shade { position: absolute; inset: 0; width: 100%; height: 100%; }
.contact-hero-picture { z-index: -2; }
.contact-hero-picture img { width: 100%; height: 100%; object-fit: cover; object-position: 70% 30%; }
.contact-hero-shade { z-index: -1; background: linear-gradient(90deg, rgba(42,55,74,.75) 0%, rgba(42,55,74,0) 50%); }
.contact-hero-copy { padding-block: 90px; }
.contact-tag { display: inline-block; margin: 0 0 26px; padding: 7px 16px; border-radius: 999px; background: #fff; color: var(--navy); font-size: 11px; line-height: 1.2; letter-spacing: .12em; text-transform: uppercase; }
.contact-hero h1 { margin: 0; font: 400 clamp(42px, 8vw, 66px)/1.2 Georgia, 'Times New Roman', serif; letter-spacing: -.025em; }
.contact-hero-description { max-width: 770px; margin: 24px 0 0; font-size: 14px; line-height: 1.75; }

/* 2. Formulario + información */
.contact-main { display: grid; }
.contact-form-panel { background: var(--navy); color: #fff; }
.contact-info-panel { background: var(--bronze); color: #fff; }
.contact-panel-inner { width: min(100% - 40px, 720px); margin-inline: auto; padding-block: 64px; }

.contact-form { display: grid; gap: 30px; margin-top: 44px; }
.field { display: flex; flex-direction: column; min-width: 0; }
.field label { margin-bottom: 12px; font-size: 12px; line-height: 1.6; letter-spacing: .03em; text-transform: uppercase; }
.field input,
.field select,
.field textarea { width: 100%; padding: 12px 0; border: 0; border-bottom: 1px solid var(--bronze); border-radius: 0; background: transparent; color: #fff; font: inherit; font-size: 14px; outline: none; transition: border-color .2s ease; }
.field textarea { min-height: 150px; resize: vertical; }
.field input:focus,
.field select:focus,
.field textarea:focus { border-bottom-color: #fff; }
.field input:focus-visible,
.field select:focus-visible,
.field textarea:focus-visible { box-shadow: 0 1px 0 #fff; }
.field [aria-invalid='true'] { border-bottom-color: #f2a48f; }
.select-wrap { position: relative; }
.select-wrap select { padding-right: 28px; cursor: pointer; appearance: none; }
.select-wrap select option { color: var(--navy); }
.select-wrap img { position: absolute; top: 50%; right: 4px; transform: translateY(-50%); pointer-events: none; }
.field-error { margin: 8px 0 0; color: #f6c0b1; font-size: 12px; }
.field-trap { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }
.form-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 18px 24px; margin-top: 4px; }
.contact-submit { min-height: 52px; padding: 12px 44px; border: 0; border-radius: 100px; background: #fff; color: var(--navy); font: inherit; font-size: 14px; cursor: pointer; transition: background-color .2s ease; }
.contact-submit:hover { background: #f1ede8; }
.contact-submit:disabled { cursor: wait; opacity: .7; }
.contact-submit:focus-visible { outline: 2px solid var(--bronze); outline-offset: 3px; }
.form-feedback { flex: 1 1 260px; margin: 0; font-size: 13px; line-height: 1.5; }
.form-feedback-error { color: #f6c0b1; }

.info-list { display: grid; gap: 34px; margin: 44px 0 0; padding: 0; list-style: none; }
.info-list li { display: flex; align-items: flex-start; gap: 16px; }
.info-icon { display: grid; place-items: center; flex: 0 0 52px; width: 52px; height: 52px; border-radius: 50%; background: #fff; }
.info-list h3,
.info-hours h3 { margin: 0; font: 400 19px/1.45 Georgia, 'Times New Roman', serif; }
.info-list p,
.info-hours p { margin: 6px 0 0; font-size: 13px; line-height: 1.7; }
.info-list a { color: inherit; text-decoration: none; }
.info-list a:hover { text-decoration: underline; }
.info-hours { margin-top: 56px; padding-top: 30px; border-top: 1px solid #fff; }

/* 3. Áreas de contacto */
.contact-areas { padding-block: 80px; background: linear-gradient(180deg, #fafafa, #f8f8f8); }
.areas-grid { display: grid; gap: 24px; margin-top: 44px; }
.area-card { padding: 36px 34px 40px; background: var(--navy); color: #fff; }
.area-card h3 { max-width: 220px; margin: 0; font: 400 clamp(24px, 4vw, 28px)/1.15 Georgia, 'Times New Roman', serif; }
.area-card p { margin: 16px 0 24px; font-size: 13px; line-height: 1.7; }
.area-card a { color: var(--bronze); font-size: 13px; overflow-wrap: anywhere; }
.area-card a:hover { color: #d1a679; }

/* 4. Aviso legal */
.contact-legal { padding-block: 50px; background: #fff; }
.legal-box { padding: 36px 28px; background: linear-gradient(166deg, #f8f8f8, #f5f5f5); }
.legal-box h2 { margin: 0 0 22px; font: 400 clamp(24px, 4vw, 28px)/1.15 Georgia, 'Times New Roman', serif; }
.legal-box p { margin: 0; font-size: 13px; line-height: 1.75; }

@media (min-width: 640px) {
    .contact-container { width: min(100% - 64px, 1200px); }
    .contact-form { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 32px; }
    .field-wide { grid-column: 1 / -1; }
    .areas-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .legal-box { padding: 48px 52px; }
}
@media (min-width: 768px) {
    .contact-hero { min-height: 560px; }
    .contact-hero-copy { padding-block: 140px 110px; }
    .contact-panel-inner { width: min(100% - 64px, 720px); padding-block: 70px 80px; }
    .contact-areas { padding-block: 100px; }
    .contact-legal { padding-block: 50px; }
}
@media (min-width: 1000px) {
    /* Columnas a sangre: el fondo azul y el bronce llegan a los bordes de la ventana. */
    .contact-main { grid-template-columns: minmax(0, 64fr) minmax(0, 36fr); }
    .contact-form-panel .contact-panel-inner { width: auto; max-width: 760px; margin: 0 64px 0 auto; padding-left: 48px; }
    .contact-info-panel .contact-panel-inner { width: auto; max-width: 420px; margin: 0 auto 0 0; padding-inline: 48px; }
    .areas-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 32px; }
}
@media (prefers-reduced-motion: reduce) {
    .gdi-contact * { transition: none !important; }
}
</style>
