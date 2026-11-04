# FieldOps — Task Log

## Recording rules

- Record verified facts and actual results.
- Distinguish approved plans from implemented capabilities.
- Never report an unexecuted check or test as passed.
- Record limitations and deferred verification.
- Mark a task complete only after user review and approval.
- Update completion records after approval.

## Task 01 — Project Discovery, Architecture and Continuity

### Date

2026-10-30

### Status

Completed. User approved the Task 01 outcome on 2026-10-30.

All six continuity documents have been created and reviewed.
The cross-document consistency review is complete.
Completion records have been finalized.

### Objective

Establish the initial business, architectural, engineering, and continuity
foundation without installing dependencies or initializing Laravel.

### Initial workspace inspection

The workspace at C:\Sites\FieldOps was empty, including hidden files.

No application code, dependency manifests, Docker configuration, tests,
or continuity documentation existed.

Git status reported that the directory was not a Git repository.
No applicable AGENTS.md was found in the workspace or its parent directories.

### Approved decisions

- Serve one maintenance company with multiple customers and sites.
- Use administrator, dispatcher/service manager, and technician staff roles.
- Defer customer portal access.
- Distinguish service requests from work orders.
- Require dispatcher review before work-order closure.
- Require a site on a work order; equipment is optional.
- Allow at most one work order per service request in the MVP.
- Allow at most one assigned technician and one planned visit window
  per work order at a time.
- Require connectivity for mobile saves in the MVP.
- Use a modular monolith with one Laravel backend.
- Keep the Inertia web application inside Laravel.
- Expose a versioned REST API for mobile.
- Share application actions and business rules across entry points.
- Use PostgreSQL for authoritative business data.
- Use Redis, Horizon, and Reverb for the relevant asynchronous and
  real-time features.
- Introduce scheduled work only when required by a feature.
- Use Ubuntu through WSL for development; Docker is optional.
- Keep backend, mobile, and documentation in one Git repository.
- Follow the developer-led mentoring workflow recorded in AGENTS.md.
- Prioritize meaningful automated tests without a dedicated PostgreSQL
  integration-test suite at this stage.

Technology versions remain targets pending compatibility verification.

### Documentation progress

Created by the developer and reviewed by the assistant:

- AGENTS.md.
- docs/PROJECT_OVERVIEW.md.
- docs/ARCHITECTURE.md.
- docs/ROADMAP.md.
- docs/TASK_LOG.md.
- docs/PROJECT_HANDOVER.md.

The architecture directory-tree formatting correction has been verified.
Cross-document review found no conflicts in business scope or architecture.

The user authorized the assistant to finalize the five documentation files.
Completion records now reflect the approved Task 01 outcome.

### Actual checks and results

- Inspected the initial workspace, including hidden entries:
  zero entries were found.
- Checked Git status: the workspace was not a Git repository.
- Checked for applicable parent and workspace AGENTS.md files:
  none existed during the initial inspection.
- Read the developer-created AGENTS.md:
  content matched the agreed collaboration rules.
- Read PROJECT_OVERVIEW.md:
  content matched the approved business scenario and MVP.
- Read ARCHITECTURE.md:
  content matched the approved architecture and WSL direction.
- Read ROADMAP.md:
  content matched the planned delivery sequence and MVP boundaries.

- Read TASK_LOG.md and PROJECT_HANDOVER.md:
  both accurately recorded the work at their respective review points.
- Verified that all six required continuity files exist.
- Verified the architecture directory-tree code fences.
- Reviewed documentation consistency:
  no business-scope or architectural conflicts were found.
  Outdated progress statements were identified for final updates.
- Reviewed Task 01 acceptance criteria:
  planning and documentation requirements were satisfied;
  final completion-record updates and verification remained.
- Completed. User approved the Task 01 outcome on 2026-10-30.

Application tests: not run.
No application or test suite exists yet.

### Limitations and deferred verification

- Runtime and package compatibility have not been verified.
- Installed WSL tools, versions, and PHP extensions have not been inspected.
- PostgreSQL location, version, and connectivity from WSL are unverified.
- Final repository location and editor access remain undecided.
- Laravel and Expo have not been initialized.
- Git has not been initialized.
- No automated or manual application workflows can be tested yet.

SQLite tests will not prove PostgreSQL-specific behavior.
Queue and broadcast fakes will not prove infrastructure integration.

### Next steps

Begin Task 02 in a separate conversation after agreeing its scope.

### Recommended next task after approval

Task 02: inspect the WSL development environment, verify compatibility,
and confirm repository location and PostgreSQL connectivity.

Agree the exact scope before making environment changes or initializing
applications.
## Final documentation verification — 2026-10-30

All five finalized docs/ files were saved and read back successfully.
The workspace inventory contains exactly the six required continuity files.
A stale-status search found no outstanding progress statements; the task log
retains a historical description of work remaining at an earlier review.
No application tests were run; no application or test suite exists.

## Task 02 — Development Environment and Repository Setup

### Dates and status

2026-10-30 to 2026-10-31. Completed.
The user reviewed and approved Task 02 on 2026-10-31.
The user explicitly authorized the assistant to update continuity files.
No applications were scaffolded and no project dependencies were installed.

### Decisions and changes

- Windows/Herd: web application, Composer, and frontend tooling.
- Ubuntu/WSL 2: Redis, Horizon, and Linux worker processes. No Docker required.
- Active repository remains C:\Sites\FieldOps, shared as /mnt/c/Sites/FieldOps.
- This explicitly revises Task 01's all-Ubuntu runtime recommendation.
- Temporary WSL documentation copy was compared using diff, .gitignore was
  copied back and verified, and the duplicate WSL repository was removed.
- User initialized Git on main in the Windows repository; no commits/remotes.
- User created .gitignore, .gitattributes, and .editorconfig.
- Ignore rules exclude secrets, dependencies, generated files, and IDE state;
  safe environment examples, log placeholders, and lockfiles remain eligible.
- Repository-local core.autocrlf=false; attributes specify LF text, CRLF
  bat/cmd scripts, and binary handling. Global Git settings were not changed.
- EditorConfig specifies UTF-8, final newline, spaces (PHP 4; frontend 2),
  LF by default, CRLF command scripts, and retained Markdown trailing spaces.
- Accidental IDE staging was cleared with git update-index --force-remove;
  working files were preserved and the staging area was verified empty.
- Proposed workflow: reviewed main, short-lived focused branches after the
  initial commit, explicit staging, focused messages, no automatic commits.
