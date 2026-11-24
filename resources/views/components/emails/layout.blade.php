{{-- Estructura común de los correos de GDI Valores (tablas y estilos en línea para Gmail y Outlook). --}}
@props(['title', 'eyebrow' => null, 'preheader' => '', 'footer' => null])
@php
    $seo = app(\App\Services\SiteSeo::class);
    $siteUrl = $seo->origin();
    $info = \App\Models\PageSection::query()->where('page', 'contact')->where('section', 'info')->value('content') ?? [];
    $serif = "Georgia,'Times New Roman',serif";
    $sans = "Helvetica,Arial,sans-serif";
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $title }} · GDI Valores</title>
</head>
<body style="margin:0;padding:0;background-color:#F3F1EE;color:#2A374A;font-family:{{ $sans }};-webkit-text-size-adjust:100%;">
    <div style="display:none;font-size:1px;line-height:1px;color:#F3F1EE;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;">{{ $preheader }}</div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F3F1EE;">
        <tr>
            <td align="center" style="padding:36px 12px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;background-color:#FFFFFF;border:1px solid #E5E0D8;">
                    <tr>
                        <td style="padding:24px 32px;background-color:#2A374A;">
                            <a href="{{ $siteUrl }}" style="text-decoration:none;"><img src="{{ asset('images/gdi/correo/logo-blanco.png') }}" width="146" height="70" alt="GDI Valores" style="display:block;border:0;width:146px;height:70px;"></a>
                        </td>
                    </tr>
                    <tr><td height="4" style="height:4px;background-color:#B68150;font-size:0;line-height:0;">&nbsp;</td></tr>
                    <tr>
                        <td style="padding:40px 32px 8px;">
                            @if ($eyebrow)
                                <p style="margin:0 0 14px;color:#B68150;font-family:{{ $sans }};font-size:11px;line-height:18px;letter-spacing:2px;text-transform:uppercase;">{{ $eyebrow }}</p>
                            @endif
                            <h1 style="margin:0;color:#2A374A;font-family:{{ $serif }};font-size:32px;font-weight:400;line-height:38px;">{{ $title }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px 40px;color:#2A374A;font-family:{{ $sans }};font-size:15px;line-height:25px;overflow-wrap:anywhere;word-break:break-word;">{{ $slot }}</td>
                    </tr>
                    <tr>
                        <td style="padding:24px 32px;background-color:#2A374A;color:#C9D0DA;font-family:{{ $sans }};font-size:12px;line-height:19px;">
                            <p style="margin:0 0 6px;color:#FFFFFF;font-family:{{ $serif }};font-size:15px;">GDI Valores</p>
                            @if (filled($info['office_text'] ?? null))<p style="margin:0;">{{ $info['office_text'] }}</p>@endif
                            @if (filled($info['phone'] ?? null) || filled($info['email'] ?? null))
                                <p style="margin:6px 0 0;">{{ collect([$info['phone'] ?? null, $info['email'] ?? null])->filter()->implode(' · ') }}</p>
                            @endif
                            @if ($footer)<p style="margin:12px 0 0;">{{ $footer }}</p>@endif
                            <p style="margin:12px 0 0;"><a href="{{ $siteUrl }}" style="color:#D19A66;text-decoration:none;">{{ parse_url($siteUrl, PHP_URL_HOST) }} ↗</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
