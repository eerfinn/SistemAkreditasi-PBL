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
        $bisaFinalisasi = false;

        // Daftar tahapan PPEPP
        $ppepp_stages = [
            Dokumen::PPEPP_PENETAPAN,
            Dokumen::PPEPP_PELAKSANAAN,
            Dokumen::PPEPP_EVALUASI,
            Dokumen::PPEPP_PENGENDALIAN,
            Dokumen::PPEPP_PENINGKATAN
        ];

        if ($user && $user->role === 'dosen') {
            foreach ($ppepp_stages as $stage) {
                // Prioritaskan draft, lalu revisi, lalu dokumen final untuk stage ini
                $dokumenStage = Dokumen::where('kriteria_id', $kriteria->id)
                    ->where('user_id', $user->id)
                    ->where('jenis_ppepp', $stage)
                    ->orderByRaw("FIELD(status, '".Dokumen::STATUS_DRAFT."', '".Dokumen::STATUS_REVISI."') DESC") // Prioritaskan draft dan revisi
                    ->orderBy('updated_at', 'desc')
                    ->first();

                $dokumenPerPPEPP[$stage] = $dokumenStage;
                if ($dokumenStage && $dokumenStage->status === Dokumen::STATUS_DRAFT) {
                    $bisaFinalisasi = true;
                }
            }
        } else {
            // Untuk peran non-dosen (admin, validator, dll.), tampilkan dokumen final per PPEPP
            foreach ($ppepp_stages as $stage) {
                 $dokumenPerPPEPP[$stage] = Dokumen::where('kriteria_id', $kriteria->id)
                    // ->where('user_id', $user->id) // Hapus ini jika non-dosen melihat semua user
                    ->where('jenis_ppepp', $stage)
                    ->whereNotIn('status', [Dokumen::STATUS_DRAFT]) // Hanya yang sudah difinalisasi
                    ->orderBy('updated_at', 'desc')
                    ->first(); // Asumsi hanya satu dokumen final per PPEPP per kriteria (mungkin perlu penyesuaian jika bisa multiple user)
            }
        }

        // Hitung ringkasan status untuk dokumen yang sudah difinalisasi (bukan draft)
        $statusQueryBaseFinal = Dokumen::where('kriteria_id', $kriteria->id)
                                        ->where('status', '!=', Dokumen::STATUS_DRAFT);
        if ($user && $user->role === 'dosen') { // Dosen hanya melihat ringkasan dokumennya
            $statusQueryBaseFinal->where('user_id', $user->id);
        }
        $statusCounts = [
            'menunggu'     => (clone $statusQueryBaseFinal)->where('status', Dokumen::STATUS_MENUNGGU)->count(),
            'revisi'       => (clone $statusQueryBaseFinal)->where('status', Dokumen::STATUS_REVISI)->count(),
            'diterima'     => (clone $statusQueryBaseFinal)->where('status', Dokumen::STATUS_DITERIMA)->count(),
            'diverifikasi' => (clone $statusQueryBaseFinal)->where('status', Dokumen::STATUS_DIVERIFIKASI)->count(),
        ];

        // Variabel $daftarDokumen tidak lagi digunakan dalam bentuk flat list,
        // data sudah ada di $dokumenPerPPEPP
        // Namun, view Anda mungkin masih mengharapkannya untuk tabel "Dokumen Final"
        // Kita akan sediakan $daftarDokumenFinal dari $dokumenPerPPEPP untuk peran non-dosen
        // atau dari dokumen final dosen.
        $daftarDokumenFinal = collect($dokumenPerPPEPP)->filter(function ($doc) {
            return $doc && $doc->status !== Dokumen::STATUS_DRAFT;
        });
        if ($user && $user->role !== 'dosen') { // Untuk non-dosen, ambil semua dokumen final
            $daftarDokumenFinal = Dokumen::where('kriteria_id', $kriteria->id)
                                    ->where('status', '!=', Dokumen::STATUS_DRAFT)
                                    ->with('user')
                                    ->orderBy('jenis_ppepp')->orderBy('updated_at', 'desc')->get();
        }


        return view('pages.kriteria.kriteria', [
            'kriteria'        => $kriteria,
            'dokumenPerPPEPP' => $dokumenPerPPEPP, // Array dokumen per tahapan PPEPP
            'ppepp_stages'    => $ppepp_stages,    // Kirim daftar stage ke view
            'statusCounts'    => $statusCounts,
            'user'            => $user,
            'bisaFinalisasi'  => $bisaFinalisasi,  // Flag untuk tombol finalisasi
            'daftarDokumen'   => $daftarDokumenFinal // Untuk tabel dokumen final yang sudah ada di view
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
        // Gate::authorize('finalisasi-dokumen-kriteria', $kriteria);

        $dokumenDrafts = Dokumen::where('user_id', $user->id)
                                ->where('kriteria_id', $kriteria->id)
                                ->where('status', Dokumen::STATUS_DRAFT)
                                ->get();

        if ($dokumenDrafts->isEmpty()) {
            return redirect()->route('kriteria.show', $kriteria->id)
                             ->with('info', 'Tidak ada dokumen draft yang bisa difinalisasi.');
        }

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
                            ->with('success', $berhasilFinalisasi . ' dokumen draft berhasil difinalisasi.');
        } else {
             return redirect()->route('kriteria.show', $kriteria->id)
                            ->with('info', 'Tidak ada dokumen draft yang valid untuk difinalisasi.');
        }
    }
}
