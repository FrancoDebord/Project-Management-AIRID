<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Daily overdue-activity notifications ──────────────────────────────────────
// Runs every day at 07:00 and sends Study Directors the list of their overdue
// activities (both as a platform notification and an email).
Schedule::command('airid:notify-overdue-activities')->dailyAt('07:00');

// ── Contract expiry notifications ──────────────────────────────────────────────
// Runs every day at 07:05 and notifies Study Directors when a team member's
// contract has expired and they still have unexecuted activities assigned.
Schedule::command('airid:notify-expired-contracts')->dailyAt('07:05');
