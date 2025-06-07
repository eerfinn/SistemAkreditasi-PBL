@props(['status'])

@php
    $statusClasses = [
        'draft' => 'badge-secondary',
        'menunggu' => 'badge-warning',
        'menunggu_direktur' => 'badge-info',
        'revisi' => 'badge-danger',
        'diverifikasi' => 'badge-success'
    ];

    $statusLabels = [
    'draft' => 'Draft',
        'menunggu' => 'Menunggu Validasi',
        'menunggu_direktur' => 'Menunggu Validasi Direktur',
        'revisi' => 'Perlu Revisi',
        'diverifikasi' => 'Terverifikasi'
    ];

    $class = $statusClasses[$status] ?? 'badge-secondary';
    $label = $statusLabels[$status] ?? ucfirst($status);
@endphp

<span class="badge light {{ $class }}">{{ $label }}</span> 