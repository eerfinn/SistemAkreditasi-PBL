<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KriteriaManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Show the upload form for a specific kriteria
     */
    public function upload($id, Request $request)
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

        // Get existing documents for all PPEPP stages
        $dokumenPerPPEPP = [];
        foreach (array_keys($ppepp_labels) as $stage) {
            $query = Dokumen::where('kriteria_id', $kriteria->id)
                ->where('jenis_ppepp', $stage)
                ->whereNotNull('path') // Only get actual documents, not descriptions
                ->with('user'); // Eager load the user relationship

            // For admin upload, show all documents but with admin flag
            $query->where('is_admin_upload', true);

            $dokumenPerPPEPP[$stage] = $query->orderBy('updated_at', 'desc')->get();
        }

        // Prepare data for the new view format with the needed structure for navigation
        $allPpeppStagesWithData = [];
        foreach ($ppepp_labels as $key => $label) {
            $allPpeppStagesWithData[] = [
                'key' => $key,
                'label' => $label,
                'route_kelola_tahap_ini' => route('admin.kriteria-management.upload.form', ['id' => $kriteria->id, 'ppepp' => $key])
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
            'is_admin' => true
        ];

        return view('pages.admin.kriteria-management.upload.kriteria', $viewData);
    }

    /**
     * Show the form for uploading documents
     */
    public function uploadForm($id, Request $request)
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

        // Get existing documents for all PPEPP stages
        $dokumenPerPPEPP = [];
        foreach (array_keys($ppepp_labels) as $stage) {
            $query = Dokumen::where('kriteria_id', $kriteria->id)
                ->where('jenis_ppepp', $stage)
                ->whereNotNull('path') // Only get actual documents, not descriptions
                ->with('user'); // Eager load the user relationship

            // For admin upload, show all documents but with admin flag
            $query->where('is_admin_upload', true);

            $dokumenPerPPEPP[$stage] = $query->orderBy('updated_at', 'desc')->get();
        }

        // Prepare data for the new view format with the needed structure for navigation
        $allPpeppStagesWithData = [];
        foreach ($ppepp_labels as $key => $label) {
            $allPpeppStagesWithData[] = [
                'key' => $key,
                'label' => $label,
                'route_kelola_tahap_ini' => route('admin.kriteria-management.upload.form', ['id' => $kriteria->id, 'ppepp' => $key])
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
            'is_admin' => true
        ];

        return view('pages.admin.kriteria-management.upload.form', $viewData);
    }

    /**
     * Store a new document uploaded by admin
     */
    public function storeDocument(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'administrator') {
            abort(403, 'Unauthorized access');
        }

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
                $dokumen->is_admin_upload = true;
                $dokumen->save();
                
                $uploadedCount++;
                
                Log::info('Admin uploaded document', [
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
                ->whereIn('status', [Dokumen::STATUS_MENUNGGU, Dokumen::STATUS_REVISI])
                ->with('user');

            $dokumenPerPPEPP[$stage] = $query->orderBy('updated_at', 'desc')->get();
        }

        // Prepare data for the navigation
        $allPpeppStagesWithData = [];
        foreach ($ppepp_labels as $key => $label) {
            $allPpeppStagesWithData[] = [
                'key' => $key,
                'label' => $label,
                'route_kelola_tahap_ini' => route('admin.kriteria-management.validasi', ['id' => $kriteria->id, 'ppepp' => $key])
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

        return view('pages.admin.kriteria-management.validasi.kriteria', $viewData);
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
            'status' => 'required|in:' . Dokumen::STATUS_DITERIMA . ',' . Dokumen::STATUS_REVISI,
            'komentar' => 'nullable|string|max:1000',
        ]);

        $dokumen->status = $request->status;
        
        if ($request->filled('komentar')) {
            $dokumen->komentar = $request->komentar;
        }
        
        $dokumen->validator_id = $user->id;
        $dokumen->validated_at = now();
        $dokumen->save();

        Log::info('Admin validated document', [
            'dokumen_id' => $dokumen->id,
            'kriteria_id' => $dokumen->kriteria_id,
            'validator_id' => $user->id,
            'status' => $request->status
        ]);

        $statusText = ($request->status === Dokumen::STATUS_DITERIMA) ? 'diterima' : 'perlu direvisi';
        
        return redirect()->back()->with('success', "Dokumen berhasil divalidasi dengan status: {$statusText}");
    }

    /**
     * Finalize admin documents
     */
    public function finalisasi(Request $request, $id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'administrator') {
            abort(403, 'Unauthorized access');
        }

        $kriteria = Kriteria::findOrFail($id);

        // Get only draft documents to finalize
        $dokumenDrafts = Dokumen::where('user_id', $user->id)
                       ->where('kriteria_id', $kriteria->id)
                       ->where('status', Dokumen::STATUS_DRAFT)
                       ->where('is_admin_upload', true)
                       ->whereNotNull('path')
                       ->get();

        Log::info('Admin found drafts to finalize', [
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
            $dokumen->status = Dokumen::STATUS_DITERIMA; // Admin documents are automatically accepted
            $dokumen->validator_id = $user->id;
            $dokumen->validated_at = now();
            $dokumen->save();
            $berhasilFinalisasi++;
            
            Log::info('Admin document finalized successfully', [
                'dokumen_id' => $dokumen->id,
                'jenis_ppepp' => $dokumen->jenis_ppepp
            ]);
        }

        if ($berhasilFinalisasi > 0) {
            Log::info('Admin finalization completed successfully', [
                'successful_count' => $berhasilFinalisasi
            ]);
            
            return redirect()->back()
                            ->with('success', "{$berhasilFinalisasi} dokumen draft berhasil difinalisasi dan langsung diterima.");
        }

        Log::warning('Admin finalization failed - no documents were finalized');
        
        return redirect()->back()
                        ->with('error', 'Gagal memfinalisasi dokumen. Pastikan dokumen memiliki file.');
    }
    
    /**
     * Delete admin draft document
     */
    public function destroyDraft(Dokumen $dokumen)
    {
        $user = Auth::user();
        
        if ($user->role !== 'administrator') {
            abort(403, 'Unauthorized access');
        }

        // Verify document is a draft and belongs to the admin
        if ($dokumen->status !== Dokumen::STATUS_DRAFT || $dokumen->user_id !== $user->id || !$dokumen->is_admin_upload) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus dokumen ini atau dokumen sudah difinalisasi.');
        }

        $kriteriaId = $dokumen->kriteria_id;

        // Delete physical file if exists
        if ($dokumen->path && Storage::disk('public')->exists($dokumen->path)) {
            Storage::disk('public')->delete($dokumen->path);
        }

        // Delete database record
        $dokumen->delete();
        
        Log::info('Admin deleted draft document', [
            'dokumen_id' => $dokumen->id,
            'kriteria_id' => $kriteriaId
        ]);

        return redirect()->route('admin.kriteria-management.upload', ['id' => $kriteriaId])
                         ->with('success', 'Dokumen draft admin berhasil dihapus.');
    }
} 