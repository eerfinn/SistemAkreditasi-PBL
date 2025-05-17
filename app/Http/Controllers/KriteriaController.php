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
        if ($user && $user->role === 'dosen') {
            foreach ($ppepp_stages as $stage) {
                $query = Dokumen::where('kriteria_id', $kriteria->id)
                    ->where('user_id', $user->id)
                    ->where('jenis_ppepp', $stage)
                    ->orderByRaw("FIELD(status, '".Dokumen::STATUS_DRAFT."', '".Dokumen::STATUS_REVISI."') DESC")
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

        // Log dokumen yang ditemukan untuk setiap tahap
        foreach ($ppepp_stages as $stage) {
            Log::info("Dokumen for {$stage}", [
                'count' => isset($dokumenPerPPEPP[$stage]) ? $dokumenPerPPEPP[$stage]->count() : 0,
                'dokumen_ids' => isset($dokumenPerPPEPP[$stage]) ? $dokumenPerPPEPP[$stage]->pluck('id')->toArray() : []
            ]);
        }

        $statusQueryBaseFinal = Dokumen::where('kriteria_id', $kriteria->id)
                                    ->where('status', '!=', Dokumen::STATUS_DRAFT);

        if ($user && $user->role === 'dosen') {
            $statusQueryBaseFinal->where('user_id', $user->id);
        }

        $statusCounts = [
            'menunggu'     => (clone $statusQueryBaseFinal)->where('status', Dokumen::STATUS_MENUNGGU)->count(),
            'revisi'       => (clone $statusQueryBaseFinal)->where('status', Dokumen::STATUS_REVISI)->count(),
            'diterima'     => (clone $statusQueryBaseFinal)->where('status', Dokumen::STATUS_DITERIMA)->count(),
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

        if ($user && $user->role !== 'dosen') {
            $daftarDokumenFinal = Dokumen::where('kriteria_id', $kriteria->id)
                                ->where('status', '!=', Dokumen::STATUS_DRAFT)
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

        return view('pages.kriteria.kriteria', [
            'kriteria'        => $kriteria,
            'dokumenPerPPEPP' => $dokumenPerPPEPP,
            'ppepp_stages'    => $ppepp_stages,
            'statusCounts'    => $statusCounts,
            'user'            => $user,
            'bisaFinalisasi'  => $bisaFinalisasi,
            'daftarDokumen'   => $daftarDokumenFinal,
            'dokumenDrafts'   => $dokumenDrafts,
            'showUploadButton'=> $user && $user->role === 'dosen'
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
            $dokumenPerPPEPP[$stage] = Dokumen::where('user_id', $user->id)
                ->where('kriteria_id', $kriteria->id)
                ->where('jenis_ppepp', $stage)
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        if (!view()->exists('pages.kriteria.form')) {
            abort(404, "View untuk form upload dokumen tidak ditemukan.");
        }

        return view('pages.kriteria.form', [
            'kriteria' => $kriteria,
            'selected_ppepp' => $selected_ppepp,
            'ppepp_labels' => $ppepp_labels,
            'dokumenPerPPEPP' => $dokumenPerPPEPP
        ]);
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

        if ($user->role !== 'dosen') {
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

        // Define PPEPP stages
        $ppepp_stages = [
            Dokumen::PPEPP_PENETAPAN,
            Dokumen::PPEPP_PELAKSANAAN,
            Dokumen::PPEPP_EVALUASI,
            Dokumen::PPEPP_PENGENDALIAN,
            Dokumen::PPEPP_PENINGKATAN
        ];

        // Check if there's at least one draft for each PPEPP stage
        $missingStages = [];
        foreach ($ppepp_stages as $stage) {
            $count = Dokumen::where('user_id', $user->id)
                    ->where('kriteria_id', $kriteria->id)
                    ->where('jenis_ppepp', $stage)
                    ->where('status', Dokumen::STATUS_DRAFT)
                    ->count();

            Log::info("Checking draft for stage {$stage}", [
                'draft_count' => $count
            ]);
            
            if ($count === 0) {
                $missingStages[] = $stage;
            }
        }

        if (!empty($missingStages)) {
            $missingStagesList = implode(', ', array_map(function($stage) {
                return ucfirst($stage);
            }, $missingStages));
            
            Log::warning('Finalization failed - missing drafts', [
                'missing_stages' => $missingStages
            ]);
            
            return redirect()->route('kriteria.kelola', $kriteria->id)
                            ->with('error', "Anda belum mengunggah draft untuk tahapan: {$missingStagesList}");
        }

        // Get all drafts to finalize
        $dokumenDrafts = Dokumen::where('user_id', $user->id)
                       ->where('kriteria_id', $kriteria->id)
                       ->where('status', Dokumen::STATUS_DRAFT)
                       ->get();

        Log::info('Found drafts to finalize', [
            'draft_count' => $dokumenDrafts->count(),
            'draft_ids' => $dokumenDrafts->pluck('id')->toArray()
        ]);

        $berhasilFinalisasi = 0;
        foreach ($dokumenDrafts as $dokumen) {
            if ($dokumen->path || !empty($dokumen->deskripsi_dokumen)) {
                $dokumen->status = Dokumen::STATUS_MENUNGGU;
                $dokumen->save();
                $berhasilFinalisasi++;
                
                Log::info('Document finalized successfully', [
                    'dokumen_id' => $dokumen->id,
                    'jenis_ppepp' => $dokumen->jenis_ppepp
                ]);
            } else {
                $dokumen->delete();
                
                Log::info('Empty document deleted', [
                    'dokumen_id' => $dokumen->id,
                    'jenis_ppepp' => $dokumen->jenis_ppepp
                ]);
            }
        }

        if ($berhasilFinalisasi > 0) {
            Log::info('Finalization completed successfully', [
                'successful_count' => $berhasilFinalisasi
            ]);
            
            return redirect()->route('kriteria.show', $kriteria->id)
                            ->with('success', 'Semua dokumen draft berhasil difinalisasi dan dikirim untuk validasi.');
        }

        Log::warning('Finalization failed - no documents were finalized');
        
        return redirect()->route('kriteria.kelola', $kriteria->id)
                        ->with('error', 'Gagal memfinalisasi dokumen. Pastikan dokumen memiliki file atau deskripsi.');
    }

    public function kelola(Kriteria $kriteria)
    {
        $user = Auth::user();
        $dokumenPerPPEPP = [];

        $ppepp_labels = [
            'penetapan' => 'C1. Penetapan',
            'pelaksanaan' => 'C2. Pelaksanaan',
            'evaluasi' => 'C3. Evaluasi',
            'pengendalian' => 'C4. Pengendalian',
            'peningkatan' => 'C5. Peningkatan'
        ];

        foreach (array_keys($ppepp_labels) as $stage) {
            $dokumenPerPPEPP[$stage] = Dokumen::where('kriteria_id', $kriteria->id)
                ->when($user->role === 'dosen', function($query) use ($user) {
                    return $query->where('user_id', $user->id);
                })
                ->where('jenis_ppepp', $stage)
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        return view('pages.kriteria.kelola', [
            'kriteria' => $kriteria,
            'dokumenPerPPEPP' => $dokumenPerPPEPP,
            'ppepp_labels' => $ppepp_labels
        ]);
    }

    public function updateDescription(Request $request, Kriteria $kriteria, $ppepp)
    {
        $request->validate([
            'description' => 'required|string|max:1000',
        ]);

        // Update the description in the database
        // You'll need to adjust this based on your actual database structure
        $kriteria->updatePPEPPDescription($ppepp, $request->description);

        return redirect()->back()->with('success', 'Deskripsi PPEPP berhasil diperbarui.');
    }
}