- Author identity is configured globally with a GitHub noreply address.
- User configured Composer public keys correctly and updated shared Herd
  Composer from 2.8.12 to 2.11.1 using self-update --stable.

### Verified versions

Windows: project-aware Herd PHP 8.5.10, Composer 2.11.1, Node 22.16.0,
npm 11.19.1, Git 2.49.0.windows.1, installed PostgreSQL binary 18.6.
Plain Windows PHP previously resolved to Herd PHP 8.3.22.

Ubuntu: 26.04 LTS, PHP 8.5.4, Git 2.53.0, Redis server/CLI 8.10.1.
WSL 2 mode; WSL software 2.7.12.0.
Ubuntu Composer, Node, and PostgreSQL CLI were not found in inspected PATH.
Linux npm resolved to Windows npm and failed; it is not the chosen frontend
execution environment.

### Actual checks and results

Evidence includes user-supplied command output and earlier read-only tool
inspection. Do not treat metadata checks as application tests.

- Read all six continuity files and inspected repository structure/Git.
- Ran Windows and Ubuntu version/module checks and Windows PHP --ini.
- Herd PHP 8.5: PostgreSQL and SQLite extensions present; Redis, pcntl,
  and posix absent. Ubuntu PHP: PostgreSQL, pcntl, posix present;
  SQLite and Redis extensions absent.
- Composer diagnose initially reported missing keys and security advisories.
  After correct key setup and self-update, diagnose reported OK for keys,
  version, vulnerability check, platform, Git, connectivity, and free disk.
- PostgreSQL pg_isready: localhost:5432 accepting connections.
- Ubuntu Redis PING: PONG. Windows TCP localhost:6379: True.
- Earlier Ubuntu-to-Windows PostgreSQL TCP check succeeded under mirrored WSL.
  No authenticated query was performed.
- Docker CLI not found in Windows PATH; no Desktop process reported.
  Docker installation/Compose/engine availability not conclusively verified.
- git check-ignore confirmed environment/dependency exclusions, environment
  example and log-placeholder exceptions, and lockfiles not ignored.
- git check-attr confirmed LF text, CRLF cmd, and PNG text conversion disabled.
- git status: main with no commits; git diff --cached --name-only: empty.
- Official PHP package manifests accept the target PHP/Laravel combination.
- npm view metadata: Node 22.16.0 satisfies npm 11.19.1, Vite 8.3.5,
  laravel-vite-plugin 3.2.0, plugin-vue 6.0.10, Expo 58.0.21,
  React Native 0.88.1, and create-expo-app 5.0.0 declared Node ranges.
  Versions were queried, not installed. See ARCHITECTURE.md for details.

Application tests/builds: not run; no application or test suite exists.
No dedicated PostgreSQL integration-test infrastructure was introduced.

### Files

Created by user: .gitignore, .gitattributes, .editorconfig; local .git/config.
Assistant updated with authorization: AGENTS.md environment guidance,
docs/ARCHITECTURE.md, docs/ROADMAP.md, docs/TASK_LOG.md,
and docs/PROJECT_HANDOVER.md. Task 01 history is retained above.
PROJECT_OVERVIEW.md business scope is unchanged.
project-commands.txt exists, is empty at inspection, and remains untracked.
.idea/ exists and is ignored. No files were staged or committed by assistant.

### Limitations and deferred work

- Horizon's Windows Composer pcntl/posix requirements need a specific verified
  installation strategy before adding Horizon; no blanket platform bypass.
- Select a PHP Redis client; validate runtime dependencies on both systems.
- PostgreSQL credentials, database creation, permissions, and PHP SQL checks.
- Redis application authentication and PHP client operations, worker execution,
  Reverb integration, Expo device connectivity, and exact scaffold dependencies.
- Update Herd site to backend/ after initialization and verify browser PHP.
- No remote or first commit; neither is required without user authorization.
- SQLite tests do not establish PostgreSQL-specific types/locking/SQL behavior.
  Queue/broadcast fakes do not establish actual infrastructure integration.
- User review and completion approval received on 2026-10-31.

### Recommended next task

Task 03: agree the scope of Laravel 13 + Vue/TypeScript/Inertia initialization
in backend/, review exact released scaffold requirements and generated files,
then let the user execute installation and initial checks step by step.
Do not initialize Expo or add Horizon simply as part of scaffolding.
## Task 03 — Laravel + Vue + TypeScript + Inertia Initialization

### Dates and status

2026-10-31 to 2026-11-01. Results reviewed and approved by user on 2026-11-01.
The user then explicitly authorized updating all continuity documents.
Completed. All six continuity documents were read back and checked during closeout.

### Work performed by the developer

- Ran installer 5.23.0 via Herd PHP 8.5 against laravel.phar:
  new backend --vue --phpunit --database=sqlite.
- Declined installer update and final npm install/build prompt. The installer
  reported backend ready; later inspection already found vendor/, node_modules/,
  and lockfiles. Exact frontend installation origin was not established.
- Reviewed generated Laravel directories, Inertia request flow, package scripts,
  Vite+ configuration, and PHPUnit SQLite :memory: isolation.
- Corrected malformed APP_URL=http://localhost:8000:8000 to http://fieldops.test.
- Linked backend with herd link fieldops --isolate=8.5. Herd commands now use
  PHP 8.5.10 in backend/. Before linking they incorrectly selected global 8.3.
- Verified app key presence without exposing its value, SQLite file existence,
  and five migrations marked Ran. Sessions/cache/queues use database drivers.
- Started npm run dev; welcome rendered and Wayfinder generated TypeScript.
- Executed the checks below and verified built-asset browser rendering with
  Vite stopped and public/hot absent.
- Installed Node 22.23.4 through existing nvm and switched to its npm 10.9.9.
  Opened a fresh terminal after the initial Node command lookup failed.

### Versions and actual checks

Laravel 13.39.0; Inertia Laravel 3.6.0; Fortify 1.41.0; Vue 3.5.44;
Inertia frontend adapters 3.10.0; TypeScript 5.9.3; Vite+ 1.4.0.
Herd PHP 8.5.10 and Composer 2.11.1. Final Node/npm: 22.23.4 / 10.9.9.

Results below are user-executed command output and reported browser checks:

