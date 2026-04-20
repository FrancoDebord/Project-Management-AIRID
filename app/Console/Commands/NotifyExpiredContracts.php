<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Pro_Personnel;
use App\Models\Pro_Project;
use App\Models\Pro_StudyActivities;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Checks for personnel whose contracts have expired (date_fin_contrat < today)
 * and who still have unexecuted activities assigned.
 *
 * For each affected project, notifies the Study Director so they can
 * reassign those activities to someone with an active contract.
 */
class NotifyExpiredContracts extends Command
{
    protected $signature   = 'airid:notify-expired-contracts';
    protected $description = 'Notify Study Directors when a team member\'s contract has expired and they still have unexecuted activities.';

    public function handle(): int
    {
        $today = Carbon::today();

        // Find personnel whose contracts have expired (date set and in the past)
        $expiredPersonnel = Pro_Personnel::where('sous_contrat', 1)
            ->whereNotNull('date_fin_contrat')
            ->where('date_fin_contrat', '<', $today->toDateString())
            ->get();

        if ($expiredPersonnel->isEmpty()) {
            $this->info('No expired contracts found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$expiredPersonnel->count()} person(s) with expired contracts. Checking for unexecuted activities…");

        // Group unexecuted activities by project → SD
        // Structure: [project_id => ['project' => ..., 'sd_user' => ..., 'issues' => [...]]]
        $alerts = [];

        foreach ($expiredPersonnel as $person) {
            $fullName = trim($person->prenom . ' ' . $person->nom);
            $expiry   = Carbon::parse($person->date_fin_contrat)->format('d/m/Y');

            // Find unexecuted activities assigned to this person
            $activities = Pro_StudyActivities::where('should_be_performed_by', $person->id)
                ->where('status', '!=', 'completed')
                ->whereNull('actual_activity_date')
                ->with('project')
                ->get();

            foreach ($activities as $act) {
                if (!$act->project || !$act->project->study_director) continue;

                $projectId = $act->project_id;

                if (!isset($alerts[$projectId])) {
                    $sdUser = User::whereHas('personnel', fn($q) => $q->where('id', $act->project->study_director))->first();
                    if (!$sdUser) continue;

                    $alerts[$projectId] = [
                        'project' => $act->project,
                        'sd_user' => $sdUser,
                        'issues'  => [],
                    ];
                }

                $alerts[$projectId]['issues'][] = "• {$act->study_activity_name} — assignée à {$fullName} (contrat expiré le {$expiry})";
            }
        }

        if (empty($alerts)) {
            $this->info('No unexecuted activities found for expired-contract personnel.');
            return Command::SUCCESS;
        }

        foreach ($alerts as $projectId => $alert) {
            $project = $alert['project'];
            $sdUser  = $alert['sd_user'];
            $issues  = $alert['issues'];

            $title = "⚠ Contrat expiré — activités à réassigner ({$project->project_code})";
            $body  = "Des activités sur le projet {$project->project_code} sont assignées à des personnes dont le contrat a expiré. Veuillez les réassigner :\n\n"
                   . implode("\n", $issues);
            $url   = url("/project/{$project->id}/overview");

            AppNotification::send($sdUser->id, $title, $body, $url);

            $this->info("Notified {$sdUser->email} about " . count($issues) . " issue(s) on {$project->project_code}.");
        }

        $this->info('Done.');
        return Command::SUCCESS;
    }
}
