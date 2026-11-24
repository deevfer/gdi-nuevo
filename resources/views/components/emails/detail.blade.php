@props(['label'])
<tr>
    <td style="padding:14px 20px;border-bottom:1px solid #E5E0D8;">
        <p style="margin:0 0 4px;color:#8A7F72;font-size:10px;line-height:16px;letter-spacing:2px;text-transform:uppercase;">{{ $label }}</p>
        <div style="color:#2A374A;font-size:15px;line-height:23px;overflow-wrap:anywhere;word-break:break-word;">{{ $slot }}</div>
    </td>
</tr>