| Check | Actual result |
|---|---|
| herd php artisan --version | Laravel Framework 13.39.0 |
| herd composer check-platform-reqs | Passed under PHP 8.5.10, exit 0 |
| herd php artisan migrate:status | All five migrations Ran |
| herd php artisan test | 40 passed, 138 assertions, 42.37s |
| npm run build after Node update | Passed, 2.34s |
| npm run types:check after update | Passed, vue-tsc --noEmit |
| npm run check after update | 68 formatted, 62 linted, no warnings/errors |
| herd composer run lint:check | 54 PHP files passed |
| herd composer run types:check | Failed: PHPStan worker exceeded 128 MiB |
| Direct PHPStan --memory-limit=512M | 39 files, no errors |
| Browser with dev server | Welcome rendered, no errors reported |
| Browser with Vite stopped/hot absent | Welcome/login/register styled, no console errors reported |

An earlier build/types/lint pass ran on unsupported Node 22.16.0. npm prune
--dry-run exposed Vite+ engines (^22.18.0, ^24.11.0, or >=26). The supported
Node update was followed by the frontend checks above. No actual prune ran.

### Files and architecture

Generated backend/ includes PHP/frontend manifests and lockfiles, app/,
bootstrap/, config/, database/, resources/, routes/, tests/, and supporting
config. Developer changed backend/.env APP_URL; Herd configuration changed
outside the repository. Root documents/Git configuration were preserved.
No nested Git repository was created, no commits or staging performed.

Assistant updated with authorization: AGENTS.md and all five docs/ files.
Task 01 and Task 02 log entries are preserved as historical records.
backend/ retains generated four-space EditorConfig/Vite+ formatting.
SQLite is temporary development storage; PostgreSQL remains intended.
No domain modules, dedicated PostgreSQL suite, Horizon, Reverb, or Expo added.

### Limitations and deferred checks

- Optional fontaine warning; no package added or optimization disabled.
- Extraneous React 19.3.1 is an optional passkeys peer; dry run proposed no cleanup.
- Default PHPStan script remains memory-limited; command-local 512 MiB passed.
- Manual authentication submissions, HMR edits, and production SSR not verified.
- PostgreSQL authentication/permissions, PHP Redis, workers/realtime/mobile deferred.
- SQLite cannot verify PostgreSQL-specific SQL/types/constraints/locking semantics.
- Generated nested .github workflow is not active verified repository CI.
- No first commit or remote. No production deployment checks.

### Next recommended task

Task 04 proposal: configure PostgreSQL development access and manually verify
starter authentication, with scope agreed first. Keep lightweight SQLite tests;
do not introduce a dedicated PostgreSQL integration-test suite.

### Documentation closeout verification — 2026-11-01

All six continuity documents were saved and read back; Task 03 status and
current runtime agree. Historical Task 01/02 log entries remain intact.
Git status still reports main with no commits; staging is empty.
Only continuity documentation was modified by the assistant in this step.
No application checks were rerun during documentation finalization; results
above come from the developer-executed checks reported in this conversation.

## Task 04 — PostgreSQL Development Setup and Authentication Verification

### Status and decisions

Completed; user approved results and the testing exception on 2026-11-01.
The user explicitly authorized continuity-document updates afterward.
PostgreSQL is now the active development database. Earlier SQLite development
statements in task-specific sections are historical checkpoints.
No domain features or Redis/Horizon/Reverb/Expo configuration was introduced.

### PostgreSQL development configuration

- Running server verified by SQL: PostgreSQL 18.6 on Windows.
- Herd PHP 8.5.10: pdo_pgsql enabled, libpq client library 16.15.
- Host 127.0.0.1, port 5432, database fieldops_dev, role fieldops_app.
- Role has LOGIN, owns fieldops_dev, and has USAGE/CREATE on public.
- NOSUPERUSER, NOCREATEDB, NOCREATEROLE, NOREPLICATION, NOBYPASSRLS verified.
- Administrator postgres was used for provisioning only, not Laravel runtime.
- Role password was set using psql's private password prompt; never recorded.
- Database created with UTF8 encoding from template0.

Owning the development database permits migrations. Production may separate
migration and runtime privileges. No claim is made that this role is denied
all access to other databases through server-wide default privileges.

### Changes and commands performed by developer

- Verified server readiness with pg_isready and pdo_pgsql with herd php --ri.
- Authenticated using psql as postgres, then independently as fieldops_app.
- Queried role/database existence before CREATE ROLE and CREATE DATABASE.
- Used psql schema-privilege checks to confirm migration capability.
- Changed backend/.env to pgsql using dedicated role/private password.
- Laravel Tinker confirmed effective driver pgsql, database fieldops_dev,
  role fieldops_app, and initially empty public schema before migrations.
- Ran artisan migrate (not fresh/refresh) and migrate:status.
- Updated backend/.env.example to the local PostgreSQL variables and
  http://fieldops.test, with blank DB_PASSWORD and APP_KEY.
- config/database.php and phpunit.xml were unchanged. SQLite file retained;
  no SQLite user data was copied. Sessions/cache/queues still use database.
- Final inspection found no cached bootstrap/cache/config.php.

### Migration and manual authentication results

All five starter-kit migrations ran in batch 1. Ten tables verified:
cache, cache_locks, failed_jobs, job_batches, jobs, migrations, passkeys,
password_reset_tokens, sessions, users. No domain tables added.

User-executed checks and user-reported browser outcomes:

- Initial users count was zero.
- Guest dashboard access redirected to login.
- Registration rejected mismatched password confirmation.
- Successful registration reached the email-verification screen.
- Laravel SQL confirmed fieldops_dev/fieldops_app, one persisted user,
  and existence of the newly registered account.
- Mail driver log verified; signed link from local mail log completed
  verification and opened the dashboard.
- Logout returned to public page and blocked protected access.
- Re-login succeeded; incorrect credentials were rejected and dashboard
  remained inaccessible while unauthenticated.

No passwords, password hashes, or signed verification links are documented.
Password reset, two-factor authentication, and passkeys were not manually tested.

### Automated checks and approved exception

- PHP Pint formatting: 54 files passed (same check executed twice).
- PHPStan with --memory-limit=512M: 39 files, no errors.
- Automated Laravel suite: NOT RUN at user's explicit request to skip tests.
  Original Task 04 suite-execution criterion was waived with user approval.
- Task 03's 40 tests/138 assertions remain historical, not a Task 04 result.
- phpunit.xml inspected: APP_ENV=testing, DB_CONNECTION=sqlite,
  DB_DATABASE=:memory:, DB_URL empty. No .env.testing existed at initial review.
- Runtime test isolation NOT verified in Task 04. Before future database-backed
  tests, verify effective config, external environment overrides, and caches.
- No dedicated PostgreSQL automated integration suite created.
- No frontend rebuild/type/lint rerun: no frontend/build changes warranted it.

