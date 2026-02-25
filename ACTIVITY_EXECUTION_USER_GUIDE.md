# 🎯 Système d'Enregistrement des Activités Réalisées - Guide d'utilisation

## Vue d'ensemble

Ce système permet d'enregistrer les activités réalisées dans la phase expérimentale (Exper Phase) d'un projet. Il trace complètement le cycle de vie de chaque activité : de la planification à l'exécution.

## 📍 Localisation

**URL**: http://127.0.0.1:8000/project/create?project_id=X

**Onglet**: `5. Exper. Phase` (Experimental Phase)

## ✨ Fonctionnalités principales

### 1️⃣ **Liste des activités programmées**
- Affiche toutes les activités créées dans "Protocol Details"
- Colonnes affichées:
  - **Activity** : Nom de l'activité
  - **Date Start** : Date de début prévue
  - **Date End** : Date de fin prévue
  - **Actual Date** : Date réelle d'exécution (remplie après enregistrement)
  - **Status** : État actuel de l'activité
  - **Parent Activity** : Activité parent si applicable
  - **Assigned to** : Personne responsable
  - **Mark** : Bouton d'action

### 2️⃣ **Enregistrement d'une exécution**
Cliquez sur le bouton **"Exécuter"** pour enregistrer qu'une activité a été réalisée.

#### Modal de capture des données:
```
┌─────────────────────────────────────────┐
│ Enregistrer l'exécution de l'activité  │
├─────────────────────────────────────────┤
│ Activité: [Nom de l'activité]          │
│                                         │
│ Date d'exécution réelle*:              │
│ [📅 Sélectionner une date]             │
│                                         │
│ Exécuté par*:                          │
│ [▼ Sélectionner une personne]          │
│                                         │
│ Commentaires:                          │
│ [📝 Texte libre...]                    │
│                                         │
│      [Annuler] [Enregistrer l'exécution]│
└─────────────────────────────────────────┘
```

#### Champs du formulaire:

| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| Date d'exécution réelle | Date | ✅ | Date exacte à laquelle l'activité a été réalisée |
| Exécuté par | Dropdown | ✅ | Sélection de la personne qui a exécuté l'activité |
| Commentaires | Textarea | ❌ | Notes supplémentaires sur l'exécution |

### 3️⃣ **Statuts des activités**

Les badges de statut utilisent les couleurs suivantes:

- 🟢 **Completed** : Activité réalisée
- 🟡 **In Progress** : Activité en cours
- 🔴 **Delayed** : Activité en retard
- ⚪ **Pending** : En attente

### 4️⃣ **Calendrier FullCalendar**
- Vue graphique des phases critiques
- Interaction possible (sélection, édition)
- Synchronisation avec la base de données

### 5️⃣ **Historique des exécutions**
- Tableau complet de toutes les exécutions enregistrées
- Colonnes: Activité, Date, Exécutée par, Statut, Commentaires, Date d'enregistrement

## 🛠️ Processus d'installation

### Étape 1: Exécuter la migration
```bash
cd e:\Projets_CREC\project-management-lshtm
php artisan migrate
```

Cette commande crée la table `activity_execution_logs` dans la base de données.

### Étape 2: Vérifier les fichiers
Vérifiez que les fichiers suivants existent:

- ✅ `app/Models/ActivityExecutionLog.php` (modèle)
- ✅ `app/Http/Controllers/ProjectAjaxController.php` (contrôleur, méthode `executeActivity`)
- ✅ `routes/route_ajax.php` (route AJAX)
- ✅ `resources/views/experimental-phase-step.blade.php` (vue principale)
- ✅ `resources/views/partials/activity-execution-history.blade.php` (vue historique)
- ✅ `database/migrations/2026_02_02_000000_create_activity_execution_logs_table.php` (migration)

### Étape 3: Redémarrer le serveur (si nécessaire)
```bash
php artisan serve
```

## 📊 Flux d'utilisation complet

