@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@elseif (trim($slot) === 'SistemAkreditasi')
<div style="display: flex; align-items: center; justify-content: center;">
    <span style="font-size: 24px; font-weight: bold; color: #055FC5;">Sistem Akreditasi</span>
</div>
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
