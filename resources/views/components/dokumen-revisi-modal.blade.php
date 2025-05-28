@props(['dokumen'])

<!-- Revision Upload Modal -->
<div class="modal fade" id="revisiModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="revisiModalLabel{{ $dokumen->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="revisiModalLabel{{ $dokumen->id }}">Upload Revisi Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dokumen.submit.revision', $dokumen->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Dokumen yang Direvisi:</label>
                        <p><strong>{{ $dokumen->nama_dokumen }}</strong></p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="file{{ $dokumen->id }}" class="form-label">File Revisi:</label>
                        <input type="file" class="form-control" id="file{{ $dokumen->id }}" name="file" required>
                        <small class="text-muted">Format: PDF, Word, Excel, PowerPoint. Maks: 5MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div> 