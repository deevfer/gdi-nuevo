@props(['href'])
<table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:28px 0 0;">
    <tr><td style="background-color:#B68150;border-radius:100px;mso-padding-alt:14px 28px;">
        <a href="{{ $href }}" style="display:inline-block;padding:14px 28px;color:#FFFFFF;font-family:Helvetica,Arial,sans-serif;font-size:14px;text-decoration:none;">{{ $slot }} →</a>
    </td></tr>
</table>
