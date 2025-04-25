<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index()
    {
        $levels = Level::all();
        return view('pages.levels.index', compact('levels'));
    }

    public function create()
    {
        return view('pages.levels.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean'
        ]);

        Level::create($validated);

        return redirect()->route('levels.index')->with('success', 'Level created successfully.');
    }

    public function show(Level $level)
    {
        return view('pages.levels.show', compact('level'));
    }

    public function edit(Level $level)
    {
        return view('pages.levels.edit', compact('level'));
    }

    public function update(Request $request, Level $level)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean'
        ]);

        $level->update($validated);

        return redirect()->route('levels.index')->with('success', 'Level updated successfully.');
    }

    public function destroy(Level $level)
    {
        $level->delete();
        return redirect()->route('levels.index')->with('success', 'Level deleted successfully.');
    }
} 