@props(['title', 'description', 'documents', 'ppepp_key', 'ppepp_descriptions', 'is_validation_view' => false])

<div class="col-xl-12 mb-4">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">{{ $title }}</h4>
            </div>
            <p class="mb-2 mt-1">{{ $description }}</p>
        </div>
        <div class="card-body">
            <div class="mb-3 p-3 bg-light rounded">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    <strong>Deskripsi:</strong>
                </div>
                <p class="mb-0">{{ $ppepp_descriptions[$ppepp_key] ?? '-' }}</p>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 35%" class="nama-dokumen-cell text-center">Nama Dokumen</th>
                            <th style="width: 15%">Pemilik</th>
                            <th style="width: 15%">Status</th>
                            <th style="width: 15%">Tanggal</th>
                            <th style="width: 15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents ?? [] as $index => $dokumen)
                        <tr class="table-row">
                            <td>{{ $index + 1 }}</td>
                            <td class="nama-dokumen-cell">
                                {{ $dokumen->nama_dokumen }}
                            </td>
                            <td>
                                {{ $dokumen->user->nama ?? 'Unknown' }}
                            </td>
                            <td>
                                <x-dokumen-status-badge :status="$dokumen->status" />
                            </td>
                            <td class="date-column">
                                {{ \Carbon\Carbon::parse($dokumen->created_at)->format('d M Y') }}
                            </td>
                            <td class="text-center">
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
                                        <button type="button" class="btn btn-secondary btn-xs sharp me-1" title="Lihat Komentar" data-bs-toggle="modal" data-bs-target="#commentModal{{ $dokumen->id }}">
                                            <i class="fas fa-comments"></i>
                                        </button>
                                    @endif

                                    <!-- Revision button - only show in non-validation view -->
                                    @if(!$is_validation_view && $dokumen->status === 'revisi')
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

                                    @if(!$is_validation_view && $dokumen->status === 'revisi')
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
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-folder-open me-2 fa-2x"></i><br>
                                    Belum ada dokumen untuk {{ $title }}
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
