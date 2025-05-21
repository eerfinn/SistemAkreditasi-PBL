<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\History;

class DokumenController extends Controller
{
    public function __construct()
    {
        // Menerapkan middleware auth ke semua method kecuali yang mungkin publik
        // Sesuaikan 'except' jika ada method yang tidak memerlukan autentikasi
        $this->middleware('auth');
        // Anda bisa menambahkan middleware role di sini atau di route
        // $this->middleware('role:dosen')->only(['create', 'store', 'destroyDraft']);
    }

    /**
     * Menampilkan form untuk mengunggah/memperbarui dokumen PPEPP untuk kriteria tertentu.
     * Ini mirip dengan KriteriaController@uploadForm. Anda bisa memilih salah satu.
     */
    public function create(Kriteria $kriteria)
    {
        $user = Auth::user();
        // Otorisasi: Pastikan user yang login berhak mengunggah untuk kriteria ini
        // Anda bisa menggunakan Gate di sini atau di route
        // Gate::authorize('upload-dokumen-kriteria', $kriteria);

        $existingDraftsArray = Dokumen::where('user_id', $user->id)
                                    ->where('kriteria_id', $kriteria->id)
                                    ->where('status', Dokumen::STATUS_DRAFT)
                                    ->get()
                                    ->keyBy('jenis_ppepp')
                                    ->toArray();

        $dokumenRevisi = Dokumen::where('user_id', $user->id)
                                ->where('kriteria_id', $kriteria->id)
                                ->where('status', Dokumen::STATUS_REVISI)
                                ->get()
                                ->keyBy('jenis_ppepp');

        // Asumsi view form Anda ada di 'pages.dokumen.create'
        // atau 'pages.kriteria.upload-kriteria.form'
        if (!view()->exists('pages.kriteria.upload-kriteria.form')) {
            // Fallback atau error jika view tidak ditemukan
            abort(404, "View untuk form upload dokumen tidak ditemukan.");
        }
        return view('pages.kriteria.upload-kriteria.form', compact('kriteria', 'existingDraftsArray', 'dokumenRevisi'));
    }

