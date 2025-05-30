    @props(['dokumen'])

<!-- Validation Modal -->
<div class="modal fade" id="validasiModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="validasiModalLabel{{ $dokumen->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="validasiModalLabel{{ $dokumen->id }}"><i class="fas fa-check-double me-2"></i>Validasi Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('validasi.update-status', $dokumen->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-start d-block fw-bold">Dokumen: <strong>{{ $dokumen->nama_dokumen }}</strong></label>
                        <p class="small text-muted mb-3 text-start">ID: {{ $dokumen->id }} | Status Saat Ini: <x-dokumen-status-badge :status="$dokumen->status" /></p>
                        
                        <div class="form-check mb-3 d-flex justify-content-between align-items-center">
                            <label class="form-check-label" for="status_revisi{{ $dokumen->id }}">
                                <span class="badge light badge-danger">Revisi</span> - Dokumen perlu direvisi
                            </label>
                            <input class="form-check-input" type="radio" name="status" id="status_revisi{{ $dokumen->id }}" value="revisi" required {{ $dokumen->status == 'revisi' ? 'checked' : '' }}>
                        </div>
                        <div class="form-check mb-3 d-flex justify-content-between align-items-center">
                            <label class="form-check-label" for="status_diverifikasi{{ $dokumen->id }}">
                                <span class="badge light badge-primary">Terverifikasi</span> - Dokumen terverifikasi final
                            </label>
                            <input class="form-check-input" type="radio" name="status" id="status_diverifikasi{{ $dokumen->id }}" value="diverifikasi" {{ $dokumen->status == 'diverifikasi' ? 'checked' : '' }}>
                        </div>
                    </div>
                    
                    <div class="mb-3 text-start">
                        <label for="komentar{{ $dokumen->id }}" class="form-label fw-bold">Komentar untuk Dokumen ini:</label>
                        <textarea class="form-control" id="komentar{{ $dokumen->id }}" name="komentar" rows="3" placeholder="Berikan komentar atau masukan untuk dokumen ini..."></textarea>
                        <small class="text-muted">Komentar ini khusus untuk dokumen yang sedang divalidasi.</small>
                    </div>
                    
                    <div class="mb-3 text-start">
                        <label for="kriteria_comment{{ $dokumen->id }}" class="form-label fw-bold">Komentar untuk Kriteria Secara Keseluruhan:</label>
                        <textarea class="form-control" id="kriteria_comment{{ $dokumen->id }}" name="kriteria_comment" rows="3" placeholder="Berikan komentar untuk kriteria secara keseluruhan (opsional)..."></textarea>
                        <small class="text-muted text-wrap">Komentar ini akan ditampilkan pada bagian komentar kriteria dan dapat dilihat oleh semua pengguna.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Validasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 