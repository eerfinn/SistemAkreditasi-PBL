<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $request->validate([
            'penetapan' => 'required|file|mimes:pdf,doc,docx,xlsx,xls|max:2048',
            'kriteria_id' => 'required|exists:kriteria,id',
        ]);

        $file = $request->file('penetapan');
        $path = $file->store('dokumen_akreditasi', 'public');
        $nama_dokumen = $file->getClientOriginalName();

        \App\Models\Dokumen::create([
            'user_id' => auth()->id(),
            'kriteria_id' => $request->kriteria_id,
            'nama_dokumen' => $nama_dokumen,
            'path' => $path,
            'status' => 'menunggu',
        ]);

        return redirect()->route('kriteria.show', 1)->with('success', 'Dokumen berhasil diupload!');
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
