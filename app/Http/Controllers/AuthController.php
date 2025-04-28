<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required', // Menggunakan username atau email
            'password' => 'required'
        ]);

        // Cek apakah input adalah email atau username
        $fieldType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Autentikasi berdasarkan field yang sesuai
        if (Auth::attempt([$fieldType => $credentials['login'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            // Get user's role and redirect accordingly
            $userRole = Auth::user()->level->level_kode;
            
            switch ($userRole) {
                case 'ADM':
                    return redirect()->route('admin.dashboard');
                case 'ANG':
                    return redirect()->route('anggota.dashboard');
                case 'KJM':
                    return redirect()->route('kjm.dashboard');
                case 'KPS':
                    return redirect()->route('kps.dashboard');
                case 'KJR':
                    return redirect()->route('kajur.dashboard');
                case 'KRT':
                    return redirect()->route('koordinator.dashboard');
                default:
                    return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