SQLite cannot establish PostgreSQL-specific SQL, type, constraint, locking,
or concurrency behavior. Manual PostgreSQL checks verify this workflow only.
The test skip applies to Task 04; future test scope must follow user direction
and must not silently convert this exception into a permanent testing policy.

### Files and outstanding work

Developer modified backend/.env (ignored/private) and backend/.env.example.
Assistant updated continuity files only, with explicit authorization.
No Git staging or commits. No config/database.php or test configuration edits.

Existing fontaine warning, extraneous optional React peer observation,
PHPStan default-memory limit, HMR editing/production SSR, nested CI placement,
PHP Redis integration, cross-platform Horizon install, Reverb, Expo, and
production privilege design remain deferred.

Next Task 05 proposal: identity/authorization design for administrator,
dispatcher/service manager, and technician; agree registration/account
provisioning and test scope before implementing roles or policies.
Public starter registration is not yet a production account-provisioning policy.

### Task 04 documentation closeout — 2026-11-01

All six continuity documents were updated and read back after user approval.
Earlier task log entries remain unchanged. Current summaries agree that
PostgreSQL development/authentication are verified, automated tests were
skipped, and runtime test isolation was not verified. Staging remains empty;
no commits. No application tests were executed during documentation editing.
## Task 05 — Staff Roles, Authorization and Account-Provisioning Design

### Status

Design decisions and implementation sequence approved during this conversation.
Continuity closeout recorded on 2026-11-02 with explicit user authorization.
Completed. Final Task 05 completion approved by the user on 2026-11-02.
This task is design-only; no authorization features were implemented.

### Recovery and actual checks

Read all six continuity documents and inspected Git and authentication code:
User model, Fortify registration/actions/provider/configuration, auth config,
web/settings routes, middleware/bootstrap, validation rules, and user migration.
Reviewed official Laravel 13 authorization guidance and the Spatie package
option; no dependency installation or application execution was performed.

Current implementation still has public registration and staff self-deletion.
No role, activation enforcement, invitation, or sensitive audit system exists.
Git inspection: main, no commits, empty staging area. No tests were run.
Task 03/04 historical checks above are not new Task 05 results.

### Approved authorization model

- Administrator, Dispatcher (including service manager), Technician.
  Separate Manager role deferred; customer portal requires separate design.
- Multiple explicit roles per account, fixed StaffRole enum, user_roles table.
  Unique user/role combination, foreign key, constrained role values.
- No dynamic permission tables/package, inheritance, or per-user overrides.
- No automatic Administrator operational permissions or global bypass.
- Middleware: authenticated, verified, active staff. Policies: resource access.
  Gates: broader capabilities. Shared actions enforce business invariants.
- Scope lists/search/exports and related data; enforce server-side authorization.
- Technician access follows current assignment; reassignment removes access.
- Dispatcher + Technician may self-assign via audited scheduling only.
- Anyone contributing technician execution cannot approve/close that work;
  reassignment cannot bypass independent review.
- Full permission matrix is recorded in ARCHITECTURE.md Task 05 section.

### Approved provisioning and security model

- Disable public registration routes/links and staff self-deletion in future
  implementation; use administrator invitations and deactivation.
- Controlled first-admin console bootstrap, no default secrets/first-user
  promotion, refusal when an Administrator assignment already exists.
  Existing test-account treatment requires explicit confirmation.
- Bootstrap account must verify email before normal staff administration.
- Invitations: fixed normalized email, explicit server-held roles, 48-hour
  expiry, hashed single-use tokens, one pending invitation per email.
  Resend invalidates old token; revoke/expiry prevent acceptance.
- Acceptance verifies the invited email, sets password, creates/activates
  account with roles and consumes token atomically. Recheck inviter authority.
  Existing accounts are never silently reset/reactivated/granted roles.
- Administrator invitations are explicit privileged actions with recent
  password confirmation and audit. Deliver notifications after commit.
- Only active verified Administrators manage others' roles/state; normal
  self-role/self-activation changes prohibited. Review retained roles on reactivation.
- Preserve last usable active/verified Administrator, including email-change
  effects and concurrent operations. Initial bootstrap is a controlled exception.
- Deactivation rejects authentication paths and subsequent protected actions;
  invalidate sessions/remember-me credentials and future API tokens.
- Password reset never reactivates accounts or changes roles.
- Reject privileged input on ordinary profile/registration/acceptance endpoints.
- Sensitive changes and required successful audit events share a transaction.
  Record actor/target/action/time/reason/selected before-after, never secrets.
- Limited security logging for denials; no application audit editing/deletion.
  Database-owner tamper resistance and retention remain future concerns.
- Console recovery is separate from bootstrap; no hidden web recovery bypass.
- Single service company; no multi-tenancy introduced.

### Approved future implementation sequence

- Task 06: role/account-state/audit foundation, approved checks and test isolation.
- Task 07: first-admin bootstrap, controlled staff access, disable public registration
  and staff self-deletion.
- Task 08: staff administration, last-admin concurrency, access revocation, audit/UI.
- Task 09: invitations and atomic acceptance with password setup/email verification.
- Task 10: authorization coverage and controlled console recovery.

Scope and acceptance criteria are in ROADMAP.md; each task still requires its
own start approval. Domain policies arrive with their modules. Redis/queue
infrastructure deliberately deferred; initial invitations can deliver synchronously
following commit. No application implementation was authorized by design approval.

### Files and limitations

Updated with explicit authorization during Task 05:

- docs/ARCHITECTURE.md: appended complete approved design and permission matrix.
- docs/ROADMAP.md: appended Tasks 06–10 with scope/acceptance criteria.
- docs/TASK_LOG.md: appended this record; previous task history preserved.
- docs/PROJECT_HANDOVER.md: refreshed current task and next-step guidance.

No other files changed by the assistant for Task 05. No packages, migrations,
account changes, commits, or staging. No tests or application quality checks run.
SQLite :memory: configuration exists; runtime isolation remains unverified after
Task 04's approved test skip. Future persistence-dependent tests require scope
approval and isolation verification. No dedicated PostgreSQL integration suite.

Remaining design details for implementation: exact account-state defaults and
existing-user transition, invitation schema/normalization, transaction/locking
mechanisms for bootstrap/acceptance/last-admin, recovery procedure, audit
retention, and rollout of closed registration. These must honor approved rules.
Current public registration/self-deletion are still starter behavior, not production
staff policy. Existing infrastructure and tool limitations remain unchanged.

### Next step

