<x-emails.layout :title="$copy['title']" :eyebrow="$copy['eyebrow']" :preheader="$contact->name.($contact->subject ? ' · '.$contact->subject : '')" :footer="$copy['footer']">
    <p style="margin:0;"><strong>{{ $contact->name }}</strong> {{ $copy['intro'] }}</p>
    @include('emails.partials.contact-details', ['contact' => $contact])
    <p style="margin:0;color:#8A7F72;font-size:12px;line-height:20px;">Recibido: {{ ($contact->created_at ?? now())->timezone('America/Guatemala')->format('d/m/Y H:i') }} · Referencia: mensaje #{{ $contact->id }}</p>
    <x-emails.button :href="$inboxUrl">{{ $copy['cta'] }}</x-emails.button>
    @if (filled($copy['reply_note']))<p style="margin:22px 0 0;color:#8A7F72;font-size:13px;line-height:21px;">{{ $copy['reply_note'] }}</p>@endif
</x-emails.layout>
