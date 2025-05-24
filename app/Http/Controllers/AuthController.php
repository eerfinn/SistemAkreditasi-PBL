<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string'
            ]);

            // First check: Verify if username exists
            $user = User::where('username', $credentials['username'])->first();
            
            $errors = [];
            
            if (!$user) {
                $errors['username'] = 'Username tidak terdaftar.';
                throw ValidationException::withMessages($errors);
            }

            // Second check: Verify if password is correct for this specific user
            if (!Hash::check($credentials['password'], $user->password)) {
                $errors['password'] = 'Password yang Anda masukkan salah.';
                throw ValidationException::withMessages($errors);
            }

            // If both checks pass, attempt to login
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended('dashboard');
            }

            // If login fails for any other reason
            throw ValidationException::withMessages([
                'username' => 'Terjadi kesalahan saat login. Silakan coba lagi.',
            ]);

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('username'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan sistem. Silakan coba lagi.'])
                ->withInput($request->only('username'));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
