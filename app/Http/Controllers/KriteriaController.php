<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\Dokumen;

class KriteriaController extends Controller
{
    public function show($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $daftarDokumen = Dokumen::where('kriteria_id', $id)->with('user')->get();
        // Hitung status dokumen
        $statusCounts = [
            'menunggu' => $daftarDokumen->where('status', 'menunggu')->count(),
            'revisi' => $daftarDokumen->where('status', 'revisi')->count(),
            'diterima' => $daftarDokumen->where('status', 'diterima')->count(),
            'diverifikasi' => $daftarDokumen->where('status', 'diverifikasi')->count(),
        ];
        return view('pages.kriteria.kriteria', compact('kriteria', 'daftarDokumen', 'statusCounts'));
    }

    public function uploadForm($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        return view('pages.kriteria.upload-kriteria.form', compact('kriteria'));
    }
}   