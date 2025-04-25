<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index()
    {
        $levels = Level::all();
        return view('pages.admin.levels.index', compact('levels'));
    }

    public function create()
    {
        return view('pages.admin.levels.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'level_kode' => 'required|string|max:50|unique:levels',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $level = Level::create($validated);

        if($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Level created successfully.',
                'level' => $level
            ]);
        }

        return redirect()->route('admin.levels.index')->with('success', 'Level created successfully.');
    }

    public function show(Level $level)
    {
        return view('pages.admin.levels.show', compact('level'));
    }

    public function edit(Level $level)
    {
        return view('pages.admin.levels.edit', compact('level'));
    }

    public function update(Request $request, Level $level)
    {
        $validated = $request->validate([
            'level_kode' => 'required|string|max:50|unique:levels,level_kode,' . $level->level_id . ',level_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $level->update($validated);

        if($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Level updated successfully.',
                'level' => $level
            ]);
        }

        return redirect()->route('admin.levels.index')->with('success', 'Level updated successfully.');
    }

    public function destroy(Level $level)
    {
        $level->delete();

        if(request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Level deleted successfully.'
            ]);
        }

        return redirect()->route('levels.index')->with('success', 'Level deleted successfully.');
    }
} 