Final documentation closeout reviewed; user approved Task 05 completion on 2026-11-02.
Recommended next task afterward: Task 06 — Staff identity foundation.

## Task 06 — Staff identity foundation and role persistence

### Status and scope

Completed. User approved implementation results and testing exceptions on
2026-11-02, then explicitly authorized all report-file updates.
Role/activation persistence is implemented; authorization enforcement is not.
Audit storage from the original Task 05 roadmap was explicitly deferred
outside Task 06 to follow the narrower implementation scope.

### Files implemented by the developer

- backend/app/Enums/StaffRole.php.
- backend/database/migrations/2026_11_02_090519_create_user_roles_table.php.
- backend/database/migrations/2026_11_02_091659_add_staff_activation_fields_to_users_table.php.
- backend/app/Models/User.php.
- backend/phpunit.xml: force=true for APP_ENV, DB_CONNECTION, DB_DATABASE, DB_URL.

No permission package or separate roles table was added.
Original migrations and tests/TestCase.php were not modified during this task.

### Role design now implemented

StaffRole is string-backed: Administrator=administrator,
Dispatcher=dispatcher, Technician=technician.
The enum namespace is App\Enums; its initial namespace capitalization was corrected.

user_roles has user_id referencing users with restricted deletion and a role
column constrained to the three allowed values. Composite primary key
(user_id, role) prevents duplicate assignments. No surrogate ID or timestamps.
Migration role values are literal so future enum changes do not rewrite history.

User::hasRole(StaffRole) performs a fresh existence query; an unsaved user returns
false. User::assignRole(StaffRole) requires a persisted user or throws LogicException.
Both use the User connection and query builder, avoiding Eloquent composite-key
limitations. Duplicate inserts are not silently ignored.

These internal persistence methods do not authorize callers, enforce active
state, protect the last Administrator, or provide an Administrator bypass.
Repeated per-user lookups can require future bulk loading for list interfaces.

### Activation foundation now implemented

users has is_active default false, nullable deactivated_at timestamp with timezone,
nullable deactivated_by user foreign key with restricted deletion, and nullable
text deactivation_reason.

User supplies is_active=false before persistence, casts boolean/date values,
and exposes isActive(). Activation/role fields remain outside mass assignment.
Deactivation metadata is hidden from default serialization because Inertia
currently shares the authenticated User model.

Existing users became inactive without receiving roles. Login/dashboard access
remains unchanged until later active-staff enforcement. Current public registration
has no role-assignment path and inserts accounts with the inactive default.
No deactivation service, session/token revocation, invitations, bootstrap,
policy system, or audit storage was implemented.

### Actual verification

All execution below was performed by the developer and reported in this chat.
The assistant reviewed saved source files read-only.

- Syntax checks passed for User, StaffRole, and both new migrations.
- Verified development SQL target fieldops_dev / fieldops_app before migration.
- Both additive migrations ran successfully against that target.
- Existing user count was 1 after migration; isActive() returned false.
- In an explicit transaction, assigned Dispatcher and Technician to the existing
  user: both membership checks returned true, Administrator returned false.
  The finally block rolled the transaction back; assignments were not persisted.
- Developer confirmed existing login/dashboard access still works.
- Initial Pint check: 57 files, one StaffRole line-ending issue.
- Targeted Pint fixed that issue; final Pint: 57 files passed.
- PHPStan with --memory-limit=512M: 42 files, no errors.
- Accidental enum staging was cleared by developer; later staging checks empty.

No automated tests were executed during Task 06, including no new database-free
unit tests. No frontend checks repeated because frontend behavior was unchanged.
No destructive migration/reset commands were executed.

### Approved testing exceptions and limitations

The developer explicitly requested no database-backed automated tests to focus
on development; this superseded the original Task 06 test-execution criteria.
Focused persistence tests and runtime-isolation verification were deferred and
that exception was approved with the outcome.

phpunit.xml still selects SQLite :memory: and an empty DB_URL; four entries now
force environment precedence. No shared createApplication guard was added.
These settings are not proof of runtime isolation. Confirm future test scope
and verify effective isolation before any persistence-dependent automated run.
No dedicated PostgreSQL automated integration-test suite.

Duplicate/invalid assignment rejection and foreign-key deletion failures were
not directly exercised. Their migration constraints were reviewed and applied;
do not claim those failure cases were tested. No permanent roles/activation
were granted to the existing development account.

### Deferred work and next task

Task 07: agree first-admin bootstrap, closed registration/staff self-deletion,
and active/verified/role-aware access enforcement. Audit storage is deferred
but is required before audit-dependent bootstrap/privileged workflows are
considered complete; agree its placement explicitly at Task 07 start.

Last-Administrator concurrency, provisioning/invitations, revocation, and
resource authorization remain future work under Task 05's approved design.
Existing fontaine/React/PHPStan-memory/CI/infrastructure limitations remain.
Do not interpret this completion as permission to implement subsequent tasks.

### Task 06 documentation closeout — 2026-11-02

All six continuity files saved and read back after explicit user authorization.
Current summaries distinguish implemented persistence from deferred enforcement,
audit storage and automated verification. Earlier task log entries preserved.
Git staging remains empty; no commits. No application checks were rerun during
documentation editing; results above are developer-executed evidence.

## Task 07 — Controlled staff access, Administrator bootstrap and audit

### Status and approval

Complete on 2026-11-02. User approved results and documented verification gaps,
then authorized continuity updates. Developer wrote application code and executed
commands. Assistant reviewed read-only and edited documentation with permission.
Earlier task records are historical checkpoints, not current security/Git state.

### Decisions and implementation

Active staff requires activation plus recognized role; staff pages require
verification. EnsureStaffAccess optional web mode and explicit auth/staff/verified
routes enforce baseline admission without admin bypass. Password/passkey callbacks
and pending two-factor challenge recheck eligibility. Logout/verification/recovery
paths retained appropriately by implementation. Public registration and self-delete
routes/UI removed. Profile writes name only; changed email/privileged input rejected.

Interactive first-admin command displays target, confirms existing promotion and
activation, securely prompts for new passwords if needed, and preserves existing
identity/password/verification. Action uses PostgreSQL transaction/advisory try-lock
(71001, 1), rechecks ANY admin assignment and account conditions, and commits
activation/role/audit together. Verification notification follows commit; failure
cannot undo provisioning. Refusals/failures use separate sanitized application logs.

### Application files changed by developer

Created:

