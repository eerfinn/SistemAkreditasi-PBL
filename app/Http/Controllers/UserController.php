<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        // Check if there's a role filter and format request
        if (request()->has('role') && request()->has('format') && request()->format === 'json') {
            $users = User::where('role', request()->role)->get();

            // Transform data for JSON response
            $users->transform(function($user) {
                return [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'kriteria_access' => $user->kriteria_access ?? []
                ];
            });

            return response()->json($users);
        }

        // Regular web request
        $users = User::all();
        return view('pages.admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = ['administrator', 'dosen', 'koordinator', 'kjm', 'kaprodi', 'kajur', 'direktur'];
        $kriteria = Kriteria::all();
        return view('pages.admin.users.create', compact('roles', 'kriteria'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:administrator,dosen,koordinator,kjm,kaprodi,kajur,direktur',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        // Set kriteria access based on role
        if ($validated['role'] === 'administrator') {
            $validated['kriteria_access'] = null;
        } else if ($validated['role'] === 'dosen') {
            // For new dosen, start with no kriteria access
            $validated['kriteria_access'] = [];
        } else {
            // For all other roles, give access to all kriteria
            $allKriteriaIds = Kriteria::pluck('id')->toArray();
            $validated['kriteria_access'] = $allKriteriaIds;
        }

        $user = User::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'user' => $user
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('pages.admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = ['administrator', 'dosen', 'koordinator', 'kjm', 'kaprodi', 'kajur', 'direktur'];
        $kriteria = Kriteria::all();
        return view('pages.admin.users.edit', compact('user', 'roles', 'kriteria'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'role' => 'required|in:administrator,dosen,koordinator,kjm,kaprodi,kajur,direktur',
            'kriteria_access' => 'nullable|array',
            'kriteria_access.*' => 'integer|exists:kriteria,id',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        }

        // Administrators don't need explicit kriteria access
        if ($validated['role'] === 'administrator') {
            $validated['kriteria_access'] = null;
        }
        // Only dosen need specific kriteria access
        else if ($validated['role'] === 'dosen') {
            // For dosen, set the kriteria access as provided
            $validated['kriteria_access'] = $request->kriteria_access ?? null;
        }
        // For all other roles, give access to all kriteria
        else {
            $allKriteriaIds = Kriteria::pluck('id')->toArray();
            $validated['kriteria_access'] = $allKriteriaIds;
        }

        $user->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user' => $user
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting the last administrator
        if ($user->role === 'administrator' && User::where('role', 'administrator')->count() <= 1) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the last administrator account.'
                ], 403);
            }
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete the last administrator account.');
        }

        // Check if trying to delete own account
        if (Auth::id() === $user->id) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account.'
                ], 403);
            }
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete your own account.');
        }

        $user->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function showJson(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update kriteria access for a user
     */
    public function updateKriteriaAccess(Request $request, User $user)
    {
        $request->validate([
            'kriteria_access' => 'nullable|array',
            'kriteria_access.*' => 'integer|exists:kriteria,id',
        ]);

        // Administrators don't need explicit kriteria access as they have access to all
        if ($user->role === 'administrator') {
            $user->kriteria_access = null;
        }
        // Only dosen need specific kriteria access, others get all access
        else if ($user->role === 'dosen') {
            // For dosen, set the kriteria access as provided
            $user->kriteria_access = $request->kriteria_access ?? [];
        }
        else {
            // For all other roles (koordinator, kjm, kaprodi, kajur), give access to all kriteria
            $allKriteriaIds = Kriteria::pluck('id')->toArray();
            $user->kriteria_access = $allKriteriaIds;
        }

        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kriteria access updated successfully.',
                'user' => $user
            ]);
        }

        return redirect()->back()->with('success', 'Kriteria access updated successfully.');
    }

    /**
     * Get kriteria names by IDs
     */
    public function getKriteriaNames(Request $request)
    {
        $ids = $request->get('ids', []);
        $kriteria = Kriteria::whereIn('id', $ids)->get(['id', 'nama_kriteria']);

        return response()->json($kriteria);
    }
}
