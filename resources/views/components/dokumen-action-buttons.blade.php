@props(['dokumen', 'commentCount'])

<a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
    <i class="fas fa-eye"></i>
</a>

@if($commentCount > 0)
    <button type="button" class="btn btn-secondary btn-xs sharp me-1" title="Lihat {{ $commentCount }} Komentar" data-bs-toggle="modal" data-bs-target="#commentModal{{ $dokumen->id }}">
        <i class="fas fa-comments"></i>
        @if($commentCount > 0)
            <span class="badge bg-danger text-white position-absolute" style="font-size: 8px; top: -5px; right: -5px;">{{ $commentCount }}</span>
        @endif
    </button>
@endif

@if(auth()->user() && (auth()->user()->role === 'dosen' || auth()->user()->role === 'administrator') && $dokumen->status === 'revisi')
    <button type="button" class="btn btn-warning btn-xs sharp me-1" title="Upload Revisi" data-bs-toggle="modal" data-bs-target="#revisiModal{{ $dokumen->id }}">
        <i class="fas fa-upload"></i>
    </button>
@endif

@if(auth()->user() && in_array(auth()->user()->role, ['administrator', 'koordinator']))
    <button type="button" class="btn btn-primary btn-xs sharp me-1" title="Validasi" data-bs-toggle="modal" data-bs-target="#validasiModal{{ $dokumen->id }}">
        <i class="fas fa-check-double"></i>
    </button>
@endif 