<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Level;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Check user level and redirect to appropriate dashboard
        switch($user->level->level_kode) {
            case 'ADM':
                return $this->adminDashboard();
            case 'ANG':
                return $this->anggotaDashboard();
            case 'KRT':
                return $this->koordinatorDashboard();
            case 'KJM':
                return $this->kjmDashboard();
            case 'KPS':
                return $this->kaprodiDashboard();
            case 'KJR':
                return $this->kajurDashboard();
            default:
                return redirect()->route('login')->with('error', 'Unauthorized access');
        }
    }

    public function adminDashboard()
    {
        $data = [
            'total_users' => User::count(),
            'total_levels' => Level::count(),
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