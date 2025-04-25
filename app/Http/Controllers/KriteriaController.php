<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::all();
        return view('pages.kriteria.index', compact('kriteria'));
    }

    public function show($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        return view('pages.kriteria.show', compact('kriteria'));
    }

    public function suplemen()
    {
        return view('pages.kriteria.suplemen');
    }
}