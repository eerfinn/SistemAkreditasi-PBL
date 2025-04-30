<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Get user's role and redirect accordingly
            $userRole = Auth::user()->role;
            
            switch ($userRole) {
                case 'administrator':
                    return redirect()->route('admin.dashboard');
                case 'anggota':
                    return redirect()->route('anggota.dashboard');
                case 'kjm':
                    return redirect()->route('kjm.dashboard');
                case 'kaprodi':
                    return redirect()->route('kaprodi.dashboard');
                case 'kajur':
                    return redirect()->route('kajur.dashboard');
                case 'koordinator':
                    return redirect()->route('koordinator.dashboard');
                default:
                    return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
