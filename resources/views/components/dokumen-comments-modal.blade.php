@props(['dokumen', 'dokumenComments', 'commentCount'])

<!-- Comments Modal -->
<div class="modal fade" id="commentModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="commentModalLabel{{ $dokumen->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title text-truncate w-75" id="commentModalLabel{{ $dokumen->id }}">
                    <i class="fas fa-comments me-2"></i>Komentar untuk Dokumen: {{ $dokumen->nama_dokumen }} ({{ $commentCount }})
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($dokumenComments->count() > 0)
                    @foreach($dokumenComments as $comment)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="comment-avatar me-2">
                                <img src="{{ ($comment->user && $comment->user->photo) ? asset('storage/profile/' . $comment->user->photo) : asset('assets/images/avatar/1.png') }}" alt="{{ optional($comment->user)->nama ?? 'User' }}" class="rounded-circle" width="40" height="40">
                            </div>
                            <h6 class="mb-0">{{ $comment->user->nama ?? 'User' }}</h6>
                            <span class="badge bg-secondary ms-2">{{ $comment->user->role ?? 'unknown' }}</span>
                            <div class="date-column ms-auto text-end">
                                <div>{{ $comment->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $comment->created_at->format('H:i') }}</small>
                            </div>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div> 