@props(['title', 'description', 'documents', 'ppepp_key', 'ppepp_descriptions', 'is_validation_view' => false])

<div class="col-xl-12 mb-4">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
            <p class="mb-2 mt-1">{{ $description }}</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Dokumen</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents ?? [] as $dokumen)
                        <tr>
                            <td>
                                {{ $dokumen->nama_dokumen }}
                                <span class="small d-block text-muted">ID: {{ $dokumen->id }}</span>
                            </td>
                            <td>
                                <x-dokumen-status-badge :status="$dokumen->status" />
                            </td>
                            <td class="text-end">
                                @if($dokumen->path)
                                    @php
                                        // Get document comments
                                        $dokumenComments = \App\Models\Komen::where('dokumen_id', $dokumen->id)
                                            ->with('user')
                                            ->orderBy('created_at', 'desc')
                                            ->get();
                                        $commentCount = $dokumenComments->count();
                                    @endphp
                                    
                                    <!-- View button -->
                                    <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Comments button -->
                                    @if($commentCount > 0)
                                        <button type="button" class="btn btn-secondary btn-xs sharp me-1" title="Lihat {{ $commentCount }} Komentar" data-bs-toggle="modal" data-bs-target="#commentModal{{ $dokumen->id }}">
                                            <i class="fas fa-comments"></i>
                                            <span class="badge bg-danger text-white position-absolute" style="font-size: 8px; top: -5px; right: -5px;">{{ $commentCount }}</span>
                                        </button>
                                    @endif

                                    <!-- Revision button - only show in non-validation view -->
                                    @if(!$is_validation_view && auth()->user() && (auth()->user()->role === 'dosen' || auth()->user()->role === 'administrator') && $dokumen->status === 'revisi')
                                        <button type="button" class="btn btn-warning btn-xs sharp me-1" title="Upload Revisi" data-bs-toggle="modal" data-bs-target="#revisiModal{{ $dokumen->id }}">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    @endif

                                    <!-- Validation button -->
                                    @if(auth()->user() && in_array(auth()->user()->role, ['administrator', 'koordinator']))
                                        <button type="button" class="btn btn-primary btn-xs sharp me-1" title="Validasi" data-bs-toggle="modal" data-bs-target="#validasiModal{{ $dokumen->id }}">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    @endif
                                    
                                    @if($commentCount > 0)
                                        <x-dokumen-comments-modal :dokumen="$dokumen" :dokumenComments="$dokumenComments" :commentCount="$commentCount" />
                                    @endif
                                    
                                    @if(!$is_validation_view && auth()->user() && (auth()->user()->role === 'dosen' || auth()->user()->role === 'administrator') && $dokumen->status === 'revisi')
                                        <x-dokumen-revisi-modal :dokumen="$dokumen" />
                                    @endif
                                    
                                    @if(auth()->user() && in_array(auth()->user()->role, ['administrator', 'koordinator']))
                                        <x-dokumen-validasi-modal :dokumen="$dokumen" />
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">Belum ada dokumen untuk {{ $title }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <td colspan="3">
                                <strong>Deskripsi:</strong>
                                <p class="mb-0 mt-1">{{ isset($ppepp_descriptions[$ppepp_key]) ? $ppepp_descriptions[$ppepp_key] : '-' }}</p>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div> 