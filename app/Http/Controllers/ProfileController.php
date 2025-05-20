<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada dan bukan default
        if ($user->photo && $user->photo !== 'default.png') {
            Storage::disk('public')->delete('profile/' . $user->photo);
        }

        // Simpan foto baru
        $filename = uniqid() . '.' . $request->photo->extension();
        $request->photo->storeAs('profile', $filename, 'public');

        // Update database
        $user->photo = $filename;
        $user->save();

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}