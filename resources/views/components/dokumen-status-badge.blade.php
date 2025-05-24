@props(['status'])

@php
$badgeClass = match($status) {
    'draft' => 'light badge-info',
    'menunggu' => 'light badge-warning',
    'revisi' => 'light badge-danger',
    'diverifikasi' => 'light badge-primary',
    'diterima' => 'light badge-success',
    default => 'light badge-secondary'
};

$statusLabel = match($status) {
    'draft' => 'Draft',
    'menunggu' => 'Menunggu',
    'revisi' => 'Revisi',
    'diverifikasi' => 'Terverifikasi',
    'diterima' => 'Diterima',
    default => ucfirst($status)
};
@endphp

<span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span> 