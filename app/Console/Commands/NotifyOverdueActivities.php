<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Pro_Project;
use App\Models\Pro_StudyActivities;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class NotifyOverdueActivities extends Command
{
    protected $signature   = 'airid:notify-overdue-activities';
    protected $description = 'Send daily notifications to Study Directors listing their overdue (past due-date, not completed) activities.';

    public function handle(): int
    {
        $today = Carbon::today()->toDateString();

        // Fetch all active (non-archived) projects that have a Study Director assigned
        $projects = Pro_Project::whereNotNull('study_director')
            ->whereNull('archived_at')
            ->with(['studyDirector'])
            ->get();

        $totalNotified = 0;

        foreach ($projects as $project) {
            // Get overdue activities for this project
            $overdue = Pro_StudyActivities::where('project_id', $project->id)
                ->where('status', '!=', 'completed')
                ->whereNull('actual_activity_date')
                ->where('estimated_activity_end_date', '<', $today)
                ->orderBy('estimated_activity_end_date')
                ->get();

            if ($overdue->isEmpty()) {
                continue;
            }

            // Find the User account linked to the Study Director's personnel record
            $sdUser = User::whereHas('personnel', fn($q) => $q->where('id', $project->study_director))
                ->first();

            if (!$sdUser) {
                $this->warn("No user account found for Study Director of project {$project->project_code}. Skipping.");
                continue;
            }

            // Build the notification body
            $count = $overdue->count();
            $title = "Overdue Activities — {$project->project_code}";
            $lines = ["You have {$count} overdue activity/activities on project {$project->project_code}:"];
            foreach ($overdue as $act) {
                $due     = Carbon::parse($act->estimated_activity_end_date)->format('d/m/Y');
                $lines[] = "• [{$due}] {$act->study_activity_name}";
            }
            $body = implode("\n", $lines);
            $url  = url("/project/{$project->id}/overview");

            // Send platform notification (AppNotification::send also queues an email)
            AppNotification::send($sdUser->id, $title, $body, $url);
            $totalNotified++;

            $this->info("Notified {$sdUser->email} about {$count} overdue activity/activities on {$project->project_code}.");
        }

        $this->info("Done. Notified Study Directors for {$totalNotified} project(s).");
        return Command::SUCCESS;
    }
}