- backend/app/Actions/Staff/BootstrapAdministrator.php.
- backend/app/Console/Commands/BootstrapAdministratorCommand.php.
- backend/app/Http/Middleware/EnsureStaffAccess.php.
- backend/app/Models/AuditEvent.php.
- backend/database/migrations/2026_11_02_123148_create_audit_events_table.php.

Modified:

- backend/app/Models/User.php (hasStaffRole).
- backend/app/Providers/FortifyServiceProvider.php.
- backend/bootstrap/app.php and backend/config/fortify.php.
- backend/routes/web.php and backend/routes/settings.php.
- backend/app/Http/Controllers/Settings/ProfileController.php.
- backend/app/Http/Requests/Settings/ProfileUpdateRequest.php.
- backend/resources/js/pages/Welcome.vue, auth/Login.vue, settings/Profile.vue.

Deleted backend/resources/js/pages/auth/Register.vue and
backend/resources/js/components/DeleteUser.vue. Build/Wayfinder generated output
regenerated by commands. Assistant changed AGENTS.md and all five docs files only.

### Actual checks and results

Developer-executed evidence:

- Command help discovered staff:bootstrap-admin and its description.
- Source review corrected whereRaw lookup and email validation rule map.
- Initial Pint found three formatting issues; targeted Pint fixed them.
- Initial PHPStan reported two User/Collection candidate errors; explicit
  instanceof User check corrected middleware type/runtime boundary.
- Final Pint: 62 files PASS.
- Final PHPStan at --memory-limit=512M: 47 files, no errors.
- npm types:check completed without errors.
- npm check: 66 formatted; 60 linted with no warnings/errors.
- npm build: 3574 modules; successful in 20.45 seconds. Optional fontaine warning
  and plugin timing information nonblocking.
- No separate php -l command executed in Task 07.
- Tinker SQL verified fieldops_dev / fieldops_app before migration; migrate:status
  showed prior migrations ran and only audit migration pending.
- artisan migrate applied audit migration successfully (153.77 ms). No resets.
- Bootstrap reported committed success. Existing user ID 1 promoted and activated.
- Browser confirmation: promoted account login/dashboard, read-only profile email,
  no deletion control, GET /register 404 all behaved as expected.
- Repeat bootstrap refused with existing-Administrator/recovery message.
- Audit count after refusal: 1 success event. Selected row: console, null actor_id,
  subject_id 1, promoted, administrator assigned, activation false to true.
- Split-line Tinker query initially produced parse error; one-line retry succeeded
  without data mutation. Raw JSON display alone does not verify runtime array cast.

No automated tests added/executed, including no database-free unit tests. No
runtime SQLite isolation verification or PostgreSQL automated integration suite.
Task 03 historical test results are not new Task 07 results.

### Approved limitations and deferred checks

Not exercised: inactive/roleless and guest/JSON rejection, POST registration,
privileged/changed-email profile payloads, password recovery, remember-me,
2FA/passkeys, eligible unverified access, new-account bootstrap, delivery failure,
noninteractive/collision/deactivated-account refusal, forced rollback/audit failure
or concurrent bootstrap. Sequential refusal is not a concurrency test. Advisory
lock and atomicity reviewed in source only. Security logs not independently inspected.

No complete RBAC/resource policies, general staff UI, invitations, recovery command,
last-admin mutation safeguards, full session/token revocation or audit retention/
tamper resistance. User approved these verification gaps. Existing optional fontaine,
React peer, default PHPStan memory, HMR/SSR, CI and infrastructure limits remain.

### Repository and next step

User committed/pushed foundation; latest inspected commit 70e8693 (chore:
initialize FieldOps foundation). Origin https://github.com/WeeRG91/fieldOps.git.
Current branch feat/staff-access-bootstrap tracks origin/feat/staff-access-bootstrap.
Task changes remain staged/unstaged/untracked, including mixed action staging and
staged DeleteUser deletion. project-commands.txt remains untracked local notes.
Assistant did not stage/commit/push. Next recommended Task 08: agree staff
administration, last-admin concurrency, revocation and audit scope before coding.
No application checks rerun during documentation editing.

## Task 08 — Staff administration, last-admin protection and revocation

### Status and approval

Completed 2026-11-02. User explicitly chose manual verification only, approved
results and listed gaps, then authorized all six continuity updates. Developer
wrote code/ran commands; assistant inspected read-only and edited documentation
with permission. No migration, dependency, test files or separate php -l command.
No automated tests/isolation verification. Historical results not relabeled.

### Recovery and implementation decisions

Six docs and actual identity/auth/schema/session/test/UI/Git inspected. Task 07
merged to main at 9f06938, feature branch removed; older handover Git snapshot stale.
Developer created feat/staff-administration. At closeout latest commit 9f06938;
Task 08 staged/unstaged/untracked (action/new Vue/types have mixed AM snapshots),
project-commands.txt untracked. No assistant staging/commit/push.
UserPolicy: active verified admin views all accounts/manages others; no self role/
state changes, admin bypass, hard delete or identity editing. Full nonempty
role selection; inactive roles retained, reactivation explicitly reviews roles.
Deactivation needs trimmed reason <=1000; activation leaves verification unchanged.
ManageStaffAccess: top-level PostgreSQL READ COMMITTED, shared try-advisory lock
(71001, 1), deterministic actor/subject row locks, fresh policy check, mutation/
revocation/audit, final usable-admin check. Reject nested transactions and SQLite.
Future protected writes must cooperate; direct SQL/helpers bypass safeguards.
Database sessions on SAME connection required; actual changes rotate remember_token
and delete stored sessions. No-op role sets do not audit/revoke. In-flight/session
rewrite races not solved; no token/WebSocket infrastructure.
Three authorized Form Requests, thin controller/selected responses, pagination 20,
batched role query. Five auth/staff/verified model-bound routes; confirmation
middleware authorizes before Fortify timestamp. Expired browser writes return
through confirmation to GET details, require resubmission. JSON 423 branch untested.
Inertia conflicts redirect 303 with session form errors, other clients normal 409
by source. Typed directory/details/forms, confirmations/errors and server-capability
sidebar. No creation/invitation/audit-management interface.

### Files created by developer

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
Wayfinder generated (--with-form --skip-database) and assets built. No new business
source claimed from generated output. Assistant updated AGENTS and five docs only.

### Actual quality checks

Developer-executed evidence:

- Full Pint 64/PHPStan 49 no errors after action checkpoint.
- Later full Pint 69 passed. PHPStan initially found middleware concrete Gate
  static call/magic timestamp arithmetic; facade/getTimestamp corrected.
