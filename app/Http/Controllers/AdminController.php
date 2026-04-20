<?php

namespace App\Http\Controllers;

use App\Models\Pro_Personnel;
use App\Models\Pro_StudyDirector;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
    }

    public function users()
    {
        $users = User::with('personnel.studyDirectorDesignation')->orderBy('nom')->get();
        $roles = User::ROLES;

        // All personnel who can be designated as SD (for the promotion UI)
        $allPersonnel = Pro_Personnel::orderBy('nom')->get();

        // IDs of personnel currently designated as active SD
        $activeSDPersonnelIds = Pro_StudyDirector::where('active', true)
            ->pluck('personnel_id')
            ->flip()
            ->toArray();

        return view('admin.users', compact('users', 'roles', 'allPersonnel', 'activeSDPersonnelIds'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:' . implode(',', array_keys(User::ROLES))],
        ]);

        $user->update(['role' => $request->role]);

        return response()->json(['success' => true, 'role_label' => $user->roleLabel()]);
    }

    /**
     * Promote a personnel member to Study Director.
     * Creates (or re-activates) a pro_study_directors record for them.
     */
    public function promoteStudyDirector(Request $request)
    {
        $request->validate(['personnel_id' => 'required|exists:personnels,id']);

        $existing = Pro_StudyDirector::where('personnel_id', $request->personnel_id)->first();

        if ($existing) {
            $existing->update(['active' => true, 'promoted_by' => Auth::id(), 'date_promotion' => now()->toDateString()]);
        } else {
            Pro_StudyDirector::create([
                'personnel_id'   => $request->personnel_id,
                'promoted_by'    => Auth::id(),
                'date_promotion' => now()->toDateString(),
                'active'         => true,
            ]);
        }

        $personnel = Pro_Personnel::find($request->personnel_id);

        return response()->json([
            'success' => true,
            'message' => trim($personnel->prenom . ' ' . $personnel->nom) . ' promoted to Study Director.',
        ]);
    }

    /**
     * Revoke Study Director designation for a personnel member.
     */
    public function demoteStudyDirector(Request $request)
    {
        $request->validate(['personnel_id' => 'required|exists:personnels,id']);

        Pro_StudyDirector::where('personnel_id', $request->personnel_id)
            ->update(['active' => false]);

        $personnel = Pro_Personnel::find($request->personnel_id);

        return response()->json([
            'success' => true,
            'message' => trim($personnel->prenom . ' ' . $personnel->nom) . ' Study Director designation revoked.',
        ]);
    }
}
