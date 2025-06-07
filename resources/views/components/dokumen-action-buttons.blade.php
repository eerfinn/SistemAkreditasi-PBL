@props(['dokumen', 'commentCount'])

@php
    $user = auth()->user();
    $isOwner = $user && $dokumen->user_id == $user->id;
    $isAdmin = $user && $user->role === 'administrator';
    $isKoordinator = $user && $user->role === 'koordinator';
    $isDirektur = $user && $user->role === 'direktur';
    
    // Tentukan apakah dokumen bisa divalidasi berdasarkan status dan peran
    $canKoordinatorValidate = $isKoordinator && 
                             in_array($dokumen->status, ['menunggu', 'revisi']) && 
                             ($dokumen->validator_level !== 'direktur' || $dokumen->validator_level === null) && 
                             $dokumen->status !== 'diverifikasi' &&
                             $dokumen->status !== 'menunggu_direktur';
                             
    $canDirektorValidate = $isDirektur && 
                          ($dokumen->status === 'menunggu_direktur' || 
                          ($dokumen->status === 'revisi' && $dokumen->validator_level === 'direktur')) &&
                          $dokumen->status !== 'diverifikasi';
                          
    $canValidate = $isAdmin || $canKoordinatorValidate || $canDirektorValidate;
    
    // Tentukan apakah dokumen bisa direvisi
    $canRevise = $isOwner && $dokumen->status === 'revisi';
@endphp

<a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
    <i class="fas fa-eye"></i>
</a>

@if($commentCount > 0)
    <button type="button" class="btn btn-secondary btn-xs sharp me-1" title="Lihat Komentar" data-bs-toggle="modal" data-bs-target="#commentModal{{ $dokumen->id }}">
        <i class="fas fa-comments"></i>
    </button>
@endif

@if($canRevise)
    <button type="button" class="btn btn-warning btn-xs sharp me-1" title="Upload Revisi" data-bs-toggle="modal" data-bs-target="#revisiModal{{ $dokumen->id }}">
        <i class="fas fa-upload"></i>
    </button>

    <form action="{{ route('dokumen.destroy', $dokumen->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus dokumen revisi ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-xs sharp me-1" title="Hapus Dokumen">
            <i class="fas fa-trash"></i>
        </button>
    </form>
@endif

@if($canValidate)
    <button type="button" class="btn btn-primary btn-xs sharp me-1" title="Validasi" data-bs-toggle="modal" data-bs-target="#validasiModal{{ $dokumen->id }}">
        <i class="fas fa-check-double"></i>
    </button>
@endif
