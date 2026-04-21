<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        // Middleware applied per-method so userSettings is accessible to everyone
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

    /** User-level settings (push notification opt-in). Accessible to all authenticated users. */
    public function userSettings()
    {
        return view('settings.user');
    }

    public function updateUserSettings(Request $request)
    {
        // Push notification preference is stored client-side (browser Notification API).
        // Server-side: we store whether the user wants email notifications.
        $user = auth()->user();
        $user->update([
            'email_notifications' => (bool) $request->input('email_notifications', true),
        ]);

        return redirect()->route('user.settings')->with('success', 'Préférences enregistrées.');
    }
}
