<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pro_Personnel;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
    }

    public function users()
    {
        $users = User::with('personnel')->orderBy('nom')->get();
        $roles = User::ROLES;
        return view('admin.users', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:' . implode(',', array_keys(User::ROLES))],
        ]);

        $user->update(['role' => $request->role]);

        return response()->json(['success' => true, 'role_label' => $user->roleLabel()]);
    }
}
