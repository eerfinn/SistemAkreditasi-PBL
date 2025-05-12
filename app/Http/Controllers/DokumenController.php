<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokumenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return back()->withErrors(['user' => 'Anda harus login untuk mengupload dokumen.'])->withInput();
        }

        $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id',
            'dokumen' => 'required|array',
            'dokumen.*' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls|max:2048',
        ], [
            'dokumen.required' => 'Minimal satu dokumen harus diupload.'
        ]);

        $kriteriaId = $request->kriteria_id;
        $userId = auth()->id();
        $status = 'menunggu';
        $deskripsi = $request->input('deskripsi', []);
        $uploaded = false;

        foreach ($request->file('dokumen', []) as $jenis => $file) {
            if ($file) {
                $path = $file->store('dokumen_akreditasi', 'public');
                $nama_dokumen = $file->getClientOriginalName();
                $dokumen = \App\Models\Dokumen::create([
                    'user_id' => $userId,
                    'kriteria_id' => $kriteriaId,
                    'nama_dokumen' => $nama_dokumen,
                    'path' => $path,
                    'status' => $status,
                ]);
                Log::info('Dokumen berhasil diupload', [
                    'user_id' => $userId,
                    'kriteria_id' => $kriteriaId,
                    'nama_dokumen' => $nama_dokumen,
                    'path' => $path,
                    'dokumen_id' => $dokumen->id,
                ]);
                $uploaded = true;
            }
        }

        if (!$uploaded) {
            return back()->withErrors(['dokumen' => 'Minimal satu dokumen harus diupload.'])->withInput();
        }

        return redirect()->route('kriteria.show', $kriteriaId)->with('success', 'Dokumen berhasil diupload!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
