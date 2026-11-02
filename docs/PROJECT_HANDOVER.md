# FieldOps — Project Handover

## Current milestone and task

Identity/access foundation. Tasks 01–07 complete. Task 07 controlled staff access,
first-Administrator bootstrap and audit foundation results and verification gaps
approved on 2026-11-02. User explicitly authorized all continuity-file updates.
TASK_LOG.md preserves historical checkpoints and execution evidence.

## Collaboration

Read AGENTS.md, this handover and relevant docs/source before proposing work.
Developer writes application code and runs installation, migration and checks;
assistant explains/reviews. Documentation editing authorized for this closeout
only. No implicit permission to implement/commit subsequent tasks.
Batch meaningful work and reuse verified facts; avoid repeated unchanged checks.
No automated tests ran in Task 07. User approved recording remaining security
verification gaps. Agree future scope and isolation before database-backed tests.

## Runtime and repository

C:\Sites\FieldOps, app backend/, WSL /mnt/c/Sites/FieldOps. Herd serves
http://fieldops.test from backend/ with PHP 8.5 isolation. Use herd php and herd
composer from backend/. Plain PHP can select 8.3. Windows/Herd handles web,
Composer and frontend; WSL Redis/future Linux workers; Docker optional.

Recorded versions (not re-audited in Task 07): Laravel 13.39.0, Herd PHP 8.5.10,
Composer 2.11.1, Vue 3.5.44, TypeScript 5.9.3, Inertia Laravel 3.6.0/frontend
3.10.0, Vite+ 1.4.0, Node 22.23.4/npm 10.9.9. Ubuntu PHP 8.5.4, Redis 8.10.1.
No mobile app, domain modules, Redis PHP client/Horizon/Reverb implementation.

Origin https://github.com/WeeRG91/fieldOps.git. Latest inspected commit 70e8693,
chore: initialize FieldOps foundation. Branch feat/staff-access-bootstrap tracks
origin/feat/staff-access-bootstrap. Task 07 changes remain staged/unstaged/untracked;
action has mixed staged/worktree changes, DeleteUser deletion staged. Do not
stage/reset/commit without permission. project-commands.txt is untracked notes.
Earlier no-commit/no-remote records in historical tasks no longer describe current Git.

## PostgreSQL and privileged account

Windows PostgreSQL 18.6, 127.0.0.1:5432, fieldops_dev / fieldops_app. Dedicated
role owns DB/schema migration privileges, without superuser/CREATEDB/CREATEROLE/
replication/BYPASSRLS (Task 04). Effective SQL database/role confirmed immediately
before Task 07 additive audit migration. Five starter migrations, two Task 06
migrations and 2026_11_02_123148_create_audit_events_table applied. No reset.

.env uses pgsql; safe .env.example has blank secrets. Sessions/cache/queues remain
database-backed; mail log. Old SQLite dev file retained. Never record passwords,
hashes, app keys or signed verification links in reports.
Existing user ID 1 now active with Administrator via explicit bootstrap promotion.
Name/password/verification preserved by implementation; manual login/dashboard passed.
No operational role implied. Do not repeat bootstrap as routine setup or weaken
its existing-admin refusal; controlled recovery is not yet implemented.

## Implemented architecture

Task 05 approved design remains in ARCHITECTURE.md: three explicit roles,
multiple roles, no admin universal bypass, no permissions package. Task 06 enum,
user_roles composite key, activation/deactivation metadata and internal role
helpers remain. Helpers are persistence, not caller authorization.

Task 07 User::hasStaffRole checks recognized enum assignments. EnsureStaffAccess
runs optional in web and via staff alias. Dashboard/settings auth/staff/verified;
ineligible authenticated accounts lose current session and receive generic 403.
Logout exempt; eligible unverified accounts retain verification. Password/passkey
callbacks and pending two-factor candidate enforce eligibility. Auth-management
routes require verified email. These paths have incomplete runtime coverage.
Public registration feature/view/links and Register.vue removed; self-deletion
route/controller/component removed. Name-only profile writes; changed email and
privileged role/state payloads rejected server-side. Keep email changes blocked
until last-usable-admin safeguards are implemented.

