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

        // Otorisasi menggunakan Gate (pastikan Gate 'upload-dokumen-kriteria' sudah didefinisikan)
        // Gate::authorize('upload-dokumen-kriteria', $kriteria);

        $ppepp_stages = [
            Dokumen::PPEPP_PENETAPAN,
            Dokumen::PPEPP_PELAKSANAAN,
            Dokumen::PPEPP_EVALUASI,
            Dokumen::PPEPP_PENGENDALIAN,
            Dokumen::PPEPP_PENINGKATAN
        ];
        $berhasilDiproses = false;
        $folderKriteriaUser = 'dokumen_akreditasi/kriteria_' . $kriteriaId . '/user_' . $user->id;

        foreach ($ppepp_stages as $stage) {
            $fileInputName = "dokumen.{$stage}";
            $deskripsiInputName = "deskripsi.{$stage}";

            if ($request->hasFile($fileInputName) || $request->filled($deskripsiInputName)) {
                $path = null;
                $originalNameForDisplay = "Dokumen " . ucfirst($stage);

                if ($request->hasFile($fileInputName)) {
                    $file = $request->file($fileInputName);
                    $originalNameForDisplay = $file->getClientOriginalName();
                    $fileNameToStore = time() . '_' . $stage . '_' . Str::slug(pathinfo($originalNameForDisplay, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs($folderKriteriaUser, $fileNameToStore, 'public');
                }

                $namaDokumenDiDB = pathinfo($originalNameForDisplay, PATHINFO_FILENAME) . " (" . ucfirst($stage) . ")";

                $existingDraft = Dokumen::where('user_id', $user->id)
                                        ->where('kriteria_id', $kriteriaId)
                                        ->where('jenis_ppepp', $stage)
                                        ->where('status', Dokumen::STATUS_DRAFT)
                                        ->first();

                if ($existingDraft) {
                    if ($path && $existingDraft->path && Storage::disk('public')->exists($existingDraft->path)) {
                        Storage::disk('public')->delete($existingDraft->path);
                    }
                    $existingDraft->update([
                        'nama_dokumen' => $namaDokumenDiDB,
                        'path' => $path ?? $existingDraft->path,
                        'deskripsi_dokumen' => $request->input($deskripsiInputName, $existingDraft->deskripsi_dokumen),
                    ]);
                    Log::info('Dokumen draft diperbarui', ['dokumen_id' => $existingDraft->id, 'path' => $existingDraft->path]);
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
                    Log::info('Dokumen draft baru dibuat', ['dokumen_id' => $newDokumen->id, 'path' => $path]);
                }
                $berhasilDiproses = true;
            }
        }

        if ($berhasilDiproses) {
            return redirect()->route('kriteria.show', $kriteriaId)
                             ->with('success', 'Perubahan dokumen draft berhasil disimpan.');
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
        // Logika untuk menampilkan detail satu dokumen
        // Gate::authorize('view-dokumen', $dokumen);
        // return view('pages.dokumen.show', compact('dokumen'));
        if ($dokumen->path && Storage::disk('public')->exists($dokumen->path)) {
            return Storage::disk('public')->response($dokumen->path);
        }
        return back()->with('error', 'File dokumen tidak ditemukan.');
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
}
