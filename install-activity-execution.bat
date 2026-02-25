@echo off
REM Installation du système d'enregistrement des activités
REM Pour Windows PowerShell/CMD

echo.
echo ========================================================
echo 🚀 Installation du système d'enregistrement
echo ========================================================
echo.

REM Vérifier PHP
echo Vérification de l'environnement...
php --version >nul 2>&1
if errorlevel 1 (
    echo ✗ PHP non trouvé. Veuillez installer PHP.
    exit /b 1
)
echo ✓ PHP trouvé

echo.
echo Vérification des fichiers...

REM Vérifier fichiers
if exist "app\Models\ActivityExecutionLog.php" (
    echo ✓ app\Models\ActivityExecutionLog.php
) else (
    echo ✗ app\Models\ActivityExecutionLog.php (manquant)
)

if exist "database\migrations\2026_02_02_000000_create_activity_execution_logs_table.php" (
    echo ✓ migration créée
) else (
    echo ✗ migration manquante
)

if exist "resources\views\partials\activity-execution-history.blade.php" (
    echo ✓ activity-execution-history.blade.php
) else (
    echo ✗ activity-execution-history.blade.php (manquant)
)

echo.
echo Exécution des migrations...
php artisan migrate

if errorlevel 1 (
    echo ✗ Erreur lors des migrations
    exit /b 1
)

echo ✓ Migrations exécutées avec succès

echo.
echo Nettoyage du cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
echo ✓ Cache nettoyé

echo.
echo ========================================================
echo ✅ INSTALLATION COMPLÈTE
echo ========================================================
echo.
echo Prochaines étapes:
echo 1. Démarrer le serveur: php artisan serve
echo 2. Ouvrir: http://127.0.0.1:8000/project/create?project_id=1
echo 3. Aller à l'onglet "5. Exper. Phase"
echo 4. Cliquer sur "Exécuter" pour une activité
echo.
echo Documentation:
echo - ACTIVITY_EXECUTION_USER_GUIDE.md
echo - IMPLEMENTATION_ACTIVITY_EXECUTION.md
echo - VISUAL_ARCHITECTURE.md
echo - CHECKLIST_IMPLEMENTATION.md
echo.
pause
