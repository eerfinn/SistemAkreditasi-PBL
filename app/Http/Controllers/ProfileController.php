<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('pages.profile.profile', compact('user'));
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->profile_photo && Storage::exists('public/profile-photos/' . $user->profile_photo)) {
            Storage::delete('public/profile-photos/' . $user->profile_photo);
        }

        // Proses upload foto baru
        if ($request->hasFile('profile_photo')) {
            $image = $request->file('profile_photo');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            
            // Buat direktori jika belum ada
            if (!Storage::exists('public/profile-photos')) {
                Storage::makeDirectory('public/profile-photos');
            }

            // Simpan gambar dengan ukuran yang dioptimasi
            $image = Image::make($image)->fit(250, 250)->encode();
            Storage::put('public/profile-photos/' . $filename, $image);

            // Update database
            $user->profile_photo = $filename;
            $user->save();

            return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
        }

        return redirect()->back()->with('error', 'Gagal mengupload foto profil.');
    }
}