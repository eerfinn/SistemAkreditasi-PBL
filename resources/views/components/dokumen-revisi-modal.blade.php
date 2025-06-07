@props(['dokumen'])

<!-- Revision Upload Modal -->
<div class="modal fade" id="revisiModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="revisiModalLabel{{ $dokumen->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="revisiModalLabel{{ $dokumen->id }}"><i class="fas fa-sync-alt me-2"></i>Upload Revisi Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dokumen.submit.revision', $dokumen->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dokumen yang Direvisi:</label>
                        <p class="mb-1"><strong>{{ $dokumen->nama_dokumen }}</strong></p>
                        <small class="text-muted">Status saat ini: <span class="badge bg-warning">Perlu Revisi</span></small>
                    </div>

                    <div class="mb-3">
                        <label for="file{{ $dokumen->id }}" class="form-label fw-bold">File Revisi:</label>
                        <input type="file" class="form-control" id="file{{ $dokumen->id }}" name="file" required>
                        <small class="text-muted">Format: PDF, Word, Excel, PowerPoint. Maks: 5MB</small>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <a href="{{ route('dokumen.destroy', $dokumen->id) }}" 
                           class="btn btn-danger"
                           onclick="event.preventDefault();
                                    if(confirm('Yakin ingin menghapus dokumen revisi ini?')) {
                                        document.getElementById('delete-form-{{ $dokumen->id }}').submit();
                                    }">
                                <i class="fas fa-trash me-1"></i> Hapus Dokumen
                        </a>
                    </div>
                    <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Upload Revisi
                    </button>
                    </div>
                </div>
            </form>
            
            <!-- Separate form for delete action -->
            <form id="delete-form-{{ $dokumen->id }}" action="{{ route('dokumen.destroy', $dokumen->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>
