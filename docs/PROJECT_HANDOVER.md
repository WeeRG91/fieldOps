# FieldOps — Project Handover

## Current milestone and task

Staff identity/authorization foundation, following initial application setup.
Task 06 — Staff Identity Foundation and Role Persistence: complete.
User approved results and testing exceptions on 2026-11-02 and explicitly
requested all report updates. Tasks 01–05 remain complete.
Historical work and actual checks are preserved in TASK_LOG.md.

## Collaboration and scope

Read AGENTS.md and relevant continuity files before work. Developer writes code
and runs commands; assistant explains, guides and reviews. Do not automatically
edit/install/migrate/stage/commit. Current authorization covers documentation only.

The developer requests fewer repeated checks and larger meaningful steps.
Reuse unchanged verified facts, inspect files directly, batch related work,
and retain target verification before database-changing migrations.
The developer explicitly skipped database-backed automated tests for Task 06.
Agree future test scope; never report skipped checks as passed.

## Repository and runtime

Root C:\Sites\FieldOps; application backend/; WSL /mnt/c/Sites/FieldOps.
Herd serves http://fieldops.test from backend/ with PHP 8.5 isolation.
Use herd php and herd composer from backend/. Plain PHP may select 8.3.
Windows/Herd handles web/Composer/frontend; Ubuntu handles Redis and future
Linux workers. Docker optional. No mobile/ or operational domain modules.

Recorded runtime: Laravel 13.39.0, Herd PHP 8.5.10, Composer 2.11.1,
Vue 3.5.44, TypeScript 5.9.3, Inertia Laravel 3.6.0/frontend adapters 3.10.0,
Vite+ 1.4.0, Node 22.23.4/npm 10.9.9. Ubuntu baseline PHP 8.5.4, Redis 8.10.1,
Git 2.53.0; Windows Git 2.49.0, WSL 2.7.12.0. Versions not rechecked in Task 06.

Git main, no commits/remotes; last cached diff empty. Root config/docs and
backend/ remain untracked. No nested repository. .env, SQLite, dependencies,
build output and .idea are ignored. Lockfiles intended for Git.
project-commands.txt is an empty local file at its last content inspection.

## PostgreSQL development

Server 18.6 at 127.0.0.1:5432, database fieldops_dev, role fieldops_app.
Dedicated role owns the database with schema USAGE/CREATE but no superuser,
CREATEDB, CREATEROLE, replication or BYPASSRLS privileges (Task 04 verification).
Laravel SQL target was rechecked before Task 06 migrations.
No passwords, password hashes or signed links in reports.

backend/.env selects pgsql; .env.example documents local PostgreSQL with empty
password/key. Sessions/cache/queues database-backed; mail driver log.
Old SQLite development file retained. Five starter migrations plus two Task 06
migrations were applied. No destructive reset or data migration.

## Approved design versus current implementation

Task 05 full policy/matrix is in ARCHITECTURE.md. Three roles: Administrator,
Dispatcher/service manager, Technician; multiple explicit roles. No permission
package, dynamic permission engine, or Administrator bypass. Policies/Gates,
active/verified staff checks, invitation-only provisioning, transactional audit,
last-admin protection and independent work review remain the approved direction.

Implemented in Task 06:

- StaffRole string enum: administrator, dispatcher, technician.
- user_roles: user_id foreign key with restricted user deletion, constrained role,
  composite primary key (user_id, role); no surrogate ID or timestamps.
- User::hasRole(StaffRole), User::assignRole(StaffRole), query-builder persistence
  using the model connection; saved-user guard; no authorization inside these helpers.
- users.is_active default false, nullable deactivated_at/deactivated_by/reason.
  Deactivation actor foreign key restricts user deletion.
- User inactive default, boolean/date casts, isActive(), unchanged fillable fields,
  hidden deactivation metadata in default serialization.

NOT implemented: active-state login/request enforcement, closed registration,
self-deletion removal, bootstrap/recovery, invitations, revocation, last-admin
safeguards, resource policies, sensitive audit storage/actions. Existing staff
accounts are inactive and roleless but can still use starter login/dashboard.
This transitional behavior is intentional for Task 06, not production security.

Audit storage was explicitly deferred outside Task 06. Its placement must be
agreed before completing audit-dependent Task 07 bootstrap/privileged actions.
Redis/queues foundation remains deferred; no related packages/config added.

## Task 06 files

Developer created:

- backend/app/Enums/StaffRole.php.
- backend/database/migrations/2026_11_02_090519_create_user_roles_table.php.
- backend/database/migrations/2026_11_02_091659_add_staff_activation_fields_to_users_table.php.

Developer modified backend/app/Models/User.php and backend/phpunit.xml.
Four PHPUnit entries force APP_ENV/DB_CONNECTION/DB_DATABASE/DB_URL.
tests/TestCase.php unchanged; no shared test guard or test files added.
Targeted Pint corrected enum line endings. Original migrations preserved.
Assistant updated only the six continuity files with explicit authorization.
No assistant application edits, tests, database writes, migrations or staging.

## Actual Task 06 verification

Developer-executed outputs and browser confirmation:

- PHP syntax: User, enum, two migrations passed.
- SQL current target fieldops_dev / fieldops_app confirmed.
- Two additive migrations: successful.
- Existing user count 1; isActive() false.
- Temporary transaction assigned Dispatcher/Technician: true/true membership;
  Administrator false. Finally block rolled back both assignments.
- Existing account login/dashboard still works.
- Pint initially failed one enum line-ending rule; targeted fix, then 57 files passed.
- PHPStan at 512 MiB: 42 files, no errors.

No automated tests run in Task 06. Database-backed checks/isolation criteria
were explicitly deferred with user approval. No new database-free unit tests
were run either. Duplicate/invalid assignment failure cases and FK deletion
failures were not directly exercised; schema constraints were reviewed/applied.
No frontend checks repeated because frontend/build code did not change.

Historical only: Task 03 had 40 tests/138 assertions and frontend checks passed;
Task 04 manual PostgreSQL authentication/persistence passed with automated suite
skipped. Task 05 was design-only with no tests. Do not relabel these as Task 06 runs.

## Testing and remaining limitations

phpunit.xml configures SQLite :memory: with forced environment settings and
empty DB_URL; runtime isolation remains unverified. Obtain agreed scope and
verify effective connection/cache/environment before future database-backed tests.
No dedicated PostgreSQL automated suite. SQLite does not prove PostgreSQL SQL,
types, constraints, locking or concurrency semantics.

Fresh hasRole calls issue queries; bulk list usage may need later optimization.
Internal assignment methods must only be exposed through future authorized actions.
Activation consistency/transitions and last-admin concurrency are not implemented.
No permanent roles or activation granted to the existing development account.

Existing limitations: optional fontaine warning; extraneous React peer; default
PHPStan 128 MiB failure (use documented 512 MiB override); four-space backend
formatting; unverified HMR editing/production SSR; nested CI workflow not configured
at root; PHP Redis/Horizon cross-platform/Reverb/Expo and production setup deferred.

## Next recommended task

Task 07 — Controlled staff access and first-admin bootstrap.
Agree exact scope, audit-foundation placement, existing-account treatment,
email-verification flow, acceptance criteria and test authorization first.
Close registration/self-deletion and enforce active/verified/role-aware staff
access without locking out initial verification. No default credentials,
automatic first-user promotion or universal Administrator bypass.
Tasks 08–10 remain planned for staff administration/revocation, invitations,
authorization coverage and controlled recovery. Do not start them implicitly.
