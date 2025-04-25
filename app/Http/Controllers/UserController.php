<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Level;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('level')->get();
        $levels = Level::all();
        return view('pages.admin.users.index', compact('users', 'levels'));
    }

    public function create()
    {
        $levels = Level::all();
        return view('pages.users.create', compact('levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'level_id' => 'required|exists:levels,level_id'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);
        $user->load('level');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'user' => $user
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('pages.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $levels = Level::all();
        return view('pages.users.edit', compact('user', 'levels'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'level_id' => 'required|exists:levels,level_id'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        }

        $user->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user' => $user->load('level')
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
