<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Services\HistoryService;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    protected $historyService;
    
    public function __construct(HistoryService $historyService)
    {
        $this->historyService = $historyService;
    }
    
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'login' => 'required|string',
                'password' => 'required|string'
            ]);

            // Determine if input is email or username
            $loginType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            
            // Find user by email or username
            $user = User::where($loginType, $credentials['login'])->first();

            $errors = [];

            if (!$user) {
                $errors['login'] = ($loginType === 'email') ? 'Email tidak terdaftar.' : 'Username tidak terdaftar.';
                throw ValidationException::withMessages($errors);
            }

            // Second check: Verify if password is correct for this specific user
            if (!Hash::check($credentials['password'], $user->password)) {
                $errors['password'] = 'Password yang Anda masukkan salah.';
                throw ValidationException::withMessages($errors);
            }

            // If both checks pass, attempt to login
            // We need to specify which field to use for authentication
            if (Auth::attempt([$loginType => $credentials['login'], 'password' => $credentials['password']])) {
                $request->session()->regenerate();

                // Check if user has a valid role for dashboard access
                $validRoles = ['administrator', 'dosen', 'koordinator', 'kjm', 'kaprodi', 'kajur', 'direktur'];
                if (!in_array($user->role, $validRoles)) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('login')->with('error', 'Peran pengguna tidak valid.');
                }
                
                // Catat aktivitas login
                $this->historyService->recordLogin($user);

                return redirect()->intended('dashboard');
            }

            // If login fails for any other reason
            throw ValidationException::withMessages([
                'login' => 'Terjadi kesalahan saat login. Silakan coba lagi.',
            ]);

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('login'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan sistem. Silakan coba lagi.'])
                ->withInput($request->only('login'));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
    
    /**
     * Display the forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }
    
    /**
     * Handle sending password reset link
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if user with this email exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar dalam sistem.']);
        }

        try {
            // Send the password reset link
            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status === Password::RESET_LINK_SENT
                        ? back()->with(['success' => __($status)])
                        : back()->withErrors(['email' => __($status)]);
        } catch (\Exception $e) {
            // For development: If email sending fails, still return success
            // with a message about checking the log
            \Illuminate\Support\Facades\Log::error('Failed to send password reset email: ' . $e->getMessage());
            
            return back()->with([
                'success' => 'Link reset password telah dikirim ke email Anda. (Catatan: Dalam mode pengembangan, email mungkin dilog ke file log aplikasi)'
            ]);
        }
    }
    
    /**
     * Display the password reset form
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }
    
    /**
     * Handle the password reset
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Attempt to reset the user's password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        // Return the status
        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('success', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
