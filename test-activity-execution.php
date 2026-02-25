#!/usr/bin/env php
<?php

/**
 * Test Script for Activity Execution System
 * Run: php test-activity-execution.php
 */

echo "\n";
echo "════════════════════════════════════════════════════════════\n";
echo "   🧪 TEST SCRIPT - Activity Execution System\n";
echo "════════════════════════════════════════════════════════════\n";
echo "\n";

// Colors
$colors = [
    'green' => "\033[32m",
    'red' => "\033[31m",
    'yellow' => "\033[33m",
    'blue' => "\033[34m",
    'reset' => "\033[0m",
];

function test($name, $condition) {
    global $colors;
    $status = $condition ? "✓ PASS" : "✗ FAIL";
    $color = $condition ? $colors['green'] : $colors['red'];
    echo "{$color}{$status}{$colors['reset']} - {$name}\n";
    return $condition;
}

function section($title) {
    global $colors;
    echo "\n{$colors['blue']}→ {$title}{$colors['reset']}\n";
}

// Tests
$all_pass = true;

section("1. FICHIERS CRÉÉS");
$files_created = [
    'app/Models/ActivityExecutionLog.php',
    'database/migrations/2026_02_02_000000_create_activity_execution_logs_table.php',
    'resources/views/partials/activity-execution-history.blade.php',
];

foreach ($files_created as $file) {
    $all_pass &= test("Fichier existe: {$file}", file_exists($file));
}

section("2. FICHIERS MODIFIÉS");
$files_modified = [
    'app/Http/Controllers/ProjectAjaxController.php',
    'routes/route_ajax.php',
    'resources/views/experimental-phase-step.blade.php',
];

foreach ($files_modified as $file) {
    $all_pass &= test("Fichier existe: {$file}", file_exists($file));
}

section("3. CONTENU DES FICHIERS");

// Vérifier que le modèle a le bon namespace
if (file_exists('app/Models/ActivityExecutionLog.php')) {
    $content = file_get_contents('app/Models/ActivityExecutionLog.php');
    $all_pass &= test("Modèle a namespace", strpos($content, 'namespace App\Models') !== false);
    $all_pass &= test("Modèle extend Model", strpos($content, 'extends Model') !== false);
    $all_pass &= test("Modèle a relations", strpos($content, 'BelongsTo') !== false);
}

// Vérifier que la route AJAX est présente
if (file_exists('routes/route_ajax.php')) {
    $content = file_get_contents('routes/route_ajax.php');
    $all_pass &= test("Route /execute-activity", strpos($content, 'execute-activity') !== false);
    $all_pass &= test("Route post", strpos($content, 'post') !== false);
}

// Vérifier que le contrôleur a la méthode
if (file_exists('app/Http/Controllers/ProjectAjaxController.php')) {
    $content = file_get_contents('app/Http/Controllers/ProjectAjaxController.php');
    $all_pass &= test("Méthode executeActivity", strpos($content, 'function executeActivity') !== false);
    $all_pass &= test("Import ActivityExecutionLog", strpos($content, 'ActivityExecutionLog') !== false);
    $all_pass &= test("Validation activity_id", strpos($content, "'activity_id'") !== false);
}

// Vérifier que la vue a le modal
if (file_exists('resources/views/experimental-phase-step.blade.php')) {
    $content = file_get_contents('resources/views/experimental-phase-step.blade.php');
    $all_pass &= test("Modal HTML", strpos($content, 'executeActivityModal') !== false);
    $all_pass &= test("Fonction JS openExecuteActivityModal", strpos($content, 'openExecuteActivityModal') !== false);
    $all_pass &= test("Fonction JS saveActivityExecution", strpos($content, 'saveActivityExecution') !== false);
    $all_pass &= test("Colonne Actual Date", strpos($content, 'Actual Date') !== false);
    $all_pass &= test("Colonne Status", strpos($content, 'Status') !== false);
    $all_pass &= test("Include historique", strpos($content, 'activity-execution-history') !== false);
}

section("4. DOCUMENTATION");
$docs = [
    'README_ACTIVITY_EXECUTION.md',
    'QUICK_START.md',
    'ACTIVITY_EXECUTION_USER_GUIDE.md',
    'IMPLEMENTATION_ACTIVITY_EXECUTION.md',
    'VISUAL_ARCHITECTURE.md',
    'CHECKLIST_IMPLEMENTATION.md',
    'FINAL_SUMMARY.md',
    'TABLE_OF_CONTENTS.md',
];

foreach ($docs as $doc) {
    $all_pass &= test("Doc existe: {$doc}", file_exists($doc));
}

section("5. SCRIPTS D'INSTALLATION");
$scripts = [
    'install-activity-execution.sh',
    'install-activity-execution.bat',
];

foreach ($scripts as $script) {
    $all_pass &= test("Script existe: {$script}", file_exists($script));
}

section("6. STRUCTURE DE BASE DE DONNÉES");
echo "   Note: Vérifiez avec: php artisan migrate:status\n";
echo "   La table activity_execution_logs doit être visible\n";

section("7. ROUTES AJAX");
echo "   Vérifiez avec: php artisan route:list | grep execute-activity\n";

section("8. TESTS MANUELS");
echo "   1. Ouvrir: http://127.0.0.1:8000/project/create?project_id=1\n";
echo "   2. Onglet: 5. Exper. Phase\n";
echo "   3. Cliquer: \"Exécuter\" sur une activité\n";
echo "   4. Remplir le modal et soumettre\n";
echo "   5. Vérifier que les données sont enregistrées\n";

echo "\n";
echo "════════════════════════════════════════════════════════════\n";
if ($all_pass) {
    echo "{$colors['green']}✅ TOUS LES TESTS PASSÉS!{$colors['reset']}\n";
} else {
    echo "{$colors['red']}⚠️  CERTAINS TESTS ONT ÉCHOUÉ{$colors['reset']}\n";
}
echo "════════════════════════════════════════════════════════════\n";
echo "\n";

// Next steps
echo "{$colors['blue']}PROCHAINES ÉTAPES:{$colors['reset']}\n";
echo "1. php artisan migrate\n";
echo "2. Démarrer le serveur: php artisan serve\n";
echo "3. Tester sur: http://127.0.0.1:8000/project/create?project_id=1\n";
echo "4. Aller à l'onglet 5 et tester le système\n";
echo "\n";

exit($all_pass ? 0 : 1);
