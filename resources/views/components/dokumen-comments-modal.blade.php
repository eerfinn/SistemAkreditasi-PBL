@props(['dokumen', 'dokumenComments', 'commentCount'])

<!-- Comments Modal -->
<div class="modal fade" id="commentModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="commentModalLabel{{ $dokumen->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title text-truncate w-75" id="commentModalLabel{{ $dokumen->id }}">
                    <i class="fas fa-comments me-2"></i>Komentar untuk Dokumen: {{ $dokumen->nama_dokumen }} ({{ $commentCount }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if($dokumenComments->count() > 0)
                    @foreach($dokumenComments as $comment)
                    <div class="mb-4 shadow-sm p-3 rounded" style="background-color: #f8f9fa; border-left: 4px solid #6c757d;">
                        <div class="d-flex">
                            <div class="comment-avatar me-3">
                                <img src="{{ ($comment->user && $comment->user->photo) ? asset('storage/profile/' . $comment->user->photo) : asset('assets/images/avatar/1.png') }}" alt="{{ optional($comment->user)->nama ?? 'User' }}" class="rounded-circle border" width="45" height="45" style="object-fit: cover;">
                            </div>
                            <div class="w-100">
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 fw-bold">{{ $comment->user->nama ?? 'User' }}</h6>
                                    <span class="badge bg-secondary ms-2 rounded-pill">{{ $comment->user->role ?? 'unknown' }}</span>
                                    <div class="date-column ms-auto">
                                        <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $comment->created_at->format('d M Y, H:i') }}</small>
                                    </div>
                                </div>
                                <div class="comment-text mt-2">
                                    <p class="mb-0 text-start">{{ $comment->komentar }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="alert alert-info mb-0 d-flex align-items-center">
                        <i class="fas fa-info-circle me-3 fa-lg"></i>
                        <span>Belum ada komentar untuk dokumen ini.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