- PHPStan 54 no errors at 512 MiB after fixes and again after final conflict handler.
  Later changed PHP files targeted Pint passed; final bootstrap 1 file PASS.
  No redundant final full Pint or separate syntax run claimed.
- route:list --path=staff -v displayed all five routes/expected middleware.
- Frontend check:fix, build, types:check reported all passed; counts/timings not
  supplied. No later Vue change after manual verification began.
- Review caught missing self/nested guards, undefined connection, actor-vs-subject
  role target, missing method/import, confirmation fall-through/Gate and distinct
  typo before verification. Explicit ID-to-role array resolved IDE inference issue.
  role=alert retained as valid accessibility markup.

### Actual manual evidence

- Tinker target fieldops_dev/fieldops_app confirmed; fixture email absent.
- Hidden prompt with visible fallback disabled created Manual Staff, user 2,
  fieldops.manual@example.test: inactive/roleless, manually verified solely for
  fixture. Primary account not modified; password not documented.
- Primary admin navigation/directory/details and self-hidden controls passed.
- First Dispatcher activation required password confirmation; returned without
  activation, second submission activated successfully.
- Dispatcher login/dashboard passed; Staff hidden; direct /staff and /staff/1 403.
- Dispatcher -> Technician role replacement passed, private-session refresh
  redirected to login; fresh dashboard access passed and /staff remained 403.
- Deactivation retained Technician/reason; revoked session; fresh correct login
  rejected.
- First three mutation audit rows independently inspected via toArray: activated,
  roles_replaced, deactivated, actor_type user/actor 1/subject 2, expected timestamps,
  array before/after roles/state/grants/revocations/reason. Primary roles unchanged.
- Reactivation with Administrator+Technician passed, current deactivation fields
  cleared; fixture staff access/self-hidden controls/multiple roles passed.
- Direct Tinker action: self-role/self-deactivation authorization denied; empty,
  duplicate, unknown roles validation rejected; unchanged reverse-order roles added
  no audit (comparison true). Not HTTP payload coverage or automated feature tests.
- Tinker-held advisory lock blocked separate browser mutation; rollback released
  lock, retry to Dispatcher succeeded. Initial debug popup prompted UX refinement.
- Repeated held-lock check after handler: inline error, input preserved, saved state
  unchanged, lock released. No retry requested afterward. Real cross-connection
  contention, not two complete simultaneous staff mutations or exhaustive races.
- Cleanup deactivated fixture via UI. Final SQL: user 2 inactive, deactivated_by=1,
  timestamp present, reason 'Task 08 manual verification complete'; primary 1 active,
  verified, administrator. Fixture/history retained. User Hidden fields disappeared
  from toArray by design; query builder verified metadata without changing model.
  Later audit rows after initial three not separately inspected/count-reconciled.

### Approved gaps and next task

No automated tests/new tests/SQLite runtime isolation. Starter tests/factory still
assume old access/email/deletion rules; registration suite skips instead of proving
closed registration. Do not blindly run. PostgreSQL-only/nonnested action requires
future test strategy; SQLite cannot prove locking. No PostgreSQL automated suite.
Final last-admin failure, forced write/audit rollback, remember-me cookies,
in-flight/session races, HTTP unauthorized mutations/privileged payloads, JSON
branches, multi-page pagination and exhaustive concurrency unverified. Older auth
flows (2FA/passkeys/recovery/unverified/active-roleless/inactive-admin) not fully
covered. User approved these limitations; source review is not execution evidence.
No Sanctum/Reverb to revoke; no invitation/recovery/audit UI, retention/tamper
resistance or email-change safety workflow. Prior runtime/infrastructure limits
remain. Next Task 09 invitation-only provisioning/atomic acceptance per Task 05,
shared lock, token lifecycle, audit, explicit notification/testing scope.
Redis/queues/Horizon/Reverb visible deferred infrastructure, no install implied.
Documentation closeout uses source/user outputs; no app checks rerun by assistant.

## Task 09 — Invitation-only staff provisioning, completed 2026-11-04

### Approval and scope

Developer implemented application code and ran commands; assistant read/reviewed
and mentored. On 2026-11-04 user confirmed final UI change, accepted manual-only
closeout/deferred verification scope and explicitly requested continuity updates
to wrap up Task 09. No assistant app edits/install/migration/Git mutation.
All six continuity files updated. No new project dependencies, DB reset or mobile/
queue/Horizon/Reverb infrastructure. No automated tests/new test files/isolation
verification; this is the approved Task 09 exception, not a new global testing ban.

### Recovery and decisions

Task 08 merged at edc9cc9, superseding its old precommit handover; current
feat/staff-invitations tracks origin/feat/staff-invitations. Task 09 AM/staged/
unstaged/untracked, some index entries old scaffolds. project-commands.txt local
untracked. No Task 09 commit/merge reported; do not commit partial staged snapshots.
Existing docs/identity/staff/schema/auth/mail/test/Git inspected; Task05 policy
preserved. Active verified Admin invitation management; normalized fixed recipient,
explicit distinct nonempty fixed roles, no existing account (including inactive).
Admin invitation requires extra explicit confirmation/recent password, no second
approver. New bearer acceptance active+verified, own password, login redirect.
No silent existing account modification/public registration/self escalation.

### Implementation

Three additive migrations (names in handover) create invitations/constrained role
relation and normalized-user-email unique functional index. Preflight normalized
collision collection empty, target fieldops_dev/fieldops_app verified. All three
migrate DONE, developer output154.95ms/5.51ms/3.09ms. No final migration status repeat.
Schema checks hash/email/expiry/pairs/exclusive lifecycle; partial unique normalized
email on unterminated records, expired rows superseded before replacement.
Fixed48h. Secure token32 random bytes/64hex, SHA256 storage/hash_equals.
Delivery DTO/native sensitive wrapper redacts debug/JSON, never flash/props/audit.
No exhaustive runtime secret-inspection claim.

ManageStaffInvitations owns issue/resend/revoke; resend new record/token/48h and
new acting inviter, fixed roles/email, prior row superseded. Audit each mutation.
ValidateStaffInvitation reused by Inspect/Accept; preview no reservation.
AcceptStaffInvitation owns PostgreSQL READ COMMITTED transaction, shared advisory
try-lock(71001,1), fresh locked inviter then invitation, revalidates secret/state/
expiry/authority/roles/collision. Hash password before lock; new user+roles+
activation/verification+consume+audit share transaction. Unique violation generic
after rollback. No nesting/SQLite. Direct SQL/helpers bypass cooperation.

