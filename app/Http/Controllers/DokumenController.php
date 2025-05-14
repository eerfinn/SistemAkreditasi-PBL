<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id',
            'dokumen' => 'sometimes|array',
            'dokumen.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120', // Max 5MB
            'deskripsi' => 'sometimes|array',
            'deskripsi.*' => 'nullable|string|max:2000',
        ]);

        $kriteriaId = $request->kriteria_id;
        $user = Auth::user();
        $kriteria = Kriteria::find($kriteriaId);

        if (!$kriteria) {
            return back()->with('error', 'Kriteria tidak ditemukan.')->withInput();
        }

        $ppepp_stages = [
            Dokumen::PPEPP_PENETAPAN,
            Dokumen::PPEPP_PELAKSANAAN,
            Dokumen::PPEPP_EVALUASI,
            Dokumen::PPEPP_PENGENDALIAN,
            Dokumen::PPEPP_PENINGKATAN
        ];
        $berhasilDiproses = false;

        foreach ($ppepp_stages as $stage) {
            $fileInputName = "dokumen.{$stage}";
            $deskripsiInputName = "deskripsi.{$stage}";

            if ($request->hasFile($fileInputName) || $request->filled($deskripsiInputName)) {
                $path = null;
                $originalNameForDisplay = "Dokumen " . ucfirst($stage);

                if ($request->hasFile($fileInputName)) {
                    $file = $request->file($fileInputName);
                    $originalNameForDisplay = $file->getClientOriginalName();

                    // Buat struktur folder yang lebih terorganisir
                    $folderPath = "dokumen_akreditasi/kriteria_{$kriteriaId}/user_{$user->id}";

                    // Generate nama file yang unik
                    $fileNameToStore = time() . '_' . $stage . '_' . Str::slug(pathinfo($originalNameForDisplay, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

                    // Simpan file dengan path yang baru
                    $path = $file->storeAs($folderPath, $fileNameToStore, 'public');

                    if (!$path) {
                        Log::error('Gagal menyimpan file', [
                            'original_name' => $originalNameForDisplay,
                            'stage' => $stage,
                            'user_id' => $user->id,
                            'kriteria_id' => $kriteriaId
                        ]);
                        continue;
                    }
                }

                $namaDokumenDiDB = pathinfo($originalNameForDisplay, PATHINFO_FILENAME) . " (" . ucfirst($stage) . ")";

                // Cek dokumen yang ada (baik draft maupun revisi)
                $existingDokumen = Dokumen::where('user_id', $user->id)
                                        ->where('kriteria_id', $kriteriaId)
                                        ->where('jenis_ppepp', $stage)
                                        ->whereIn('status', [Dokumen::STATUS_DRAFT, Dokumen::STATUS_REVISI])
                                        ->first();

                if ($existingDokumen) {
                    // Hapus file lama jika ada
                    if ($path && $existingDokumen->path && Storage::disk('public')->exists($existingDokumen->path)) {
                        Storage::disk('public')->delete($existingDokumen->path);
                    }

                    $existingDokumen->update([
                        'nama_dokumen' => $namaDokumenDiDB,
                        'path' => $path ?? $existingDokumen->path,
                        'deskripsi_dokumen' => $request->input($deskripsiInputName, $existingDokumen->deskripsi_dokumen),
                        'status' => Dokumen::STATUS_DRAFT, // Reset status ke draft
                    ]);

                    Log::info('Dokumen diperbarui', [
                        'dokumen_id' => $existingDokumen->id,
                        'path' => $existingDokumen->path,
                        'status' => $existingDokumen->status
                    ]);
                } else {
                    $newDokumen = Dokumen::create([
                        'user_id' => $user->id,
                        'kriteria_id' => $kriteriaId,
                        'nama_dokumen' => $namaDokumenDiDB,
                        'path' => $path,
                        'jenis_ppepp' => $stage,
                        'deskripsi_dokumen' => $request->input($deskripsiInputName),
                        'status' => Dokumen::STATUS_DRAFT,
                    ]);

                    Log::info('Dokumen baru dibuat', [
                        'dokumen_id' => $newDokumen->id,
                        'path' => $path
                    ]);
                }
                $berhasilDiproses = true;
            }
        }

        if ($berhasilDiproses) {
            return redirect()->route('kriteria.show', $kriteriaId)
                             ->with('success', 'Perubahan dokumen berhasil disimpan. Silakan finalisasi dokumen jika sudah selesai.');
        } else {
            return back()->with('info', 'Tidak ada file atau deskripsi yang diunggah/diperbarui.');
        }
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
                $filePath = Storage::disk('public')->path($dokumen->path);

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
                'full_path' => Storage::disk('public')->path($dokumen->path)
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
        // Logika untuk memperbarui dokumen yang sudah ada (mungkin untuk revisi dokumen final)
        // Gate::authorize('update-dokumen-final', $dokumen);
        // Validasi dan logika update...
        return redirect()->route('dashboard'); // Atau halaman lain yang sesuai
    }

    public function destroy(Dokumen $dokumen)
    {
        // Logika untuk menghapus dokumen final (mungkin hanya admin)
        // Gate::authorize('delete-dokumen-final', $dokumen);
        // $kriteriaId = $dokumen->kriteria_id;
        // $dokumen->delete(); // File fisik akan terhapus oleh event di model
        // return redirect()->route('kriteria.show', $kriteriaId)->with('success', 'Dokumen berhasil dihapus.');
        return redirect()->route('dashboard'); // Atau halaman lain yang sesuai
    }

    public function finalisasiAll($kriteria_id)
    {
        try {
            // Ambil semua dokumen draft untuk kriteria ini
            $dokumenDrafts = Dokumen::where('kriteria_id', $kriteria_id)
                ->where('status', Dokumen::STATUS_DRAFT)
                ->get();

            if ($dokumenDrafts->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada dokumen draft yang dapat difinalisasi.');
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
}
