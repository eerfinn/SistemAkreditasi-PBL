<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return view('pages.projects.index');
    }

    public function create()
    {
        return view('pages.projects.create');
    }

    public function store(Request $request)
    {
        // Store project logic here
    }

    public function show($id)
    {
        return view('pages.projects.show', compact('id'));
    }

    public function edit($id)
    {
        return view('pages.projects.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Update project logic here
    }

    public function destroy($id)
    {
        // Delete project logic here
    }
} 