```
┌─────────────────────────────────────────────────────────┐
│  1. Créer un projet et protocole                        │
│     (Onglets 1-4)                                       │
└─────────────────────────┬───────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│  2. Ajouter les activités dans "Protocol Details"      │
│     (Onglet 2: Protocol Details)                        │
└─────────────────────────┬───────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│  3. Visualiser les activités dans "Exper. Phase"       │
│     (Onglet 5: Exper. Phase)                            │
│     - Tableau des activités                             │
│     - Calendrier                                        │
└─────────────────────────┬───────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│  4. Enregistrer l'exécution d'une activité             │
│     - Cliquer sur "Exécuter"                            │
│     - Remplir le formulaire                             │
│     - Soumettre                                         │
└─────────────────────────┬───────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│  5. Voir l'historique d'exécution                       │
│     - Table "Activity Execution History"                │
│     - Vérifier les dates et responsables                │
└─────────────────────────────────────────────────────────┘
```

## 🔄 Données enregistrées

Quand vous enregistrez une exécution, les données suivantes sont sauvegardées:

### Dans `pro_studies_activities`:
```
- actual_activity_date: Date d'exécution réelle
- performed_by: ID de la personne qui a exécuté
- status: "completed"
- description: Ajout des commentaires (+ ancienne description)
```

### Dans `activity_execution_logs`:
```
- activity_id: ID de l'activité
- project_id: ID du projet
- execution_date: Date d'exécution
- executed_by: ID de la personne
- comments: Commentaires
- status: "completed" (peut être étendu: partial, failed, etc.)
- created_at/updated_at: Timestamps
```

## 🔒 Sécurité & Validation

### Validations côté serveur:
- ✅ L'activité doit exister
- ✅ La date doit être valide
- ✅ La personne doit exister
- ✅ Le projet doit exister
- ✅ Token CSRF validé automatiquement

### Restrictions:
- 🔐 Authentification requise (login)
- 🔐 Seuls les utilisateurs authentifiés peuvent accéder
- 🔐 Foreign keys pour garantir l'intégrité

## 📱 Exemple d'utilisation

### Scénario: Enregistrer l'exécution d'une activité

1. **Connexion**: Connectez-vous avec vos identifiants
2. **Sélection du projet**: Onglet 5, sélectionnez un projet existant
3. **Visualisation**: Le tableau affiche toutes les activités programmées
4. **Clic sur "Exécuter"**: Le modal s'ouvre pour votre activité
5. **Remplissage**:
   - Date d'exécution: `15/02/2026`
   - Exécuté par: `Jean Dupont`
   - Commentaires: `Activité réalisée avec succès, tous les tests passés`
6. **Soumission**: Cliquez sur "Enregistrer l'exécution"
7. **Confirmation**: Message de succès, puis rechargement de la page
8. **Vérification**: 
   - La colonne "Actual Date" affiche `15/02/2026`
   - Le badge "Status" affiche `✓ Completed`
   - L'historique en bas montre l'entrée avec tous les détails

## 🐛 Troubleshooting

### Problème: Le bouton "Exécuter" ne fonctionne pas
- ✅ Vérifiez que vous êtes connecté
- ✅ Vérifiez que JavaScript est activé
- ✅ Vérifiez la console du navigateur (F12) pour les erreurs

### Problème: Erreur lors de la soumission du formulaire
- ✅ Vérifiez que la migration a été exécutée
- ✅ Vérifiez que la personne existe dans la base de données
- ✅ Consultez le log Laravel: `storage/logs/laravel.log`

### Problème: La date n'est pas sauvegardée
- ✅ Vérifiez le format de la date (doit être YYYY-MM-DD)
- ✅ Vérifiez que le champ n'est pas vide

### Problème: L'historique ne s'affiche pas
- ✅ Vérifiez que la migration a créé la table `activity_execution_logs`
- ✅ Exécutez: `php artisan migrate --fresh` (attention: cela réinitialise la DB)

## 📞 Support & Rapports de bugs

Pour tout problème:
1. Vérifiez les logs: `storage/logs/laravel.log`
2. Testez avec des données simples
3. Vérifiez les messages d'erreur dans la console navigateur (F12)

## 📚 Documentation technique

Pour les détails techniques complets, voir:
- [IMPLEMENTATION_ACTIVITY_EXECUTION.md](./IMPLEMENTATION_ACTIVITY_EXECUTION.md)
- [Modèle ActivityExecutionLog](./app/Models/ActivityExecutionLog.php)
- [Contrôleur ProjectAjaxController](./app/Http/Controllers/ProjectAjaxController.php)
