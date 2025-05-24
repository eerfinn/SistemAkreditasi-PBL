@props(['dokumen', 'dokumenComments', 'commentCount'])

<!-- Comments Modal -->
<div class="modal fade" id="commentModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="commentModalLabel{{ $dokumen->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-truncate w-75" id="commentModalLabel{{ $dokumen->id }}">Komentar untuk Dokumen: {{ $dokumen->nama_dokumen }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($dokumenComments->count() > 0)
                    @foreach($dokumenComments as $comment)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-xs me-2">
                                <span class="avatar-initial rounded-circle bg-primary">
                                    {{ substr($comment->user->name ?? 'U', 0, 1) }}
                                </span>
                            </div>
                            <h6 class="mb-0">{{ $comment->user->name ?? 'User' }}</h6>
                            <span class="badge bg-secondary ms-2">{{ $comment->user->role ?? 'unknown' }}</span>
                            <small class="text-muted ms-auto">{{ $comment->created_at->format('d M Y H:i') }}</small>
                        </div>
                        <p class="mb-0 ps-4">{{ $comment->komentar }}</p>
                    </div>
                    @endforeach
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> Belum ada komentar untuk dokumen ini.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div> 