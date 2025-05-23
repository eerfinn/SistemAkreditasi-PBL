<?php

namespace App\Http\Controllers;

use App\Models\DaftarTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DaftarTugasController extends Controller
{
    /**
     * Mendapatkan semua tugas untuk user yang sedang login
     */
    public function index()
    {
        $tugas = DaftarTugas::where('user_id', Auth::id())->get();
        return response()->json($tugas);
    }

    /**
     * Menyimpan tugas baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu' => 'nullable|date_format:H:i',
            'show_in_calendar' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tugas = new DaftarTugas();
        $tugas->user_id = Auth::id();
        $tugas->judul = $request->judul;
        $tugas->deskripsi_tugas = $request->deskripsi_tugas ?? null;
        $tugas->tanggal = $request->tanggal;
        $tugas->waktu = $request->waktu ?? '00:00:00';
        $tugas->status = 'pending';
        $tugas->show_in_calendar = $request->has('show_in_calendar') ? (bool)$request->show_in_calendar : true;
        $tugas->save();

        return response()->json($tugas, 201);
    }

    /**
     * Mengupdate tugas yang ada
     */
    public function update(Request $request, $id)
    {
        $tugas = DaftarTugas::where('user_id', Auth::id())->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu' => 'nullable|date_format:H:i',
            'status' => 'in:pending,completed',
            'show_in_calendar' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tugas->judul = $request->judul;
        $tugas->deskripsi_tugas = $request->deskripsi_tugas ?? $tugas->deskripsi_tugas;
        $tugas->tanggal = $request->tanggal;
        $tugas->waktu = $request->waktu ?? $tugas->waktu;
        $tugas->status = $request->status ?? $tugas->status;
        $tugas->show_in_calendar = $request->has('show_in_calendar') ? (bool)$request->show_in_calendar : $tugas->show_in_calendar;
        $tugas->save();

        return response()->json($tugas);
    }

    /**
     * Mengupdate status tugas
     */
    public function updateStatus(Request $request, $id)
    {
        $tugas = DaftarTugas::where('user_id', Auth::id())->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tugas->status = $request->status;
        $tugas->save();

        return response()->json($tugas);
    }

    /**
     * Menghapus tugas
     */
    public function destroy($id)
    {
        $tugas = DaftarTugas::where('user_id', Auth::id())->findOrFail($id);
        $tugas->delete();

        return response()->json(['message' => 'Tugas berhasil dihapus']);
    }
} 