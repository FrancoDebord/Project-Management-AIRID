<?php

namespace App\Http\Controllers;

use App\Models\Pro_Project;
use App\Models\User;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    //

    function __construct() {}

    function indexPage(Request $request)
    {

        $projectsCount = Pro_Project::count();
        $activeUsers = User::where('active', true)->count();
        $totalBudget = 0;
        $tasksInProgress = Pro_Project::where('project_stage', 'in progress')->count();

        // Groupement des budgets par mois
        $budgets = Pro_Project::selectRaw('MONTH(created_at) as month, 23 as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];
        $budgetsByMonth = [];
        for ($i = 1; $i <= 12; $i++) {
            $budgetsByMonth[] = $budgets[$i] ?? 0;
        }


        $all_projects = Pro_Project::orderBy("date_debut_effective", "desc")->get();

        return view("accueil", compact("all_projects", 'projectsCount', 'activeUsers', 'totalBudget', 'tasksInProgress', 'months', 'budgetsByMonth'));
    }
}
