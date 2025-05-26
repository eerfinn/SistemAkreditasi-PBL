<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KriteriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('kriteria.access')->only(['show', 'uploadForm', 'finalisasiDokumen']);
    }

    /**
     * Display a listing of all kriteria.
     */
    public function index()
    {
        $kriteria = Kriteria::all();
        return view('pages.kriteria.index', compact('kriteria'));
    }

    /**
     * Show the detail of a specific kriteria
     */
    public function show($id, Request $request)
    {
        $user = Auth::user();
        $kriteria = Kriteria::findOrFail($id);
        $selected_ppepp = $request->query('ppepp', 'penetapan'); // Default to penetapan if not specified

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

            // Show all documents for admin, both admin-uploaded and user-uploaded
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

        // Count documents by status
        $statusCounts = [
            'draft' => 0,
            'menunggu' => 0,
            'revisi' => 0,
            'diterima' => 0,
            'diverifikasi' => 0
        ];

        foreach ($dokumenPerPPEPP as $docs) {
            foreach ($docs as $doc) {
                if (isset($statusCounts[$doc->status])) {
                    $statusCounts[$doc->status]++;
                }
            }
        }

        $viewData = [
            'kriteria' => $kriteria,
            'selected_ppepp' => $selected_ppepp,
            'ppepp_labels' => $ppepp_labels,
            'dokumenPerPPEPP' => $dokumenPerPPEPP,
            'ppepp_descriptions' => $ppepp_descriptions,
            'allPpeppStagesWithData' => $allPpeppStagesWithData,
            'stageKey' => $selected_ppepp,
            'stageLabel' => $ppepp_labels[$selected_ppepp] ?? 'Tahap Tidak Diketahui',
            'existingDocsForStage' => $dokumenPerPPEPP[$selected_ppepp] ?? collect(),
            'statusCounts' => $statusCounts,
            'is_admin' => $user->role === 'administrator'
        ];

        return view('pages.kriteria.kriteria', $viewData);
    }

    /**
     * Show the kelola (management) view for a kriteria
     */
    public function kelola($kriteria, Request $request)
    {
        $user = Auth::user();
        $kriteria = Kriteria::findOrFail($kriteria);

        $ppepp_labels = [
            'penetapan' => 'C1. Penetapan',
            'pelaksanaan' => 'C2. Pelaksanaan',
            'evaluasi' => 'C3. Evaluasi',
            'pengendalian' => 'C4. Pengendalian',
            'peningkatan' => 'C5. Peningkatan'
        ];

        // Get documents for each PPEPP stage
        $dokumenPerPPEPP = [];
        foreach (array_keys($ppepp_labels) as $stage) {
            $query = Dokumen::where('kriteria_id', $kriteria->id)
                ->where('jenis_ppepp', $stage)
                ->whereNotNull('path')
                ->with('user');

            $dokumenPerPPEPP[$stage] = $query->orderBy('updated_at', 'desc')->get();
        }

        // Get descriptions for each PPEPP stage from kriteria table
        $ppepp_descriptions = json_decode($kriteria->ppepp_descriptions ?? '{}', true) ?: [];

        return view('pages.kriteria.kelola', [
            'kriteria' => $kriteria,
            'ppepp_labels' => $ppepp_labels,
            'dokumenPerPPEPP' => $dokumenPerPPEPP,
            'ppepp_descriptions' => $ppepp_descriptions,
        ]);
    }

    /**
     * Show the form for uploading documents
     */
    public function uploadForm($kriteria, $ppepp, Request $request)
    {
        $user = Auth::user();
        $kriteria = Kriteria::findOrFail($kriteria);
        $selected_ppepp = $ppepp; // Use the provided PPEPP value

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

            // Show all documents
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

        $viewData = [
            'kriteria' => $kriteria,
            'selected_ppepp' => $selected_ppepp,
            'ppepp_labels' => $ppepp_labels,
            'dokumenPerPPEPP' => $dokumenPerPPEPP,
            'ppepp_descriptions' => $ppepp_descriptions,
            'allPpeppStagesWithData' => $allPpeppStagesWithData,
            'stageKey' => $selected_ppepp,
            'stageLabel' => $ppepp_labels[$selected_ppepp] ?? 'Tahap Tidak Diketahui',
            'existingDocsForStage' => $dokumenPerPPEPP[$selected_ppepp] ?? collect(),
            'is_admin' => $user->role === 'administrator'
        ];

        return view('pages.kriteria.form', $viewData);
    }

    /**
     * Store a new document
     */
    public function storeDocument(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'files.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120',
            'kriteria_id' => 'required|exists:kriteria,id',
            'jenis_ppepp' => 'required|string'
        ]);

        $kriteria = Kriteria::findOrFail($request->kriteria_id);
        $uploadedCount = 0;

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/dokumen_akreditasi/kriteria_' . $kriteria->id, $fileName);
                
                $dokumen = new Dokumen();
                $dokumen->kriteria_id = $kriteria->id;
                $dokumen->user_id = $user->id;
                $dokumen->nama_dokumen = $file->getClientOriginalName();
                $dokumen->jenis_ppepp = $request->jenis_ppepp;
                $dokumen->path = $path;
                $dokumen->status = Dokumen::STATUS_DRAFT;
                $dokumen->is_admin_upload = $user->role === 'administrator';
                $dokumen->save();
                
                $uploadedCount++;
                
                Log::info('User uploaded document', [
                    'dokumen_id' => $dokumen->id,
                    'kriteria_id' => $kriteria->id,
                'user_id' => $user->id,
                    'file_name' => $fileName
                ]);
            }
        }

        if ($uploadedCount > 0) {
            return redirect()->back()->with('success', "{$uploadedCount} dokumen berhasil diunggah dan disimpan sebagai draft.");
        }

        return redirect()->back()->with('error', 'Tidak ada dokumen yang diunggah.');
    }

    /**
     * Show the validation page for a specific kriteria
     */
    public function validasi($id, Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'administrator') {
            abort(403, 'Unauthorized access');
        }

        $kriteria = Kriteria::findOrFail($id);
        $selected_ppepp = $request->query('ppepp', 'penetapan'); // Default to penetapan if not specified

        $ppepp_labels = [
            'penetapan' => 'C1. Penetapan',
            'pelaksanaan' => 'C2. Pelaksanaan',
            'evaluasi' => 'C3. Evaluasi',
            'pengendalian' => 'C4. Pengendalian',
            'peningkatan' => 'C5. Peningkatan'
        ];

        // Get documents that need validation
        $dokumenPerPPEPP = [];
        foreach (array_keys($ppepp_labels) as $stage) {
            $query = Dokumen::where('kriteria_id', $kriteria->id)
                ->where('jenis_ppepp', $stage)
                ->whereNotNull('path')
                ->with('user');
                
            // For validation, show documents that need validation (menunggu/revisi)
            $query->whereIn('status', [Dokumen::STATUS_MENUNGGU, Dokumen::STATUS_REVISI]);

            $dokumenPerPPEPP[$stage] = $query->orderBy('updated_at', 'desc')->get();
        }

        // Prepare data for the navigation
        $allPpeppStagesWithData = [];
        foreach ($ppepp_labels as $key => $label) {
            $allPpeppStagesWithData[] = [
                'key' => $key,
                'label' => $label,
                'route_kelola_tahap_ini' => route('kriteria.validasi', ['id' => $kriteria->id, 'ppepp' => $key])
            ];
        }

        // Get descriptions for each PPEPP stage from kriteria table
        $ppepp_descriptions = json_decode($kriteria->ppepp_descriptions ?? '{}', true) ?: [];

        // Count documents by status
        $statusCounts = [
            'draft' => 0,
            'menunggu' => 0,
            'revisi' => 0,
            'diterima' => 0,
            'diverifikasi' => 0
        ];

        foreach ($dokumenPerPPEPP as $docs) {
            foreach ($docs as $doc) {
                if (isset($statusCounts[$doc->status])) {
                    $statusCounts[$doc->status]++;
                }
            }
        }

        $viewData = [
            'kriteria' => $kriteria,
            'selected_ppepp' => $selected_ppepp,
            'ppepp_labels' => $ppepp_labels,
            'dokumenPerPPEPP' => $dokumenPerPPEPP,
            'ppepp_descriptions' => $ppepp_descriptions,
            'allPpeppStagesWithData' => $allPpeppStagesWithData,
            'stageKey' => $selected_ppepp,
            'stageLabel' => $ppepp_labels[$selected_ppepp] ?? 'Tahap Tidak Diketahui',
            'documents' => $dokumenPerPPEPP[$selected_ppepp] ?? collect(),
            'statusCounts' => $statusCounts,
            'is_admin' => true
        ];

        return view('pages.kriteria.validasi', $viewData);
    }

    /**
     * Process document validation
     */
    public function processValidasi(Request $request, Dokumen $dokumen)
    {
        $user = Auth::user();
        
        if ($user->role !== 'administrator') {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'status' => 'required|in:' . Dokumen::STATUS_DIVERIFIKASI . ',' . Dokumen::STATUS_REVISI,
            'komentar' => 'nullable|string|max:1000',
        ]);

        $dokumen->status = $request->status;
        
        if ($request->filled('komentar')) {
            $dokumen->komentar = $request->komentar;
        }
        
        $dokumen->save();

        Log::info('Admin validated document', [
            'dokumen_id' => $dokumen->id,
            'kriteria_id' => $dokumen->kriteria_id,
            'admin_id' => $user->id,
            'status' => $request->status
        ]);

        $statusText = ($request->status === Dokumen::STATUS_DIVERIFIKASI) ? 'diverifikasi' : 'perlu direvisi';
        
        return redirect()->back()->with('success', "Dokumen berhasil divalidasi dengan status: {$statusText}");
    }

    /**
     * Finalize documents
     */
    public function finalisasi(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['administrator', 'dosen1', 'dosen2', 'dosen3'])) {
            abort(403, 'Unauthorized access');
        }

        $kriteria = Kriteria::findOrFail($id);

        // Get only draft documents to finalize
        $dokumenDrafts = Dokumen::where('kriteria_id', $kriteria->id)
                       ->where('status', Dokumen::STATUS_DRAFT)
                       ->whereNotNull('path')
                       ->get();

        Log::info('User found drafts to finalize', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'draft_count' => $dokumenDrafts->count(),
            'draft_ids' => $dokumenDrafts->pluck('id')->toArray()
        ]);

        // If no draft documents found, inform the user
        if ($dokumenDrafts->count() === 0) {
            return redirect()->back()
                            ->with('info', 'Tidak ada dokumen draft yang perlu difinalisasi.');
        }

        $berhasilFinalisasi = 0;
        foreach ($dokumenDrafts as $dokumen) {
            $dokumen->status = Dokumen::STATUS_MENUNGGU; // Change status to menunggu (waiting for validation)
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

            return redirect()->back()
                            ->with('success', "{$berhasilFinalisasi} dokumen draft berhasil difinalisasi dan menunggu validasi.");
        }

        Log::warning('Finalization failed - no documents were finalized');

        return redirect()->back()
                        ->with('error', 'Gagal memfinalisasi dokumen. Pastikan dokumen memiliki file.');
    }
    
    /**
     * Delete draft document
     */
    public function destroyDraft(Dokumen $dokumen)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['administrator', 'dosen1', 'dosen2', 'dosen3'])) {
            abort(403, 'Unauthorized access');
        }

        // Verify document is a draft
        if ($dokumen->status !== Dokumen::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'Hanya dokumen draft yang dapat dihapus.');
        }

        $kriteriaId = $dokumen->kriteria_id;

        // Delete physical file if exists
        if ($dokumen->path && Storage::disk('public')->exists($dokumen->path)) {
            Storage::disk('public')->delete($dokumen->path);
        }

        // Delete database record
        $dokumen->delete();
        
        Log::info('User deleted draft document', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'dokumen_id' => $dokumen->id,
            'kriteria_id' => $kriteriaId
        ]);

        return redirect()->route('kriteria.show', ['kriteria' => $kriteriaId])
                         ->with('success', 'Dokumen draft berhasil dihapus.');
    }

    /**
     * Update PPEPP description for a kriteria
     */
    public function updateDescription(Request $request, $kriteria, $ppepp)
    {
        $request->validate([
            'description' => 'required|string|max:1000',
        ]);

        $kriteria = Kriteria::findOrFail($kriteria);
        $kriteria->updatePPEPPDescription($ppepp, $request->description);

        return redirect()->back()->with('success', 'Deskripsi PPEPP berhasil diperbarui.');
    }

    /**
     * Delete PPEPP description for a kriteria
     */
    public function deleteDescription(Request $request, $kriteria, $ppepp)
    {
        $kriteria = Kriteria::findOrFail($kriteria);
        $kriteria->updatePPEPPDescription($ppepp, null);

            return redirect()->back()->with('success', 'Deskripsi PPEPP berhasil dihapus.');
    }
}
