<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // Hanya redirect ke adminDashboard
        if ($user->role === 'administrator') {
            return $this->adminDashboard();
        }
        // Jika bukan admin, redirect ke login atau halaman lain sesuai kebutuhan
        return redirect()->route('login')->with('error', 'Unauthorized access');
    }

    public function adminDashboard()
    {
        $data = [
            'total_users' => User::count(),
            'user' => auth()->user()
        ];
        return view('pages.admin.dashboard', $data);
    }
} 