Existing MailDev2.2.1 used through current Node and old nvm entrypoint, bound
localhost SMTP1025/UI1080. SMTP capture smoke passed. Ignored .env and safe
.env.example changed to local SMTP/null credentials/FieldOps sender.
StaffInvitationNotification/SendStaffInvitation synchronous after commit,
SMTP forced not log. Delivery failure leaves saved invitation/warning; resend
invalidates old link. No atomic mail/DB/outbox. Only local captures verified.

Public numeric-ID GET acceptance, POST preview/acceptance; guest-only requests,
CSRF, limits30/20/5 per minute IP, secret dontFlash, no-store/no-referrer.
GET only ID/mustSignOut; secret URL fragment read browser-only/removed, preview
POST validates before selected recipient fields. Acceptance input name/password/
confirmation/token, missing specified privileged fields; min12 mixed case/number/
symbol. Vue dontRemember secrets/clear fields; redirect login no automatic login.
Admin index/store/resend/revoke, policy/requests/recent-password middleware/fresh
action authorization; history20/page, explicit roles/Admin confirmation, errors/
toasts/resend/revoke; sidebar policy capability. Inertia conflicts/throttle inline
handlers. Wrong Inspect request import on admin store found by review and fixed.
Static review also corrected logical operator/confirmed key/boolean existence
errors earlier; static checks alone did not establish business correctness.

Shared lib/date.ts Intl en-GB UTC formatter added, used by two invitation pages
and existing staff details. Both invitation controllers and existing StaffController
UTC response conversion; offset mislabeled UTC corrected without storage rewrite.
Duplicate email error summary+field display removed at end.

### Files

Full exact inventory in PROJECT_HANDOVER.md. New model/policy; five Staff actions;
sensitive DTO/notification; public/admin controllers; three staff requests/two public
requests; confirmation middleware; three migrations; public/admin Vue pages,
invitation types, shared date helper. Modified routes/bootstrap/AppServiceProvider/
Inertia shared props/sidebar/auth types/.env.example; ignored local .env;
existing StaffController/Show.vue UTC presentation. Generated Wayfinder/assets.
Assistant changed only AGENTS and five continuity docs with explicit authorization.

### Executed checks

Developer evidence:
- Migration php -l/Pint3 passed; PHPStan57 at migration checkpoint.
- Later PHPStan59/61/63/65/66/69 checkpoints; final admin-backend74/no errors,
  --memory-limit=512M. No rerun after small UTC response changes.
- Targeted Pint7 files fixed4 issues; route:list3 public/four admin with middleware.
- Frontend check:fix formatting completed, lint66/no warnings/errors, vue-tsc passed,
  production build3586 modules passed; Inertia middleware Pint1 PASS.
- Date changes: check:fix/types/build and two invitation-controller Pint --test
  reported all passed, no detailed counts. Extra existing StaffController UTC
  change not in that targeted list; no separately claimed result.
- Final duplicate-error import/template deletion and .env.example update after
  quality checkpoint, no repeat frontend check claimed.
- No full final Pint/separate full php -l/automated tests/runtime-isolation run.
  Earlier Task03 generated40 tests are historical, not Task 09 results.

### Manual PostgreSQL/browser evidence

- Target fieldops_dev/fieldops_app, unused acceptance fixture email/no invitations.
- Admin issue Dispatcher to fieldops.invitation@example.test; user reported
  expected form/history/local MailDev success. No account until acceptance;
  that intermediate absence was expected, not independently queried.
- Signed-out preview correct fixed email/Dispatcher/expiry/form; fragment removal
  not separately confirmed. Accepted success message/login redirect; user3
  Invitation Staff login/dashboard/no extra verification prompt reported as
  successful workflow, hidden Staff/Invitations; direct both GET403 confirmed.
- Tinker user3 active=true, verified=true, Dispatcher=true, Admin=false;
  invitation1 accepted and accepted_user_id matches. Exactly one selected
  staff.invitation_accepted row inspected: actor_type user/actor3/subject3,
  metadata invitation_id1/invited_by1/dispatcher. No passwords/hashes documented.
- Signed-out reopening original accepted link unavailable/no form.
- Technician fieldops.resend@example.test: resend old replaced/new pending,
  second capture, old link unavailable/new preview correct collectively confirmed.
  Revocation then latest link unavailable confirmed. No acceptance.
- Invitation4 fieldops.expiry@example.test manually changed created_at to49h
  ago/expires48h after; guarded target/status check, status expired output.
  Original email link unavailable confirmed. Artificial expiry fixture, not real
  elapsed-time/boundary test; original issuance audit unchanged.
- Existing accepted email issue returned 'An account already exists for this email.'
  Subsequent unchanged roles/no extra email not independently queried.
- New Technician fieldops.validation@example.test pending; duplicate returned
  'A pending invitation already exists. Use Resend.' No extra-email/row count
  independently checked. Duplicate inline display prompted final UI cleanup.
- Original link token last hex character modified: unavailable/no form.
  This proves valid-format tamper rejection, not all malformed-token cases.
- No final fixture cleanup requested. User2inactive retained from Task 08, user3
  activeDispatcher retained; primary1 not role/state changed; resend fixture
  revoked, fixture4expired, validation fixture pending when checked.

### Approved exceptions, limitations and next task

No automated tests/new tests/isolation proof; no dedicated PostgreSQL suite.
SQLite cannot execute these PostgreSQL actions/constraints unchanged or verify
advisory locks/concurrency. Legacy test/factory assumptions still need revision.
User approved deferring privileged HTTP payloads, SMTP failure, inviter eligibility
changes, concurrent acceptance/rollback and broader gaps. Administrator invitation,
signed-in acceptance denial, rate-limit/JSON branches, boundary expiry, casefold/
trim collisions, expired/revoked reissue, post-issuance account collision, exhaustive
audits/log secret inspection and multi-page pagination unverified.
Unauthorized GET403 is not issuance POST coverage. Sequential consumed-link
rejection is not simultaneous acceptance evidence. Transaction code reviewed,
not tested by forced write/audit failure. No absence-of-plaintext or password-hash
runtime audit. SMTP capture is not external delivery/atomic email guarantee.
Task 08 last-admin/remember-me/session races/inherited auth gaps persist.

Task 10 recommended: agreed authorization coverage/controlled console recovery,
shared lock+usable-admin safety+transactional audit, no public recovery/self bypass.
Redis client/queues/Horizon/Reverb/Sanctum/Expo/mobile/business modules/CI deferred.
