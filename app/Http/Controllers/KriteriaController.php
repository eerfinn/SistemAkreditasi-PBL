<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KriteriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('kriteria.access')->only(['show', 'uploadForm', 'finalisasiDokumen']);
    }

    public function show(Kriteria $kriteria)
    {
        $user = Auth::user();
        $dokumenPerPPEPP = [];
        $totalDrafts = 0;

        $ppepp_stages = [
            Dokumen::PPEPP_PENETAPAN,
            Dokumen::PPEPP_PELAKSANAAN,
            Dokumen::PPEPP_EVALUASI,
            Dokumen::PPEPP_PENGENDALIAN,
            Dokumen::PPEPP_PENINGKATAN
        ];

        // Debugging data retrieval
        Log::info('Retrieving documents for kriteria', [
            'kriteria_id' => $kriteria->id,
            'user_id' => $user ? $user->id : 'guest',
            'user_role' => $user ? $user->role : 'guest'
        ]);

        // Mengambil dokumen yang dikelompokkan per tahap PPEPP
        if ($user && in_array($user->role, ['dosen1', 'dosen2', 'dosen3', 'administrator'])) {
            foreach ($ppepp_stages as $stage) {
                // Important change: Include all documents for this user and kriteria,
                // regardless of status, to ensure validated documents remain visible
                // even when some need revision
                $query = Dokumen::where('kriteria_id', $kriteria->id)
                    ->where('user_id', $user->id)
                    ->where('jenis_ppepp', $stage)
                    ->whereNotNull('path') // Only get actual documents, not descriptions
                    ->orderByRaw("FIELD(status, '".Dokumen::STATUS_DRAFT."', '".Dokumen::STATUS_REVISI."',
                               '".Dokumen::STATUS_MENUNGGU."', '".Dokumen::STATUS_DITERIMA."',
                               '".Dokumen::STATUS_DIVERIFIKASI."') ASC")
                    ->orderBy('updated_at', 'desc');

                $dokumenCollection = $query->get();

                Log::info("Documents for stage {$stage}", [
                    'count' => $dokumenCollection->count(),
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings()
                ]);

                $dokumenPerPPEPP[$stage] = $dokumenCollection;
                $draftCount = $dokumenCollection->where('status', Dokumen::STATUS_DRAFT)->count();
                if ($draftCount > 0) {
                    $totalDrafts++;
                }
            }
        } else {
            foreach ($ppepp_stages as $stage) {
                $query = Dokumen::where('kriteria_id', $kriteria->id)
                    ->where('jenis_ppepp', $stage)
                    ->whereNotIn('status', [Dokumen::STATUS_DRAFT])
                    ->whereNotNull('path') // Only get actual documents, not descriptions
                    ->orderBy('updated_at', 'desc');

                $dokumenCollection = $query->get();

                Log::info("Documents for stage {$stage} (non-dosen)", [
                    'count' => $dokumenCollection->count(),
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings()
                ]);

                $dokumenPerPPEPP[$stage] = $dokumenCollection;
            }
        }

        // Load kriteria comments
        $kriteriaComments = \App\Models\Komen::where('kriteria_id', $kriteria->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        Log::info('Loaded kriteria comments', [
            'kriteria_id' => $kriteria->id,
            'comment_count' => $kriteriaComments->count()
        ]);

        // Log dokumen yang ditemukan untuk setiap tahap
        foreach ($ppepp_stages as $stage) {
            Log::info("Dokumen for {$stage}", [
                'count' => isset($dokumenPerPPEPP[$stage]) ? $dokumenPerPPEPP[$stage]->count() : 0,
                'dokumen_ids' => isset($dokumenPerPPEPP[$stage]) ? $dokumenPerPPEPP[$stage]->pluck('id')->toArray() : []
            ]);
        }

        $statusQueryBaseFinal = Dokumen::where('kriteria_id', $kriteria->id)
                                    ->where('status', '!=', Dokumen::STATUS_DRAFT);

        if ($user && in_array($user->role, ['dosen1', 'dosen2', 'dosen3'])) {
            $statusQueryBaseFinal->where('user_id', $user->id);
        }

        $statusCounts = [
            'menunggu'     => (clone $statusQueryBaseFinal)->where('status', Dokumen::STATUS_MENUNGGU)->count(),
            'revisi'       => (clone $statusQueryBaseFinal)->where('status', Dokumen::STATUS_REVISI)->count(),
            'diverifikasi' => (clone $statusQueryBaseFinal)->where('status', Dokumen::STATUS_DIVERIFIKASI)->count(),
        ];

        // Hitung semua dokumen draft
        $dokumenDrafts = new \Illuminate\Database\Eloquent\Collection();
        foreach ($dokumenPerPPEPP as $stageDokumen) {
            $dokumenDrafts = $dokumenDrafts->concat($stageDokumen->where('status', Dokumen::STATUS_DRAFT));
        }

        // Daftar dokumen final (non-draft) untuk semua tahap
        $daftarDokumenFinal = new \Illuminate\Database\Eloquent\Collection();
        foreach ($dokumenPerPPEPP as $stageDokumen) {
            $daftarDokumenFinal = $daftarDokumenFinal->concat($stageDokumen->where('status', '!=', Dokumen::STATUS_DRAFT));
        }

        if ($user && !in_array($user->role, ['dosen1', 'dosen2', 'dosen3'])) {
            $daftarDokumenFinal = Dokumen::where('kriteria_id', $kriteria->id)
                                ->where('status', '!=', Dokumen::STATUS_DRAFT)
                                ->whereNotNull('path') // Only get actual documents, not descriptions
                                ->with('user')
                                ->orderBy('jenis_ppepp')->orderBy('updated_at', 'desc')->get();
        }

        // Periksa apakah semua tahap PPEPP memiliki setidaknya satu dokumen draft
        $bisaFinalisasi = true;
        foreach ($ppepp_stages as $stage) {
            $hasStageDocuments = $dokumenPerPPEPP[$stage]->where('status', Dokumen::STATUS_DRAFT)->count() > 0;
            if (!$hasStageDocuments) {
                $bisaFinalisasi = false;
                break;
            }
        }

        // Get descriptions from kriteria table
        $ppepp_descriptions = json_decode($kriteria->ppepp_descriptions ?? '{}', true) ?: [];

        // Check if there are any draft documents
        $hasDraftDocuments = false;
        foreach($dokumenPerPPEPP as $stageDocs) {
            if(isset($stageDocs) && $stageDocs->where('status', \App\Models\Dokumen::STATUS_DRAFT)->count() > 0) {
                $hasDraftDocuments = true;
                break;
            }
        }

        // Check if there are documents needing revision
        $hasRevisionDocuments = isset($statusCounts) && ($statusCounts['revisi'] ?? 0) > 0;

        // Check if there are any documents at all in this kriteria
        $hasAnyDocuments = false;
        foreach($dokumenPerPPEPP as $stageDocs) {
            if(isset($stageDocs) && count($stageDocs) > 0) {
                $hasAnyDocuments = true;
                break;
            }
        }

        // Check if there are any finalized documents (menunggu/diverifikasi)
        $hasFinalizedDocuments = isset($statusCounts) &&
            (($statusCounts['menunggu'] ?? 0) > 0 ||
             ($statusCounts['diverifikasi'] ?? 0) > 0);

        // For dosen, we'll always allow them to manage documents
        // The old logic that disabled the button is removed
        $disableKelola = false;

        return view('pages.kriteria.kriteria', [
            'kriteria'         => $kriteria,
            'dokumenPerPPEPP'  => $dokumenPerPPEPP,
            'ppepp_stages'     => $ppepp_stages,
            'statusCounts'     => $statusCounts,
            'user'             => $user,
            'bisaFinalisasi'   => $bisaFinalisasi,
            'daftarDokumen'    => $daftarDokumenFinal,
            'dokumenDrafts'    => $dokumenDrafts,
            'showUploadButton' => $user && in_array($user->role, ['dosen1', 'dosen2', 'dosen3', 'administrator']),
            'ppepp_descriptions' => $ppepp_descriptions,
            'kriteriaComments' => $kriteriaComments,
            'disableKelola' => $disableKelola
        ]);
    }

    public function uploadForm(Kriteria $kriteria, Request $request)
    {
        $user = Auth::user();
        // Gate::authorize('upload-dokumen-kriteria', $kriteria);

        $selected_ppepp = $request->query('ppepp', null); // Default to null if not specified

        $ppepp_labels = [
            'penetapan' => 'C1. Penetapan',
            'pelaksanaan' => 'C2. Pelaksanaan',
            'evaluasi' => 'C3. Evaluasi',
            'pengendalian' => 'C4. Pengendalian',
            'peningkatan' => 'C5. Peningkatan'
        ];

        // Get existing documents for all PPEPP stages
        $dokumenPerPPEPP = [];
        foreach (array_keys($ppepp_labels) as $stage) {
            $query = Dokumen::where('kriteria_id', $kriteria->id)
                ->where('jenis_ppepp', $stage)
                ->whereNotNull('path') // Only get actual documents, not descriptions
                ->with('user'); // Eager load the user relationship

            // For dosen, only show their own documents
            // For admin, show all documents
            if (in_array($user->role, ['dosen1', 'dosen2', 'dosen3'])) {
                $query->where('user_id', $user->id);
            }

            $dokumenPerPPEPP[$stage] = $query->orderBy('updated_at', 'desc')->get();
        }

        // Prepare data for the new view format with the needed structure for navigation
        $allPpeppStagesWithData = [];
        foreach ($ppepp_labels as $key => $label) {
            $allPpeppStagesWithData[] = [
                'key' => $key,
                'label' => $label,
                'route_kelola_tahap_ini' => route('kriteria.upload.form', ['kriteria' => $kriteria->id, 'ppepp' => $key])
            ];
        }

        // Get descriptions for each PPEPP stage from kriteria table
        $ppepp_descriptions = json_decode($kriteria->ppepp_descriptions ?? '{}', true) ?: [];

        // Common data array for both views
        $viewData = [
            'kriteria' => $kriteria,
            'selected_ppepp' => $selected_ppepp,
            'ppepp_labels' => $ppepp_labels,
            'dokumenPerPPEPP' => $dokumenPerPPEPP,
            'ppepp_descriptions' => $ppepp_descriptions,
            'allPpeppStagesWithData' => $allPpeppStagesWithData,
            'stageKey' => $selected_ppepp,
            'stageLabel' => $ppepp_labels[$selected_ppepp] ?? 'Tahap Tidak Diketahui',
            'existingDocsForStage' => $dokumenPerPPEPP[$selected_ppepp] ?? collect()
        ];

        // Check if we should use the new view or existing one
        if (view()->exists('pages.kriteria.upload-kriteria.form')) {
            return view('pages.kriteria.upload-kriteria.form', $viewData);
        }

        if (!view()->exists('pages.kriteria.form')) {
            abort(404, "View untuk form upload dokumen tidak ditemukan.");
        }

        return view('pages.kriteria.form', $viewData);
    }

    public function finalisasiDokumen(Request $request, Kriteria $kriteria)
    {
        $user = Auth::user();

        // Log initial information
        Log::info('Starting finalization process', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'kriteria_id' => $kriteria->id
        ]);

        // Check if user role is one of the dosen roles
        if (!in_array($user->role, ['dosen1', 'dosen2', 'dosen3', 'administrator'])) {
            Log::warning('Unauthorized finalization attempt - not a dosen', [
                'user_id' => $user->id,
                'role' => $user->role
            ]);
            abort(403, 'Hanya dosen yang dapat memfinalisasi dokumen.');
        }

        // Check if the kriteria belongs to this user (if applicable)
        if (isset($kriteria->dosen_id) && $kriteria->dosen_id !== $user->id) {
            Log::warning('Unauthorized finalization attempt - not assigned to kriteria', [
                'user_id' => $user->id,
                'kriteria_dosen_id' => $kriteria->dosen_id
            ]);
            abort(403, 'Anda tidak berwenang memfinalisasi dokumen untuk kriteria ini.');
        }

        // Get only draft documents to finalize
        $dokumenDrafts = Dokumen::where('user_id', $user->id)
                       ->where('kriteria_id', $kriteria->id)
                       ->where('status', Dokumen::STATUS_DRAFT)
                       ->whereNotNull('path')
                       ->get();

        Log::info('Found drafts to finalize', [
            'draft_count' => $dokumenDrafts->count(),
            'draft_ids' => $dokumenDrafts->pluck('id')->toArray()
        ]);

        // If no draft documents found, inform the user
        if ($dokumenDrafts->count() === 0) {
            return redirect()->route('kriteria.show', $kriteria->id)
                            ->with('info', 'Tidak ada dokumen draft yang perlu difinalisasi.');
        }

        $berhasilFinalisasi = 0;
        foreach ($dokumenDrafts as $dokumen) {
            $dokumen->status = Dokumen::STATUS_MENUNGGU;
            $dokumen->save();
            $berhasilFinalisasi++;

            Log::info('Document finalized successfully', [
                'dokumen_id' => $dokumen->id,
                'jenis_ppepp' => $dokumen->jenis_ppepp
            ]);
        }

        if ($berhasilFinalisasi > 0) {
            Log::info('Finalization completed successfully', [
                'successful_count' => $berhasilFinalisasi
            ]);

            return redirect()->route('kriteria.show', $kriteria->id)
                            ->with('success', "{$berhasilFinalisasi} dokumen draft berhasil difinalisasi dan dikirim untuk validasi.");
        }

        Log::warning('Finalization failed - no documents were finalized');

        return redirect()->route('kriteria.upload.form', ['kriteria' => $kriteria->id, 'ppepp' => 'penetapan'])
                        ->with('error', 'Gagal memfinalisasi dokumen. Pastikan dokumen memiliki file atau deskripsi.');
    }

    public function kelola(Kriteria $kriteria)
    {
        return redirect()->route('kriteria.upload.form', ['kriteria' => $kriteria->id, 'ppepp' => 'penetapan']);
    }

    public function updateDescription(Request $request, Kriteria $kriteria, $ppepp)
    {
        $request->validate([
            'description' => 'required|string|max:1000',
        ]);

        // Update the description in the database
        $kriteria->updatePPEPPDescription($ppepp, $request->description);

        return redirect()->back()->with('success', 'Deskripsi PPEPP berhasil diperbarui.');
    }

    public function deleteDescription(Kriteria $kriteria, $ppepp)
    {
        $descriptions = json_decode($kriteria->ppepp_descriptions ?? '{}', true);

        if (isset($descriptions[$ppepp])) {
            unset($descriptions[$ppepp]);
            $kriteria->ppepp_descriptions = json_encode($descriptions);
            $kriteria->save();

            return redirect()->back()->with('success', 'Deskripsi PPEPP berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Deskripsi PPEPP tidak ditemukan.');
    }
}
