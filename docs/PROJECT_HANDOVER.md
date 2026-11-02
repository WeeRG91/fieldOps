# FieldOps — Project Handover

## Current milestone and task

Identity/access foundation. Tasks 01–08 complete. Task 08 staff administration,
last-Administrator protection and supported revocation implemented; manual results
and explicit gaps approved on 2026-11-02. User authorized six-file closeout.
TASK_LOG.md preserves historical checkpoints and actual evidence.

## Collaboration and testing boundary

Read AGENTS and relevant continuity/source first. Developer writes code/runs
commands; assistant mentors/reviews. No auto application edits, installs, migrations,
staging or commits. This permission covers documentation closeout only.
Batch meaningful checks/reuse verified facts. User chose MANUAL VERIFICATION ONLY
for Task 08; no automated tests/new tests/separate php -l/runtime isolation check.
Agree future scope/isolation and PostgreSQL-only action test strategy before tests.

## Repository and runtime

C:\Sites\FieldOps, app backend/, WSL /mnt/c/Sites/FieldOps. Herd serves
http://fieldops.test from backend/ with PHP 8.5 isolation. Use herd php and herd
composer in backend/. Windows/Herd web/Composer/frontend; WSL Redis/future Linux
workers. Docker optional. No mobile app/operational domain modules.
Recorded runtime, not re-audited Task 08: Laravel 13.39.0/PHP 8.5.10, Composer 2.11.1,
Node 22.23.4/npm 10.9.9, Vue 3.5.44/TypeScript 5.9.3, Inertia Laravel 3.6.0/frontend 3.10.0,
Vite+ 1.4.0; Ubuntu PHP 8.5.4/Redis 8.10.1.
Origin https://github.com/WeeRG91/fieldOps.git. Task 07 merged to main at 9f06938,
old feature branch removed. Current feat/staff-administration; latest commit 9f06938.
Task 08 staged/unstaged/untracked changes; action/new Vue/types include AM mixed
snapshots. project-commands.txt untracked. Do not commit partial staged versions.
Assistant did not stage/commit/push. Earlier Git snapshots are historical.

## PostgreSQL and retained accounts

Windows PostgreSQL 18.6, 127.0.0.1:5432, fieldops_dev/fieldops_app. Dedicated owner
migration role without superuser/CREATEDB/CREATEROLE/replication/BYPASSRLS from
Task 04. SQL target rechecked Task 08 before fixture creation. No reset or Task 08
migration; eight existing migrations (five starter, two identity, one audit) remain
applied from prior tasks. .env pgsql; example blank secrets. Sessions/cache/queues
database-backed, mail log. No secrets/hashes/signed links in reports.
Primary user 1 active/verified/Administrator only, final join query verified.
Fixture user 2 Manual Staff, fieldops.manual@example.test, retained INACTIVE; last
confirmed roles Dispatcher. Final deactivated_by=1, timestamp present, reason
'Task 08 manual verification complete'. History retained; do not delete blindly.
Query builder inspected hidden metadata; do not remove model serialization guards.
Fixture email manually verified only for testing, not public provisioning. Password
entered hidden without visible fallback, never documented.

## Implemented architecture

Fixed StaffRole/multiple assignments/no global admin bypass remain. Task 07 staff
middleware/password/passkey/2FA checks, closed registration/self-delete, name-only
profile, first-admin bootstrap and audit remain. Bootstrap already used/refuses
ANY admin assignment; no recovery yet. Keep profile email changes blocked.

Task 08 UserPolicy allows active verified admin directory/details/manage others,
rejects all self role/state changes. All existing users visible including inactive/
roleless; no creation, delete, identity/password/verification editing.
ManageStaffAccess replaceRoles/activate/deactivate owns top-level PostgreSQL READ
COMMITTED, rejects nesting/other drivers, takes try-advisory lock (71001, 1), locks
actor/subject by ascendingID, reloads policy authority, mutates/revokes/audits,
checks active verified admin remains before commit. Exceptions roll back together.
Key shared with bootstrap; future invitation/recovery writes must cooperate.
Direct SQL/internal helpers bypass safeguards. Last-admin failure branch untested.
Complete nonempty distinct roles; sorted no-op skips mutation/revocation/audit.
Inactive roles retained; reactivation explicitly selects roles/clears metadata,
leaves verification/password unchanged. Deactivation needs reason<=1000, retains
roles, records actor/time/reason. Redundant state transitions rejected by source.

Audit existing schema: staff.roles_replaced/staff.activated/staff.deactivated,
user actor/subject/time, selected role/state before-after/grants/revocations/reason.
Same transaction, no secrets or rollback-row failure-history claim. No audit UI,
retention/tamper resistance. First three mutation rows independently inspected;
subsequent rows not separately inspected/count-reconciled.
Every actual role/state change rotates remember_token/deletes currently stored
database sessions on SAME app connection. Unsupported driver/connection fails.
Role-change refresh forced re-login; deactivation refresh/fresh login denied.
Running requests can finish/concurrent requests can rewrite sessions; no auth
version/epoch/cancellation guarantee. Remember-me cookie itself untested.
No Sanctum tokens or Reverb/WebSocket infrastructure to revoke.

## HTTP and Vue interface

