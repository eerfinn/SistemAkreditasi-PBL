<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('pages.admin.user-management.index', compact('users'));
    }

    public function create()
    {
        $roles = ['administrator', 'dosen', 'koordinator', 'kajur', 'kjm', 'kaprodi'];
        return view('pages.admin.user-management.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|unique:user',
            'password' => 'required|string|min:8',
            'role' => 'required|in:administrator,dosen,koordinator,kjm,kaprodi,kajur'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'user' => $user
            ]);
        }

        return redirect()->route('admin.user.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('pages.admin.user-management.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = ['administrator', 'dosen', 'koordinator', 'kajur', 'kjm', 'kaprodi'];
        return view('pages.admin.user-management.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|unique:user,username,' . $user->id,
            'role' => 'required|in:administrator,dosen,koordinator,kjm,kaprodi,kajur'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        }

        $user->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user' => $user
            ]);
        }

        return redirect()->route('admin.user.index')->with('success', 'User updated successfully.');
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

        return redirect()->route('admin.user.index')->with('success', 'User deleted successfully.');
    }
    
    public function profile()
{
    $user = Auth::user(); // Ambil data user yang sedang login
    return view('profile.profile', compact('user'));
}
    
    public function showJson(User $user)
    {
        return response()->json($user);
    }
}
