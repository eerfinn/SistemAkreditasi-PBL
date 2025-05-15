<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('photo')->store('profile_photos', 'public');
        $user = Auth::user();
        $user->profile_photo = $path;
        

        return response()->json([
            'success' => true,
            'photoUrl' => asset('storage/' . $path),
        ]);
    }

    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->nama = $request->nama;
        $user->username = $request->username;
        

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
    }
}