StaffController explicit fields, page size 20, one batched role query/page. Authorized
details include metadata otherwise hidden on User. Three requests authorize bound
User/validate roles or reason/reject specified identity and privileged input.
Thin writes pass validated values only to action.
GET /staff, GET /staff/{staff}, PATCH /staff/{staff}/roles,
POST /staff/{staff}/activate, POST /staff/{staff}/deactivate: auth/staff/verified,
numeric binding. Read policy middleware; writes authorize before password prompt,
in request and fresh in action. RequireStaffPasswordConfirmation uses Fortify
timestamp/auth.password_timeout(10800 default); expired browser writes return via
confirmation to GET details for explicit resubmission, JSON 423 by source.
bootstrap/app.php catches staff-route Inertia ConflictHttpException only:303
redirect/errors.staff display inline, preserve input, saved state unchanged.
Other clients keep normal 409. Manual old debug popup replaced and retested.
Typed staff/Index/Show have pagination, separate active/verified status, current
roles/read-only identity, role/state forms/confirmations/reason/errors/processing.
Self controls hidden. Policy auth.canManageStaff drives AppSidebar only, not backend
security. Wayfinder --with-form --skip-database generated. No UI dependency added.

## Files changed in Task 08

Created:

- backend/app/Policies/UserPolicy.php.
- backend/app/Actions/Staff/ManageStaffAccess.php.
- backend/app/Http/Controllers/Staff/StaffController.php.
- backend/app/Http/Middleware/RequireStaffPasswordConfirmation.php.
- backend/app/Http/Requests/Staff/ReplaceStaffRolesRequest.php.
- backend/app/Http/Requests/Staff/ActivateStaffRequest.php.
- backend/app/Http/Requests/Staff/DeactivateStaffRequest.php.
- backend/resources/js/types/staff.ts.
- backend/resources/js/pages/staff/Index.vue and Show.vue.

Modified backend/routes/web.php, backend/bootstrap/app.php,
backend/app/Http/Middleware/HandleInertiaRequests.php,
backend/resources/js/types/auth.ts, backend/resources/js/components/AppSidebar.vue.
No schema/test/dependency changes. Assistant changed AGENTS and five docs only.

## Actual checks and manual evidence

Developer execution, not new assistant execution:

- Full Pint 69 passed at backend checkpoint; subsequent changed middleware/bootstrap
  targeted formatting passed. Latest bootstrap check 1 file PASS.
- PHPStan initially caught Gate/timestamp errors; corrected. Latest54 files/no
  errors at 512 MiB after final handler. No separate php -l.
- Frontend check:fix/build/types all reported passed, no supplied counts/timings.
- route:list showed five staff routes and expected middleware.
- Primary admin directory/details/sidebar/self-hidden controls passed.
- Activation first stopped for password confirmation, returned without mutation;
  second submission activated Dispatcher fixture.
- Dispatcher/Technician dashboard/login, hidden staff nav and direct-read403 passed
  (Dispatcher directory/details, Technician directory checked).
- Role replacement revoked private session; deactivation retained roles/reason,
  revoked session and rejected correct fresh login.
- First three audit rows correct actor 1/subject 2/timestamps/array metadata; primary
  roles unchanged. Reactivation Administrator+Technician, cleared metadata and
  fixture admin access/self-hidden controls passed.
- Direct action self-role/state denials, empty/duplicate/unknown roles and reverse-
  order no-op with unchanged audit count passed. Not HTTP payload coverage.
- Tinker-held shared lock blocked separate browser write; rollback release allowed
  retry. Repeated check verified inline conflict/preserved input/unchanged saved
  state, lock released. Limited two-connection contention, not exhaustive racing.
- Final fixture inactive/deactivation metadata verified; primary remains usable.

## Approved remaining gaps

No automated tests/new tests/SQLite runtime isolation. phpunit.xml forced testing/
SQLite :memory:/empty DB_URL; TestCase unchanged. Old starter tests/factory assume
old access/email/deletion rules; registration suite skips instead of checking
closure. Do not blindly run. PostgreSQL-only/nonnested action needs explicit future
test strategy; SQLite cannot prove locking/concurrency. No PostgreSQL suite.
Unverified last-admin failure branch, injected audit/write rollback, exhaustive
concurrent mutations, remember-me cookie behavior, in-flight/session races,
HTTP unauthorized mutations/privileged payloads, JSON confirmation/conflicts,
multi-page pagination, inherited 2FA/passkey/recovery/unverified/active-roleless/
inactive-admin cases. Source review not executed evidence. User approved gaps.
No production-ready/immediate cancellation claim. Invitations/controlled recovery/
audit browsing/retention/tamper resistance/email-change safety remain future.
Optional fontaine/React peer, default PHPStan 128 MiB, HMR-edit/production SSR,
root CI/Linux dependency setup limits remain.

## Next recommended task

Task 09 invitation-only staff provisioning/atomic acceptance per Task 05. Agree
normalized fixed email, explicit roles, 48-hour hashed single-use tokens, resend/
revoke invalidation, inviter authority recheck and atomic account/role/audit writes.
Existing accounts separate, no silent reset/reactivation. Coordinate protected
writes with shared lock; agree notifications/testing. Task 10 coverage/console
recovery. Keep Redis PHP client, queues/workers, cross-platform Horizon and Reverb
infrastructure visible; none installed/configured here. Sanctum/Expo/mobile later.
