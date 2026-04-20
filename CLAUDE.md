# AIRID Project Management System — Claude Code Guide

## Project Overview
Laravel 11 application for AIRID (African Institute for Research in Infectious Diseases) to manage GLP and non-GLP research studies. Covers project lifecycle: planning → protocol development → experimental → reporting → archiving, plus QA inspection management.

## Stack
- **Backend**: Laravel 11, PHP 8.2, MySQL
- **Frontend**: Bootstrap 5, jQuery (in `public/storage/assets/js/javascript_ajax.js`), vanilla JS
- **PDF**: barryvdh/laravel-dompdf v3.1.2 — `Pdf::loadView('pdf.view-name', compact(...))`
- **Email**: Gmail SMTP via Laravel Mailable + queue (`MAIL_MAILER=smtp`, port 587, TLS)
- **Bootstrap**: `bootstrap/app.php` (no RouteServiceProvider)

## Key Conventions

### Routes
- Standard web routes: `routes/web.php`
- AJAX routes: `routes/route_ajax.php` — loaded via `then:` callback in `bootstrap/app.php`
- Console schedules: `routes/console.php`

### Models
All project-related models use the `Pro_` prefix (e.g. `Pro_Project`, `Pro_StudyActivities`).

### Notifications
Use `AppNotification::send($userId, $title, $body, $url)` for in-app notifications.
This automatically also sends an email to the recipient (via `App\Mail\AppNotificationMail`).

### JavaScript
- All jQuery AJAX form handlers are in `public/storage/assets/js/javascript_ajax.js`
- Vanilla JS in-page scripts go at the bottom of their respective Blade views
- No double-submit: never register a `submit` handler in both a partial and `javascript_ajax.js`

### Protocol Dev Activities
Levels in `pro_protocols_devs_activities`:
- Level 1 — "SD uploads Draft Protocol" (creates QA inspection automatically)
- Level 2 — "QA Inspection of Draft" (**hidden** from Protocol Dev tab — QA manages it)
- Level 3 — "Final Approved Protocol (signed)" (creates QA inspection automatically)
- Level 4 — "QA Inspection of Final Protocol" (**hidden** from Protocol Dev tab)
- Level 5 — "Protocol Amendment/Deviation" (creates QA inspection automatically)

QA inspections are auto-created for **all** projects (not just GLP) when documents are uploaded at levels 1, 3, 5.

## Database — Notable Tables
| Table | Model | Purpose |
|-------|-------|---------|
| `pro_projects` | `Pro_Project` | Main project record |
| `pro_studies_activities` | `Pro_StudyActivities` | Activities per project |
| `pro_studies_initiation_meetings` | `Pro_StudyQualityAssuranceMeeting` | Planning phase meeting |
| `pro_protocols_devs_activities` | `Pro_ProtocolDevActivity` | Protocol dev activity definitions |
| `pro_protocol_dev_activities_project` | `Pro_ProtocolDevActivityProject` | Per-project protocol dev state |
| `pro_protocol_dev_documents` | `Pro_ProtocolDevDocument` | Uploaded protocol docs (has `qa_inspection_id`) |
| `pro_qa_inspections` | `Pro_QaInspection` | QA inspection records |
| `pro_app_notifications` | `AppNotification` | In-app notifications |
| `pro_studies_types` | `Pro_StudyType` | Study types (GLP, non-GLP, Implementation Research…) |
| `pro_study_directors` | `Pro_StudyDirector` | Personnel designated as Study Directors (scientific eligibility) |
| `personnels` | `Pro_Personnel` | All personnel — `sous_contrat=1` = active contract, `date_fin_contrat` = expiry date |

## Study Director Designation vs. User Role

These are two **independent** concepts:
- `users.role = 'study_director'` → system access/permissions (login)
- `pro_study_directors` table → scientific eligibility to be appointed Study Director on a project

Only personnel with an active record in `pro_study_directors` (`active = 1`) appear in:
- The "Study Director" select of the SD Appointment Form
- The "study_director" field when creating/editing a project

Admin route for managing designations:
- `POST /admin/study-directors/promote` → `AdminController::promoteStudyDirector()`
- `POST /admin/study-directors/demote` → `AdminController::demoteStudyDirector()`
- UI: `/admin/users` has a new "Study Director" column with promote/demote buttons

## Personnel Contract Filtering

