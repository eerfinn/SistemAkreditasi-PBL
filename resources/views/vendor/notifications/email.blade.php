<x-mail::message>
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Terjadi Kesalahan!')
@else
# @lang('Halo!')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<div style="text-align: center; margin: 35px 0;">
    <x-mail::button :url="$actionUrl" :color="$color">
    {{ $actionText }}
    </x-mail::button>
</div>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Salam,'),<br>
<strong style="color: #055FC5;">Tim {{ config('app.name') }}</strong>
@endif

{{-- Info Panel --}}
<x-mail::panel>
<p style="text-align: center; font-size: 13px; color: #718096;">
<strong>Perlu bantuan?</strong> Jika Anda memiliki pertanyaan atau kesulitan, silakan hubungi kami di <a href="mailto:sistemakreditasi@gmail.com">sistemakreditasi@gmail.com</a>
</p>
</x-mail::panel>

{{-- Social Media Icons --}}
<div style="text-align: center; margin-top: 25px;">
    <p style="color: #718096; font-size: 13px; margin-bottom: 10px;">Ikuti kami di media sosial</p>
    <div class="social-icons">
        <a href="https://www.facebook.com/polinema" style="display: inline-block; margin: 0 5px;">
            <img src="https://cdn-icons-png.flaticon.com/128/5968/5968764.png" alt="Facebook" width="24" height="24">
        </a>
        <a href="https://www.instagram.com/polinema_campus/" style="display: inline-block; margin: 0 5px;">
            <img src="https://cdn-icons-png.flaticon.com/128/3955/3955024.png" alt="Instagram" width="24" height="24">
        </a>
        <a href="https://www.linkedin.com/school/polinema-joss/" style="display: inline-block; margin: 0 5px;">
            <img src="https://cdn-icons-png.flaticon.com/128/3536/3536505.png" alt="LinkedIn" width="24" height="24">
        </a>
    </div>
</div>

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
@lang(
    "Jika Anda mengalami kesulitan mengklik tombol \":actionText\", salin dan tempel URL di bawah ini\n".
    'ke browser Anda:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>
