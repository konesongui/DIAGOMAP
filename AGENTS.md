# Diagoma Agent Instructions

## Project Snapshot
- CodeIgniter 3.x PHP application for a multi-tenant school/organization ERP.
- Entry point: `index.php`; app runs behind Apache/WAMP at `http://localhost/diagoma/`.
- No build step is required for normal development.

## Start Here
- Prefer the app structure already in place under `application/controllers/`, `application/models/`, and `application/views/admin/`.
- Before changing behavior, check whether an existing controller, model, view, route, or helper already handles the flow.
- Link to documentation instead of duplicating it: [README.md](README.md), [QRCODE_ATTENDANCE_GUIDE.md](QRCODE_ATTENDANCE_GUIDE.md), [RH_INTEGRATION_NOTES.md](RH_INTEGRATION_NOTES.md), [CHECKLIST_INSTALLATION.txt](CHECKLIST_INSTALLATION.txt).

## Non-Negotiable Conventions
- Always scope tenant data by `entreprise_id` in reads, updates, and deletes.
- Never trust posted tenant or settings IDs when the current session can provide them.
- Keep strict SQL mode in mind: explicit defaults are required for `NOT NULL` columns on insert.
- Use existing `MY_Model` helpers and logging patterns when they fit the change.
- Preserve legacy compatibility for logo/image helpers that are expected to both return and echo values.
- Watch for staff `employee_id` collisions; generation must remain unique under retries.

## Common Project Patterns
- Controllers are usually thin and delegate data work to models.
- Admin pages frequently use DataTables and AJAX-driven CRUD.
- Views often expect `error_message` to be set when a user-facing failure occurs.
- QR attendance and related integrations already exist; reuse their routes, models, and helpers where relevant.

## Useful Commands
- Install dependencies: `composer install`
- Syntax check a PHP file: `php -l path\to\file.php`
- Run the app locally: browse to `http://localhost/diagoma/`

## When Editing
- Keep changes minimal and consistent with the surrounding CodeIgniter style.
- Avoid broad refactors unless they are necessary for the requested fix.
- Check `application/config/routes.php` before adding or renaming routes.
- Check `application/config/database.php` and related config before assuming environment defaults.

## Notes for Future Work
- The repository memory already contains useful project facts in [AGENTS-knowledge-base.md](/memories/repo/AGENTS-knowledge-base.md); keep this file focused on agent behavior rather than duplicating that knowledge.
- If a task repeatedly touches a specific area, consider a narrower instruction file later for that module only.
