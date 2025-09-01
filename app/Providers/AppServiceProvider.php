<?php

namespace App\Providers;

use App\Models\Pro_Personnel;
use App\Models\Pro_Project;
use App\Models\User;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //

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

        $all_personnels = Pro_Personnel::orderBy("prenom", "asc")->get();
        view()->share('all_personnels', $all_personnels);


        view()->share('projectsCount', $projectsCount);
        view()->share('activeUsers', $activeUsers);
        view()->share('totalBudget', $totalBudget); 
        view()->share('tasksInProgress', $tasksInProgress);
        view()->share('months', $months);
        view()->share('budgetsByMonth', $budgetsByMonth);
        
    }
}
