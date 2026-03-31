<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin,facility_manager']);
    }

    public function index()
    {
        $grouped = AppSetting::grouped();

        $groupLabels = [
            'documents'    => ['label' => 'Documents contrôlés', 'icon' => 'bi-file-earmark-text'],
            'organisation' => ['label' => 'Organisation',         'icon' => 'bi-building'],
        ];

        return view('admin.settings', compact('grouped', 'groupLabels'));
    }

    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            AppSetting::set($key, $value ?: null);
        }

        return redirect()->route('admin.settings')->with('success', 'Paramètres enregistrés avec succès.');
    }
}
