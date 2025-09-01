<?php

namespace App\Http\Controllers;

use App\Models\Pro_Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WizardController extends Controller
{
    //
    public function index()
{
   

    return view('manage-project', compact('projectsCount','activeUsers','totalBudget','tasksInProgress','months','budgetsByMonth'));
}


     public function submit(Request $request)
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'email'        => 'required|email',
            'phone'        => 'nullable|string|max:50',
            'project_name' => 'required|string|max:255',
            'category'     => 'required|string',
            'budget'       => 'nullable|numeric',
            'description'  => 'nullable|string',
            'file'         => 'nullable|file|max:2048',
        ]);

        if($request->hasFile('file')){
            $path = $request->file('file')->store('uploads','public');
            $validated['file_path'] = $path;
        }

        // Ici vous pouvez sauvegarder en base de données ou envoyer un email
        // Exemple: MyModel::create($validated);

        return redirect('/wizard')->with('success', 'Formulaire soumis avec succès !');
    }
}