    /**
     * Menyimpan dokumen yang baru diunggah atau memperbarui dokumen draft.
     * Menangani array file dan deskripsi dari form PPEPP.
     */
    public function store(Request $request)
    {
        // Log request information
        Log::info('Document upload request details', [
            'request_data' => $request->except(['files']), // Don't log binary file data
            'has_files' => $request->hasFile('files'),
            'route' => $request->route()->getName()
        ]);

        // Validate common fields
        $validatedData = $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id',
            'jenis_ppepp' => 'required|string',
            'files.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120',
        ]);

        $kriteriaId = $request->kriteria_id;
        $jenisPpepp = $request->jenis_ppepp;
        $user = Auth::user();
        $kriteria = Kriteria::find($kriteriaId);

        if (!$kriteria) {
            Log::error('Kriteria not found', ['kriteria_id' => $kriteriaId]);
            return back()->with('error', 'Kriteria tidak ditemukan.')->withInput();
        }

        // Handle file uploads
        if ($request->hasFile('files')) {
            $uploadedCount = 0;
            $folderPath = "dokumen_akreditasi/kriteria_{$kriteriaId}/{$jenisPpepp}/user_{$user->id}";
            
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $originalNameForDisplay = $file->getClientOriginalName();
            
            Log::info('Processing file upload', [
                'original_name' => $originalNameForDisplay,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);
            
            $fileNameToStore = time() . '_' . Str::slug(pathinfo($originalNameForDisplay, PATHINFO_FILENAME)) 
                            . '.' . $file->getClientOriginalExtension();

            try {
                $path = $file->storeAs($folderPath, $fileNameToStore, 'public');
                
                if (!$path) {
                    Log::error('Failed to save file', [
                                'original_name' => $originalNameForDisplay
                    ]);
                            continue;
                }

                Log::info('File stored successfully', [
                            'path' => $path
                ]);

                $namaDokumenDiDB = pathinfo($originalNameForDisplay, PATHINFO_FILENAME);

                        // Create new document for each file
                    $newDokumen = Dokumen::create([
                        'user_id' => $user->id,
                        'kriteria_id' => $kriteriaId,
                        'nama_dokumen' => $namaDokumenDiDB,
                        'path' => $path,
                        'jenis_ppepp' => $jenisPpepp,
                        'status' => Dokumen::STATUS_DRAFT,
                    ]);
                    
                    Log::info('New document created', [
                        'dokumen_id' => $newDokumen->id
                    ]);

                        $uploadedCount++;
            } catch (\Exception $e) {
                Log::error('Exception during file upload', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                    }
                }
            }
            
            if ($uploadedCount > 0) {
                return redirect()->back()->with('success', "{$uploadedCount} dokumen berhasil diunggah.");
            } else {
                return back()->with('error', 'Tidak ada file yang berhasil diunggah.')->withInput();
            }
        }

        return back()->with('info', 'Tidak ada file yang dipilih untuk diunggah.')->withInput();
    }

    /**
     * Menghapus dokumen yang masih berstatus draft.
     */
    public function destroyDraft(Dokumen $dokumen)
    {
        // Otorisasi: Pastikan user yang menghapus adalah pemilik dan statusnya draft
        // Anda bisa menggunakan Gate di sini: Gate::authorize('delete-draft-dokumen', $dokumen);
        if (Auth::id() !== $dokumen->user_id || $dokumen->status !== Dokumen::STATUS_DRAFT) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus dokumen ini atau dokumen sudah difinalisasi.');
        }

        $kriteriaId = $dokumen->kriteria_id;

        // File fisik akan otomatis terhapus oleh event 'deleting' di model Dokumen
        $dokumen->delete();
        Log::info('Dokumen draft dihapus', ['dokumen_id' => $dokumen->id]);

        return redirect()->route('kriteria.show', $kriteriaId)
                         ->with('success', 'Dokumen draft berhasil dihapus.');
    }

    // Placeholder untuk method resource lainnya
    public function index()
    {
        // Logika untuk menampilkan daftar semua dokumen (mungkin untuk admin)
        // $dokumens = Dokumen::with(['user', 'kriteria'])->latest()->paginate(15);
        // return view('pages.dokumen.index', compact('dokumens'));
        return redirect()->route('dashboard'); // Atau halaman lain yang sesuai
    }

    public function show(Dokumen $dokumen)
    {
        try {
            // Pastikan user memiliki akses ke dokumen ini
            if (Auth::id() !== $dokumen->user_id && !in_array(Auth::user()->role, ['administrator', 'koordinator', 'kps', 'kajur', 'kjm', 'kaprodi'])) {
                return back()->with('error', 'Anda tidak memiliki akses ke dokumen ini.');
            }

            // Periksa apakah path ada dan file ada di storage
            if (!$dokumen->path) {
                return back()->with('error', 'File dokumen tidak ditemukan (path kosong).');
            }

            // Coba akses file dari storage public
            if (Storage::disk('public')->exists($dokumen->path)) {
                $filePath = storage_path('app/public/' . $dokumen->path);

                // Tentukan content type berdasarkan ekstensi file
                $extension = pathinfo($dokumen->path, PATHINFO_EXTENSION);
                $contentType = match(strtolower($extension)) {
                    'pdf' => 'application/pdf',
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'xls' => 'application/vnd.ms-excel',
                    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'ppt' => 'application/vnd.ms-powerpoint',
                    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    default => 'application/octet-stream'
                };

                return response()->file($filePath, [
                    'Content-Type' => $contentType,
                    'Content-Disposition' => 'inline; filename="' . basename($dokumen->path) . '"'
                ]);
            }

            Log::error('File tidak ditemukan di storage', [
                'dokumen_id' => $dokumen->id,
                'path' => $dokumen->path,
                'full_path' => storage_path('app/public/' . $dokumen->path)
            ]);

            return back()->with('error', 'File dokumen tidak ditemukan di sistem.');
        } catch (\Exception $e) {
            Log::error('Error saat membuka file dokumen', [
                'dokumen_id' => $dokumen->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat membuka file: ' . $e->getMessage());
        }
    }

    public function edit(Dokumen $dokumen)
    {
        // Logika untuk menampilkan form edit dokumen (mungkin untuk dokumen final yang direvisi)
        // Gate::authorize('edit-dokumen-final', $dokumen);
        // $kriteria = $dokumen->kriteria;
        // return view('pages.dokumen.edit', compact('dokumen', 'kriteria'));
        return redirect()->route('dashboard'); // Atau halaman lain yang sesuai
    }

    public function update(Request $request, Dokumen $dokumen)
    {
        // Validasi akses
        if (Auth::id() !== $dokumen->user_id || !in_array($dokumen->status, [Dokumen::STATUS_DRAFT, Dokumen::STATUS_REVISI])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengubah dokumen ini.');
        }

        $request->validate([
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120',
            'deskripsi' => 'required|string|max:2000',
        ]);

        // Update deskripsi
        $dokumen->deskripsi_dokumen = $request->deskripsi;

        // Jika ada file baru
        if ($request->hasFile('dokumen') && $request->file('dokumen')->isValid()) {
            $file = $request->file('dokumen');
            $originalNameForDisplay = $file->getClientOriginalName();
            
            // Buat struktur folder
            $folderPath = "dokumen_akreditasi/kriteria_{$dokumen->kriteria_id}/{$dokumen->jenis_ppepp}/user_{$dokumen->user_id}";
            
            // Generate nama file yang unik
            $fileNameToStore = time() . '_' . Str::slug(pathinfo($originalNameForDisplay, PATHINFO_FILENAME)) 
                             . '.' . $file->getClientOriginalExtension();

            // Hapus file lama jika ada
            if ($dokumen->path && Storage::disk('public')->exists($dokumen->path)) {
                Storage::disk('public')->delete($dokumen->path);
            }

            // Simpan file baru
            $path = $file->storeAs($folderPath, $fileNameToStore, 'public');
            
            if ($path) {
                $dokumen->path = $path;
                $dokumen->nama_dokumen = pathinfo($originalNameForDisplay, PATHINFO_FILENAME);
            }
        }

        $dokumen->save();

        return redirect()->back()->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Dokumen $dokumen)
    {
        // Validasi akses
        if (Auth::id() !== $dokumen->user_id || !in_array($dokumen->status, [Dokumen::STATUS_DRAFT, Dokumen::STATUS_REVISI])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus dokumen ini.');
        }

        $kriteriaId = $dokumen->kriteria_id;

        // Hapus file fisik
        if ($dokumen->path && Storage::disk('public')->exists($dokumen->path)) {
            Storage::disk('public')->delete($dokumen->path);
        }

        // Hapus record dari database
        $dokumen->delete();

        return redirect()->route('kriteria.show', $kriteriaId)
                        ->with('success', 'Dokumen berhasil dihapus.');
    }

    public function finalisasiAll($kriteria_id)
    {
        try {
            // Ambil semua dokumen draft untuk kriteria ini
            $dokumenDrafts = Dokumen::where('kriteria_id', $kriteria_id)
                ->where('user_id', Auth::id())
                ->where('status', Dokumen::STATUS_DRAFT)
                ->get();

            if ($dokumenDrafts->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada dokumen draft yang dapat difinalisasi.');
            }

            // Pastikan semua tahapan PPEPP memiliki setidaknya satu dokumen draft
            $ppepp_stages = [
                Dokumen::PPEPP_PENETAPAN,
                Dokumen::PPEPP_PELAKSANAAN,
                Dokumen::PPEPP_EVALUASI,
                Dokumen::PPEPP_PENGENDALIAN,
                Dokumen::PPEPP_PENINGKATAN
            ];

            $dokumenPerPPEPP = [];
            foreach ($ppepp_stages as $stage) {
                $dokumenPerPPEPP[$stage] = $dokumenDrafts->where('jenis_ppepp', $stage)->count();
            }

            // Periksa apakah semua tahap memiliki setidaknya satu dokumen
            $missingStages = array_filter($dokumenPerPPEPP, function($count) {
                return $count === 0;
            });

            if (!empty($missingStages)) {
                $missingStageNames = array_map(function($stage) {
                    return ucfirst($stage);
                }, array_keys($missingStages));
                
                return redirect()->back()->with('error', 'Beberapa tahapan belum memiliki dokumen: ' . implode(', ', $missingStageNames) . '. Harap upload dokumen untuk semua tahapan sebelum finalisasi.');
            }

            // Update status semua dokumen menjadi menunggu
            foreach ($dokumenDrafts as $dokumen) {
                $dokumen->update([
                    'status' => Dokumen::STATUS_MENUNGGU
                ]);
            }

            return redirect()->back()->with('success', 'Semua dokumen draft berhasil difinalisasi dan status diubah menjadi Menunggu Validasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memfinalisasi dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Handle submission of document revisions.
     * This replaces the file in an existing document that needs revision.
     */
    public function submitRevision(Request $request, Dokumen $dokumen)
    {
        $user = Auth::user();
        
        // Validate that the user owns this document or is an admin, and document needs revision
        if ($user->id !== $dokumen->user_id && $user->role !== 'administrator') {
            return back()->with('error', 'Anda tidak memiliki izin untuk merevisi dokumen ini.');
        }
        
        if ($dokumen->status !== Dokumen::STATUS_REVISI) {
            return back()->with('error', 'Dokumen ini tidak dalam status revisi.');
        }
        
        // Validate the request
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120',
            'keterangan_revisi' => 'nullable|string|max:500',
        ]);
        
        try {
            // Get the uploaded file
            $file = $request->file('file');
            $originalNameForDisplay = $file->getClientOriginalName();
            
            // Create the folder structure
            $folderPath = "dokumen_akreditasi/kriteria_{$dokumen->kriteria_id}/{$dokumen->jenis_ppepp}/user_{$user->id}";
            
            // Generate a unique filename
            $fileNameToStore = time() . '_' . Str::slug(pathinfo($originalNameForDisplay, PATHINFO_FILENAME)) 
                            . '.' . $file->getClientOriginalExtension();
            
            // Delete the old file if it exists
            if ($dokumen->path && Storage::disk('public')->exists($dokumen->path)) {
                Storage::disk('public')->delete($dokumen->path);
                Log::info('Deleted old revision file', ['path' => $dokumen->path]);
            }
            
            // Store the new file
            $path = $file->storeAs($folderPath, $fileNameToStore, 'public');
            
            if (!$path) {
                throw new \Exception('Failed to save the file.');
            }
            
            // Update the document
            $dokumen->nama_dokumen = pathinfo($originalNameForDisplay, PATHINFO_FILENAME);
            $dokumen->path = $path;
            $dokumen->status = Dokumen::STATUS_MENUNGGU;
            if ($request->filled('keterangan_revisi')) {
                $dokumen->keterangan_revisi = $request->keterangan_revisi;
            }
            $dokumen->updated_at = now();
            $dokumen->save();
            
            // Record the history
            $history = new History();
            $history->user_id = $user->id;
            $history->dokumen_id = $dokumen->id;
            $history->aktivitas = "Mengupload revisi dokumen {$dokumen->nama_dokumen}";
            $history->save();
            
            Log::info('Document revision submitted successfully', [
                'dokumen_id' => $dokumen->id,
                'new_path' => $path
            ]);
            
            return redirect()->route('kriteria.show', $dokumen->kriteria_id)
                ->with('success', 'Revisi dokumen berhasil diunggah dan menunggu validasi.');
                
        } catch (\Exception $e) {
            Log::error('Error submitting document revision', [
                'dokumen_id' => $dokumen->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat mengunggah revisi: ' . $e->getMessage())
                ->withInput();
        }
    }
}