All personnel selection dropdowns (activity assignment, key personnel, meeting participants, project manager) filter to `sous_contrat = 1` only. The `date_fin_contrat` column (added via migration `2026_04_20_161548`) tracks contract end dates.

When a contract expires while the person still has unexecuted activities, the Study Director is notified daily (see `airid:notify-expired-contracts`).

## Recent Changes (April 2026)

### Mail / Email Notifications
- Gmail SMTP configured in `.env` (airidafrica@gmail.com, port 587, TLS)
- `AppNotification::send()` now also queues an email via `App\Mail\AppNotificationMail`
- Email template: `resources/views/emails/app-notification.blade.php`

### New Study Type
- "Implementation Research" added to `pro_studies_types` via migration `2026_04_20_000001`

### Study Director Appointment Form PDF
- PDF template: `resources/views/pdf/sd-appointment-form.blade.php`
- Route: `GET /pdf/sd-appointment-form?project_id=X` → name `pdf.sd-appointment-form`
- Controller: `FrontendController::sdAppointmentFormPdf()`
- "Download PDF" button shown in `partials/study_director_appointment_form.blade.php` when form data exists

### Default Study Director in Activity Modal
- `ProjectAjaxController::getStudyTypeById()` now returns `study_director_id` in JSON
- `javascript_ajax.js` auto-selects the Study Director in `#should_be_performed_by` after populating the select

### Default Responsible at Activity Execution
- `openExecuteActivityModal(activityId, activityName, responsibleId)` now accepts a 3rd param
- The execute button in `experimental-phase-step.blade.php` passes `$activite->should_be_performed_by`
- `performedBySelect` is pre-set to the activity's responsible person

### Protocol Dev — QA Integration
- Levels 2 & 4 (QA-only) are **hidden** from the Protocol Dev tab
- QA inspections are auto-created for all projects (removed `$isGlp` restriction)
- Inspection column shows: status badge (Done/Planned), Checklist link, findings count

### Planning Phase — Meeting Report
- New columns on `pro_studies_initiation_meetings`: `report_file_path`, `report_content`, `report_date`, `report_redacted_by`
- Migration: `2026_04_20_000002`
- UI: report card with PDF upload + text textarea in `planning-phase-step.blade.php`
- Route: `POST /ajax/save-meeting-report` → `ProjectAjaxController::saveMeetingReport()`

### Daily Overdue-Activity Notifications
- Command: `php artisan airid:notify-overdue-activities`
- Scheduled at 07:00 daily in `routes/console.php`
- Sends platform notification + email to Study Director of each project with overdue activities
- Activities are overdue if: `estimated_activity_end_date < today` AND `status != completed` AND `actual_activity_date IS NULL`

## Running the Scheduler (production)
Add to crontab:
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### Study Director Designation + Contract Filtering
- New table `pro_study_directors` (migration `2026_04_20_161547`) with columns: `personnel_id`, `promoted_by`, `date_promotion`, `active`, `notes`
- New column `personnels.date_fin_contrat` (migration `2026_04_20_161548`)
- New model `Pro_StudyDirector` (`app/Models/Pro_StudyDirector.php`)
- Updated `Pro_Personnel` model with `sous_contrat`, `date_fin_contrat` casts and `isStudyDirector()` helper
- Admin users page (`/admin/users`) now shows SD designation status with promote/demote buttons
- All personnel selects (activities, key personnel, PM, meeting participants) now filter to `sous_contrat = 1`
- SD appointment form: SD select shows only active `pro_study_directors` records; PM select shows `sous_contrat = 1`
- Contract expiry command: `php artisan airid:notify-expired-contracts` — scheduled at 07:05 daily

## Key Files
| File | Purpose |
|------|---------|
| `public/storage/assets/js/javascript_ajax.js` | Central jQuery AJAX handlers |
| `app/Http/Controllers/ProjectAjaxController.php` | All AJAX endpoints |
| `app/Http/Controllers/FrontendController.php` | Page controllers + PDF export |
| `app/Http/Controllers/AdminController.php` | User management + SD designation |
| `app/Models/AppNotification.php` | In-app + email notification sender |
| `app/Models/Pro_StudyDirector.php` | Study Director designation records |
| `app/Console/Commands/NotifyOverdueActivities.php` | Daily overdue notification command |
| `app/Console/Commands/NotifyExpiredContracts.php` | Daily expired-contract notification command |
| `routes/route_ajax.php` | AJAX route definitions |
| `routes/console.php` | Scheduled commands |
