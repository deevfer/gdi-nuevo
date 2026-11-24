{{-- Datos que la persona envió desde el formulario de Contacto. --}}
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:24px 0;background-color:#F8F6F3;border:1px solid #E5E0D8;">
    <x-emails.detail label="Nombre">{{ $contact->name }}</x-emails.detail>
    <x-emails.detail label="Email"><a href="mailto:{{ $contact->email }}" style="color:#2A374A;text-decoration:underline;">{{ $contact->email }}</a></x-emails.detail>
    @if ($contact->company)<x-emails.detail label="Empresa">{{ $contact->company }}</x-emails.detail>@endif
    @if ($contact->phone)<x-emails.detail label="Teléfono">{{ $contact->phone }}</x-emails.detail>@endif
    @if ($contact->subject)<x-emails.detail label="Asunto">{{ $contact->subject }}</x-emails.detail>@endif
    <x-emails.detail label="Mensaje"><div style="white-space:pre-line;">{{ $contact->message }}</div></x-emails.detail>
</table>
