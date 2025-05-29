<x-mail::layout>
{{-- Header (hidden since we're using banner) --}}
<x-slot:header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
<div style="text-align: center; margin-bottom: 10px;">
    <a href="{{ config('app.url') }}" style="text-decoration: none;">
        <strong style="color: #055FC5; font-size: 16px;">Sistem Akreditasi</strong>
    </a>
</div>
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
