<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil pengguna.
     */
    public function index()
    {
        $user = Auth::user();
        return view('pages.profile.profile', compact('user'));
    }

    /**
     * Upload atau update foto profil pengguna.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'profile_photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->photo && Storage::exists('public/profile/' . $user->photo)) {
            Storage::delete('public/profile/' . $user->photo);
        }

        // Simpan foto baru
        $filename = time() . '.' . $request->file('profile_photo')->getClientOriginalExtension();
        $request->file('profile_photo')->storeAs('public/profile', $filename);

        // Update nama file ke kolom foto
        User::where('id', $user->id)->update(['photo' => $filename]);

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui.');
    }
    
    /**
     * Alternative method to update profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->photo && Storage::exists('public/profile/' . $user->photo)) {
            Storage::delete('public/profile/' . $user->photo);
        }

        // Simpan foto baru
        $filename = time() . '.' . $request->file('photo')->getClientOriginalExtension();
        $request->file('photo')->storeAs('public/profile', $filename);

        // Update nama file ke kolom foto
        User::where('id', $user->id)->update(['photo' => $filename]);

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}