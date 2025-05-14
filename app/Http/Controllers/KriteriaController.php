<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        if ($user && $user->role === 'dosen') {
            foreach ($ppepp_stages as $stage) {
                $dokumenStage = Dokumen::where('kriteria_id', $kriteria->id)
                    ->where('user_id', $user->id)
                    ->where('jenis_ppepp', $stage)
                    ->orderByRaw("FIELD(status, '".Dokumen::STATUS_DRAFT."', '".Dokumen::STATUS_REVISI."') DESC")
                    ->orderBy('updated_at', 'desc')
                    ->first();

                $dokumenPerPPEPP[$stage] = $dokumenStage;
                if ($dokumenStage && $dokumenStage->status === Dokumen::STATUS_DRAFT) {
                    $totalDrafts++;
                }
            }
        } else {
            foreach ($ppepp_stages as $stage) {
                $dokumenPerPPEPP[$stage] = Dokumen::where('kriteria_id', $kriteria->id)
                    ->where('jenis_ppepp', $stage)
                    ->whereNotIn('status', [Dokumen::STATUS_DRAFT])
                    ->orderBy('updated_at', 'desc')
                    ->first();
            }
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

        $daftarDokumenFinal = collect($dokumenPerPPEPP)->filter(function ($doc) {
            return $doc && $doc->status !== Dokumen::STATUS_DRAFT;
        });

        if ($user && $user->role !== 'dosen') {
            $daftarDokumenFinal = Dokumen::where('kriteria_id', $kriteria->id)
                                ->where('status', '!=', Dokumen::STATUS_DRAFT)
                                ->with('user')
                                ->orderBy('jenis_ppepp')->orderBy('updated_at', 'desc')->get();
        }

        $bisaFinalisasi = ($totalDrafts === count($ppepp_stages));

        return view('pages.kriteria.kriteria', [
            'kriteria'        => $kriteria,
            'dokumenPerPPEPP' => $dokumenPerPPEPP,
            'ppepp_stages'    => $ppepp_stages,
            'statusCounts'    => $statusCounts,
            'user'            => $user,
            'bisaFinalisasi'  => $bisaFinalisasi,
            'daftarDokumen'   => $daftarDokumenFinal,
            'totalDrafts'     => $totalDrafts
        ]);
    }

    public function uploadForm(Kriteria $kriteria)
    {
        $user = Auth::user();
        // Gate::authorize('upload-dokumen-kriteria', $kriteria);

        $existingDataPerPPEPP = [];
        $ppepp_stages = [Dokumen::PPEPP_PENETAPAN, Dokumen::PPEPP_PELAKSANAAN, Dokumen::PPEPP_EVALUASI, Dokumen::PPEPP_PENGENDALIAN, Dokumen::PPEPP_PENINGKATAN];

        foreach($ppepp_stages as $stage) {
            $doc = Dokumen::where('user_id', $user->id)
                        ->where('kriteria_id', $kriteria->id)
                        ->where('jenis_ppepp', $stage)
                        // Ambil draft atau yang perlu revisi untuk diedit
                        ->whereIn('status', [Dokumen::STATUS_DRAFT, Dokumen::STATUS_REVISI])
                        ->first();
            if ($doc) {
                $existingDataPerPPEPP[$stage] = $doc;
            } else {
                // Jika tidak ada draft/revisi, cek apakah ada dokumen final untuk stage ini (hanya untuk info, tidak untuk diedit langsung)
                $finalDoc = Dokumen::where('user_id', $user->id)
                                ->where('kriteria_id', $kriteria->id)
                                ->where('jenis_ppepp', $stage)
                                ->whereNotIn('status', [Dokumen::STATUS_DRAFT, Dokumen::STATUS_REVISI])
                                ->first();
                if ($finalDoc) {
                     $existingDataPerPPEPP[$stage] = $finalDoc; // Kirim sebagai referensi
                } else {
                    $existingDataPerPPEPP[$stage] = null;
                }
            }
        }

        if (!view()->exists('pages.kriteria.upload-kriteria.form')) {
            abort(404, "View untuk form upload dokumen tidak ditemukan.");
        }
        return view('pages.kriteria.upload-kriteria.form', compact('kriteria', 'existingDataPerPPEPP', 'ppepp_stages'));
    }

    public function finalisasiDokumen(Request $request, Kriteria $kriteria)
    {
        $user = Auth::user();

        if ($user->role !== 'dosen' || $kriteria->dosen_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $totalDrafts = Dokumen::where('user_id', $user->id)
                         ->where('kriteria_id', $kriteria->id)
                         ->where('status', Dokumen::STATUS_DRAFT)
                         ->count();

        if ($totalDrafts !== 5) {
            return redirect()->route('kriteria.show', $kriteria->id)
                            ->with('error', 'Anda belum mengunggah draft untuk semua tahapan PPEPP');
        }

        $dokumenDrafts = Dokumen::where('user_id', $user->id)
                           ->where('kriteria_id', $kriteria->id)
                           ->where('status', Dokumen::STATUS_DRAFT)
                           ->get();

        $berhasilFinalisasi = 0;
        foreach ($dokumenDrafts as $dokumen) {
            if ($dokumen->path || !empty($dokumen->deskripsi_dokumen)) {
                $dokumen->status = Dokumen::STATUS_MENUNGGU;
                $dokumen->save();
                $berhasilFinalisasi++;
            } else {
                $dokumen->delete();
            }
        }

        if ($berhasilFinalisasi > 0) {
            return redirect()->route('kriteria.show', $kriteria->id)
                            ->with('success', 'Semua dokumen draft berhasil difinalisasi dan dikirim untuk validasi.');
        }

        return redirect()->route('kriteria.show', $kriteria->id)
                        ->with('error', 'Gagal memfinalisasi dokumen.');
    }
}
