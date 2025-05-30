@props(['status'])

@php
$badgeClass = match($status) {
    'draft' => 'light badge-info',
    'menunggu' => 'light badge-warning',
    'revisi' => 'light badge-danger',
    'diverifikasi' => 'light badge-primary',
    default => 'light badge-secondary'
};

$statusLabel = match($status) {
    'draft' => 'Draft',
    'menunggu' => 'Menunggu',
    'revisi' => 'Revisi',
    'diverifikasi' => 'Terverifikasi',
    default => ucfirst($status)
};
@endphp

<span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span> 