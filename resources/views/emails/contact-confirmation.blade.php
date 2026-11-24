<x-emails.layout :title="$copy['title']" :eyebrow="$copy['eyebrow']" :preheader="$copy['preheader']" :footer="$copy['footer']">
    <p style="margin:0 0 16px;">{{ $copy['greeting'] }} <strong>{{ $contact->name }}</strong>,</p>
    <p style="margin:0;white-space:pre-line;">{{ $copy['body'] }}</p>
    @if (filled($copy['summary_title']))
        <p style="margin:32px 0 0;color:#8A7F72;font-size:11px;line-height:18px;letter-spacing:2px;text-transform:uppercase;">{{ $copy['summary_title'] }}</p>
        @include('emails.partials.contact-details', ['contact' => $contact])
    @endif
    @if (filled($copy['reply_note']))
        <p style="margin:0;padding:18px 20px;background-color:#F8F1EA;border-left:3px solid #B68150;color:#2A374A;font-size:14px;line-height:23px;">{{ $copy['reply_note'] }}</p>
    @endif
    @if (filled($copy['cta']) && filled($ctaUrl))<x-emails.button :href="$ctaUrl">{{ $copy['cta'] }}</x-emails.button>@endif
    @if (filled($copy['signature']))<p style="margin:32px 0 0;white-space:pre-line;">{{ $copy['signature'] }}</p>@endif
</x-emails.layout>
