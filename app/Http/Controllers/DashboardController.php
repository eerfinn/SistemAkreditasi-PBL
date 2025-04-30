<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Check user role and redirect to appropriate dashboard
        switch($user->role) {
            case 'administrator':
                return $this->adminDashboard();
            case 'anggota':
                return $this->anggotaDashboard();
            case 'koordinator':
                return $this->koordinatorDashboard();
            case 'kjm':
                return $this->kjmDashboard();
            case 'kaprodi':
                return $this->kaprodiDashboard();
            case 'kajur':
                return $this->kajurDashboard();
            default:
                return redirect()->route('login')->with('error', 'Unauthorized access');
        }
    }

    public function adminDashboard()
    {
        $data = [
            'total_users' => User::count(),
            'user' => auth()->user()
        ];
        
        return view('pages.admin.dashboard', $data);
    }

    public function anggotaDashboard()
    {
        $data = [
            'user' => auth()->user()
        ];
        
        return view('pages.anggota.dashboard', $data);
    }

    public function koordinatorDashboard()
    {
        $data = [
            'user' => auth()->user()
        ];
        
        return view('pages.koordinator.dashboard', $data);
    }

    public function kjmDashboard()
    {
        $data = [
            'user' => auth()->user()
        ];
        
        return view('pages.kjm.dashboard', $data);
    }

    public function kaprodiDashboard()
    {
        $data = [
            'user' => auth()->user()
        ];
        
        return view('pages.kaprodi.dashboard', $data);
    }

    public function kajurDashboard()
    {
        $data = [
            'user' => auth()->user()
        ];
        
        return view('pages.kajur.dashboard', $data);
    }
} 