@props(['dokumen', 'commentCount'])

<a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
    <i class="fas fa-eye"></i>
</a>

@if($commentCount > 0)
    <button type="button" class="btn btn-secondary btn-xs sharp me-1" title="Lihat Komentar" data-bs-toggle="modal" data-bs-target="#commentModal{{ $dokumen->id }}">
        <i class="fas fa-comments"></i>
    </button>
@endif

@if(auth()->user() && ($dokumen->user_id == auth()->user()->id || auth()->user()->role === 'administrator') && $dokumen->status === 'revisi')
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

@if(auth()->user() && in_array(auth()->user()->role, ['administrator', 'koordinator']))
    <button type="button" class="btn btn-primary btn-xs sharp me-1" title="Validasi" data-bs-toggle="modal" data-bs-target="#validasiModal{{ $dokumen->id }}">
        <i class="fas fa-check-double"></i>
    </button>
@endif
