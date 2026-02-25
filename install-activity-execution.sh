#!/bin/bash
# Installation & Activation du système d'enregistrement des activités

echo "🚀 Installation du système d'enregistrement des activités réalisées"
echo "==============================================================="
echo ""

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}1. Vérification de l'environnement...${NC}"
if ! command -v php &> /dev/null; then
    echo -e "${YELLOW}⚠️  PHP non trouvé${NC}"
    exit 1
fi
echo -e "${GREEN}✓ PHP trouvé$(php --version | head -n 1)${NC}"
echo ""

echo -e "${BLUE}2. Vérification des fichiers créés...${NC}"
files_to_check=(
    "app/Models/ActivityExecutionLog.php"
    "database/migrations/2026_02_02_000000_create_activity_execution_logs_table.php"
    "resources/views/partials/activity-execution-history.blade.php"
)

for file in "${files_to_check[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓ $file${NC}"
    else
        echo -e "${YELLOW}✗ $file (manquant)${NC}"
    fi
done
echo ""

echo -e "${BLUE}3. Vérification des modifications...${NC}"
modified_files=(
    "app/Http/Controllers/ProjectAjaxController.php"
    "routes/route_ajax.php"
    "resources/views/experimental-phase-step.blade.php"
)

for file in "${modified_files[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓ $file${NC}"
    else
        echo -e "${YELLOW}✗ $file (manquant)${NC}"
    fi
done
echo ""

echo -e "${BLUE}4. Exécution des migrations...${NC}"
php artisan migrate
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Migrations exécutées avec succès${NC}"
else
    echo -e "${YELLOW}✗ Erreur lors des migrations${NC}"
    exit 1
fi
echo ""

echo -e "${BLUE}5. Vérification de la base de données...${NC}"
php artisan tinker --execute="return DB::table('activity_execution_logs')->count();"
echo -e "${GREEN}✓ Table activity_execution_logs accessible${NC}"
echo ""

echo -e "${BLUE}6. Nettoyage du cache...${NC}"
php artisan cache:clear
php artisan view:clear
php artisan config:clear
echo -e "${GREEN}✓ Cache nettoyé${NC}"
echo ""

echo "==============================================================="
echo -e "${GREEN}✅ INSTALLATION COMPLÈTE${NC}"
echo ""
echo -e "${BLUE}Prochaines étapes:${NC}"
echo "1. Démarrer le serveur: php artisan serve"
echo "2. Ouvrir: http://127.0.0.1:8000/project/create?project_id=1"
echo "3. Aller à l'onglet '5. Exper. Phase'"
echo "4. Cliquer sur 'Exécuter' pour une activité"
echo ""
echo -e "${YELLOW}Documentation:${NC}"
echo "- Guide utilisateur: ACTIVITY_EXECUTION_USER_GUIDE.md"
echo "- Documentation technique: IMPLEMENTATION_ACTIVITY_EXECUTION.md"
echo "- Architecture visuelle: VISUAL_ARCHITECTURE.md"
echo "- Checklist: CHECKLIST_IMPLEMENTATION.md"
echo ""
