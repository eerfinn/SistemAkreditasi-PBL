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

        $errors = [];

        // Check if username exists
        $user = \App\Models\User::where('username', $credentials['username'])->first();
        if (!$user) {
            $errors['username'] = 'Username salah.';
        }

        // Check if password matches any user (independent of username)
        $passwordMatch = false;
        foreach (\App\Models\User::all() as $u) {
            if (\Illuminate\Support\Facades\Hash::check($credentials['password'], $u->password)) {
                $passwordMatch = true;
                break;
            }
        }
        if (!$passwordMatch) {
            $errors['password'] = 'Password salah.';
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->onlyInput('username');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Get user's role and redirect accordingly
            $userRole = Auth::user()->role;
            
            switch ($userRole) {
                case 'administrator':
                    return redirect()->route('admin.dashboard');
                case 'dosen':
                    return redirect()->route('dosen.dashboard');
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
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