BootstrapAdministratorCommand (staff:bootstrap-admin) is interactive, displays
actual database/role and requires promotion/final confirmation. New passwords
hidden without fallback, minimum 12/mixed case/numbers/symbols; new account remains
unverified. Existing identity/password/verification preserved. PostgreSQL action
uses transaction-scoped advisory try-lock (71001, 1), refuses ANY Administrator
assignment, rechecks selected account/collisions/deactivation, locks existing row.
Activation/role/audit commit together. Notify after commit; delivery failure does
not undo creation. Lock coordinates cooperating bootstrap calls only, not general
last-admin mutation. No concurrency or fault-injection test performed.

AuditEvent/audit_events: event, actor type, nullable actor/subject FKs with restrict
delete, timezone-aware occurred_at, JSON metadata; no automatic model timestamps.
Immutable datetime/array casts defined; no audit management UI. Successful event
staff.administrator_bootstrapped: console actor/null actor_id, subject user ID,
promotion/role/activation metadata. Console is invocation channel, not separately
authenticated human identity. Refusal/failure uses sanitized app logs outside tx;
rolled-back audit rows do not persist. Database-owner tamper resistance deferred.

## Task 07 files

Created by developer:

- backend/app/Actions/Staff/BootstrapAdministrator.php.
- backend/app/Console/Commands/BootstrapAdministratorCommand.php.
- backend/app/Http/Middleware/EnsureStaffAccess.php.
- backend/app/Models/AuditEvent.php.
- backend/database/migrations/2026_11_02_123148_create_audit_events_table.php.

Modified: User.php, FortifyServiceProvider.php, bootstrap/app.php, config/fortify.php,
routes/web.php, routes/settings.php, Settings/ProfileController.php,
Settings/ProfileUpdateRequest.php, pages/Welcome.vue, auth/Login.vue and
settings/Profile.vue. Deleted auth/Register.vue and components/DeleteUser.vue
under resources/js. Task 07 did not change tests or install dependencies.
Assistant updated AGENTS.md and the five continuity docs only, with authorization.

## Actual checks

Developer-executed, not assistant execution:

- Initial Pint failures corrected; final 62 files PASS.
- Initial PHPStan candidate-type errors corrected by instanceof User guard;
  final analysis 47 files, no errors with --memory-limit=512M.
- Frontend types passed; vp check 66 formatted / 60 linted, no warnings/errors.
- Build passed, 3574 modules, 20.45 seconds. Optional fontaine warning remains.
- Artisan help confirmed command discovery. No separate php -l run.
- Effective DB/role verified, only audit migration pending, additive migrate DONE.
- Bootstrap success reported for existing account; repeat refused before prompts.
- Promoted account login/dashboard, read-only email, absent self-delete,
  GET /register 404 all confirmed manually.
- Success audit count 1 after refusal: console/null actor, subject 1, promoted,
  administrator assigned, activation false to true. Raw Tinker JSON representation
  does not establish array-cast runtime behavior. Split-line query parse error
  corrected with single-line query; no data mutation from error.

No automated tests executed. Historical Task 03 40 tests/138 assertions are not
Task 07 results. phpunit.xml still forces testing/SQLite :memory:/empty DB_URL;
tests/TestCase.php unchanged. Runtime isolation remains unverified.
No dedicated PostgreSQL automated integration suite.

## Deferred checks and security limits

Not verified in Task 07: inactive/roleless or guest/JSON denial, POST registration,
privileged/changed-email input rejection, password recovery, remember-me,
2FA/passkeys, eligible unverified flows, new-account bootstrap, notification
failure, noninteractive/collision/deactivated-account refusal, audit failure
rollback, simultaneous bootstrap. Application security logs not inspected.
Sequential refusal does not prove concurrency; reviewed transaction does not
prove rollback behavior under injected failure. User approved these limitations.
SQLite cannot prove PostgreSQL SQL/locking/concurrency. Agree test scope first.

Full staff management/policies, self-change/last-admin concurrency protection,
session/remember-me/token revocation, invitations, controlled recovery, audit
retention/access and tamper resistance remain future work. No production-ready
security claim. Optional fontaine/React peer, default PHPStan 128 MiB, HMR-editing/
production SSR, nested CI, Linux dependency/Horizon/Reverb/Expo limits remain.

## Next recommended task

Task 08 — Staff administration, last-Administrator safeguards and revocation.
Recover state and agree exact permissions, mutation invariants, concurrent-change
strategy, revocation behavior, audit events/UI and verification scope first.
Keep Task 05 role matrix, no self role/state changes and no global admin bypass.
Invitations remain Task 09; broad coverage/controlled recovery Task 10.
Task 07 unverified security paths remain explicit follow-ups, not passed tests.
