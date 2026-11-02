# FieldOps — Architecture

## Status

Tasks 01–05 established planning, runtime, PostgreSQL, and authorization design.
Task 06 role/activation persistence is implemented and approved on 2026-11-02.
Authorization enforcement and provisioning remain unimplemented.
Database-backed tests and runtime-isolation verification were explicitly deferred.
Historical task sections describe their respective checkpoints.

## Architectural approach

FieldOps uses a modular monolith: one Laravel backend with clear business
boundaries and a shared database.

The backend serves:

- An Inertia web application for administration and dispatching.
- A versioned REST API for the technician mobile application.

This approach keeps deployment and transactions straightforward while
allowing business responsibilities to evolve independently within the
application.

Microservices are outside the initial scope.

## Technology targets

| Area | Target |
|---|---|
| Backend | Laravel 13 and PHP 8.5 |
| Business database | PostgreSQL |
| Queues and cache | Redis |
| Queue management and monitoring | Laravel Horizon |
| Real-time transport | Laravel Reverb |
| Mobile API authentication | Laravel Sanctum |
| Periodic work | Laravel Scheduler |
| Web | Vue 3, TypeScript, and Inertia.js |
| Mobile | React Native, Expo, and TypeScript |
| Version control | Git |

These are intended targets, not verified compatibility claims.
Check package constraints, runtime versions, and required extensions before
installation.

## System structure

Web browser:
Vue and TypeScript -> Inertia -> Laravel web controllers

Mobile application:
React Native and Expo -> REST API -> Laravel API controllers

Both controller types use shared application actions, authorization,
and business rules.

Laravel stores business data in PostgreSQL.
Redis supports queues and appropriate caching.
Horizon runs and monitors Redis-backed queue workers.
Reverb delivers authorized real-time updates.
Scheduler triggers periodic commands and jobs when required.

## Business boundaries

The initial modules are:

- Identity and Access.
- Customers and Sites.
- Equipment.
- Service Requests.
- Work Orders.
- Scheduling and Assignment.
- Field Execution.
- Notifications.

Each module owns its business behavior and related data changes.
Changes to another module's data should use explicit application operations.

Cross-module reads may use practical database queries.
Do not introduce an interface or repository abstraction for every read.

These boundaries do not imply separate deployments, databases, or packages.

## Application responsibilities

### HTTP layer

Controllers handle HTTP coordination and responses.

Form Requests validate input structure and request-level requirements.
Policies authorize operations on records.
API Resources define explicitly selected response fields.

Web controllers do not call API controllers.
Neither entry point duplicates business rules.

### Application actions

Named actions coordinate important use cases, such as:

- AssignTechnician.
- SubmitWorkOrderCompletion.
- CloseWorkOrder.

These names are illustrative.

Actions coordinate authorization where needed, business behavior,
persistence, transactions, and subsequent events or jobs.

The authorization design must cover every entry point.
Calling an action outside HTTP must not accidentally bypass required rules.

### Business behavior

Shared business logic enforces invariants and allowed state transitions.

Examples include:

- Equipment referenced by a work order belongs to its site.
- A request produces at most one work order in the MVP.
- A work order has at most one currently assigned technician.
- Completion requires the necessary report.
- Closure requires dispatcher review of completed work.

Detailed transitions, editing rules, and concurrency handling will be
specified and tested before implementation.

Use database constraints where they can enforce an invariant reliably.

## Web application

Keep Vue pages and supporting TypeScript inside Laravel's resources/js.

Use Laravel routes, session authentication, policies, and Inertia responses.

The web application does not need a separate REST endpoint for every page.
It shares application actions with the mobile API.

## Mobile application and REST API

Keep the Expo application in mobile/.

Use versioned endpoints under /api/v1.
The server remains authoritative for validation, authorization, and state.

The MVP requires connectivity to save changes.
Display failed saves clearly and do not represent unsaved data as saved.

Offline storage, synchronization, and conflict resolution are deferred.

Define response contracts, pagination, validation errors, and mutation
retry behavior before implementing the relevant endpoints.

## Authentication and authorization

Use session authentication for the Inertia web application.
Use Sanctum API tokens for the mobile application.

Define secure mobile token storage, expiry, revocation, and logout behavior
during the authentication milestone.

Use policies and explicit business rules to restrict operations.
A role alone does not grant access to every record.

Private broadcast channels require subscription authorization.
Never rely on hiding interface controls as an access-control mechanism.

## Persistence and transactions

PostgreSQL is the authoritative store for business data and change history.

Use migrations, foreign keys, and appropriate constraints.
Group related changes in transactions when they must succeed together.

For example, closing a work order and recording its history should succeed
or fail together.

Redis must not be the only store for essential business state.

Concurrency-sensitive operations require an explicit strategy before
implementation. Examples include duplicate request conversion and
simultaneous status changes.

## Queues and background jobs

Use Redis-backed queues and Horizon.

Queue work that can occur after the main operation, such as notification
delivery and broadcasting.

Dispatch jobs and events that depend on saved data after the transaction
commits.

Essential business changes must not depend on a notification succeeding.
Define retry and duplicate-processing behavior for jobs when introduced.

## Real-time updates

Use Reverb with authenticated private channels.

Broadcasts inform authorized clients that relevant state has changed.
Clients retrieve authoritative state from the backend.

After reconnecting, clients refresh current data because events may have
been missed.

Broadcast payloads must expose only information authorized for recipients.

## Scheduled work

Use Laravel Scheduler for periodic commands and jobs when a feature
requires them.

Scheduler determines when work should run.
Queue workers execute work submitted to queues.

No scheduled business feature is required merely to demonstrate Scheduler.

## Repository organization

```text
fieldops/
  AGENTS.md
  README.md
  docs/
    PROJECT_OVERVIEW.md
    ARCHITECTURE.md
    ROADMAP.md
    TASK_LOG.md
    PROJECT_HANDOVER.md
  backend/
    app/
    resources/js/
    routes/
    tests/
  mobile/
```

README.md and application directories will be created when relevant.
Their appearance in this layout does not mean they already exist.

Within backend/app, retain familiar Laravel locations:

- Actions/, grouped by business responsibility.
- Enums/.
- Events/.
- Http/Controllers/Web/.
- Http/Controllers/Api/V1/.
- Http/Requests/.
- Http/Resources/.
- Jobs/.
- Models/.
- Notifications/.
- Policies/.

Introduce directories only when useful.
Do not add a custom module framework initially.

## Local development

Task 02 decision, approved by the user on 2026-10-31:

- Active repository: C:\Sites\FieldOps, accessed from Ubuntu as
  /mnt/c/Sites/FieldOps.
- Windows/Herd runs the web application, Composer, Node.js, and npm.
- Ubuntu through WSL 2 runs Horizon, Redis, and Linux worker processes.
- Reuse the Windows PostgreSQL installation. Docker is optional and unused.
- PhpStorm is the backend/web editor; VS Code is intended for mobile.
- Use `herd php` and `herd composer` in the project to select Herd PHP 8.5.
  The plain Windows `php` command previously selected PHP 8.3.22.

This supersedes Task 01's proposed all-Ubuntu runtime and WSL filesystem
location. The modular-monolith design and business scope are unchanged.
The temporary Ubuntu repository copy was compared with the Windows copy;
its .gitignore was preserved, and the duplicate was removed by the user.

Herd links http://fieldops.test to C:\Sites\FieldOps\backend with PHP 8.5.
Project-aware CLI commands in backend/ select PHP 8.5.10; browser rendering works.

### Task 02 inventory — historical baseline

Evidence: command outputs supplied by the user during Task 02, plus earlier
read-only inspection in this chat. Versions describe observed installations,
not a claim that every component is current or secure.

| Component | Windows | Ubuntu |
|---|---|---|
| PHP | Herd project PHP 8.5.10; plain PHP previously 8.3.22 | 8.5.4 |
| Composer | 2.11.1 under Herd PHP 8.5.10 | Not found in inspected PATH |
| Git | 2.49.0.windows.1 | 2.53.0 |
| Node.js | 22.16.0 | Not found in inspected PATH |
| npm | 11.19.1 | Windows npm resolves but fails without Linux node |
| PostgreSQL | Installed binary 18.6; localhost:5432 accepts connections | psql not found in PATH |
| Redis | localhost:6379 reachable by TCP | Server/CLI 8.10.1; localhost PING returns PONG |

WSL software: 2.7.12.0. Distribution: Ubuntu 26.04 LTS using WSL 2.
Earlier inspection reported mirrored networking and Ubuntu-to-Windows
PostgreSQL TCP reachability. Authentication and SQL were not verified.
Docker CLI was unavailable in Windows PATH and no Docker Desktop process
was reported. Docker installation itself was not conclusively determined.

Herd PHP 8.5 includes pdo_pgsql, pdo_sqlite, and sqlite3. Its Redis extension,
pcntl, and posix were absent. Ubuntu PHP includes pdo_pgsql, pcntl, and posix;
SQLite and Redis PHP extensions were absent in its inspected module list.

### Compatibility assessment

Official Laravel 13, Horizon, Reverb, and Sanctum manifests inspected during
Task 02 accept PHP 8.5 and declare the relevant Laravel 13 support.
Scheduler is part of Laravel. These are declared constraints, not proof of
successful dependency resolution or execution.

User-executed npm registry metadata checks established:

| Package checked | Version | Result with Node 22.16.0 |
|---|---|---|
| npm | 11.19.1 | Meets ^20.17.0 or >=22.9.0 |
| Vite | 8.3.5 | Meets ^20.19.0 or >=22.12.0 |
| laravel-vite-plugin | 3.2.0 | Node range satisfied; requires Vite ^8 and fontaine ^0.8 |
| @vitejs/plugin-vue | 6.0.10 | Node range satisfied; permits Vite 8 |
| Expo | 58.0.21 | Meets ^22.13.0 range |
| React Native | 0.88.1 | Meets ^22.13.0 range |
| create-expo-app | 5.0.0 | Meets >=20.0.0 |

These versions were queried, not installed or pinned. The selected Laravel
starter kit may use Vite+; inspect its exact manifest before Task 03 installs.
Expo must select matching React/React Native versions; do not independently
install the latest versions from this table.

Official sources consulted:

- https://github.com/laravel/framework/blob/13.x/composer.json
- https://github.com/laravel/horizon/blob/5.x/composer.json
- https://github.com/laravel/reverb/blob/main/composer.json
- https://github.com/laravel/sanctum/blob/4.x/composer.json
- https://github.com/laravel/vue-starter-kit
- https://docs.expo.dev/get-started/create-a-project/
- https://getcomposer.org/doc/03-cli.md#self-update-selfupdate
- https://composer.github.io/pubkeys.html

### Remaining runtime decisions

Windows Composer cannot satisfy Horizon's pcntl/posix requirements natively.
Before adding Horizon, agree and verify a specific cross-platform install
procedure and Linux runtime validation. Do not broadly ignore platform
requirements or assume a Windows-generated vendor directory works in Linux.
Ubuntu Composer is not required for initial setup but may become appropriate
for that procedure. Select a PHP Redis client before Redis-backed features.

Task 03 subsequently scaffolded backend/ and installed application dependencies.
PostgreSQL credentials/database permissions, authenticated PHP connections,
worker execution, Reverb, and device networking remain deferred.

## Engineering conventions

- Use English and consistent business vocabulary.
- Keep controllers focused and business rules shared.
- Use explicit state transitions.
- Prefer focused application actions over unnecessary abstraction.
- Use strict TypeScript checking.
- Use the installed Laravel Pint for PHP formatting.
- Use the generated Vite+ lint/format checks; backend formatting currently uses four spaces.
- Store instants in UTC and convert for display.
- Define timezone semantics for planned visit windows before implementation.
- Keep secrets out of Git.
- Maintain safe example environment files.
- Use short-lived branches and focused commits.

PHPUnit is the initial testing direction.
Confirm the generated testing setup during Laravel initialization.

## Testing strategy

Use unit tests for isolated business logic.
Use Laravel feature tests for HTTP workflows, validation, authorization,
persistence, and framework integration.

Use SQLite in-memory databases where persistence is genuinely needed.
Do not create a dedicated PostgreSQL integration-test suite at this stage.

Use Laravel fakes and mocks for dependencies outside the behavior under test.
Do not replace the behavior being verified with a mock.

Manually check important web, mobile, and real-time workflows.

### Limitations

SQLite tests do not prove PostgreSQL-specific behavior, including SQL,
types, constraints, locking, and concurrency semantics.

Queue and broadcast fakes do not verify Redis, Horizon, workers, Reverb,
or actual client delivery.

Record relevant gaps and deferred checks for each implementation task.
Never report an unexecuted test as passed.

## Deferred architectural decisions

Resolve these when their implementation tasks begin:

- Exact application dependency versions and cross-platform installation procedure.
- Authenticated PostgreSQL access and application connectivity.
- Detailed database schema and work-order state transitions.
- Concurrency and mutation retry strategies.
- Mobile token lifecycle and secure storage.
- API contracts and error conventions.
- Notification recipients and delivery details.
- Planned visit timezone rules.
- Production hosting, process supervision, and deployment.
- CI/CD tooling and pipeline design.

These are deliberate implementation-stage decisions, not completed work.
## Task 03 — Initialized application, 2026-11-01

### Stack and structure

Laravel 13.39.0; Inertia Laravel adapter 3.6.0; Fortify 1.41.0;
Vue 3.5.44; Inertia Vue/Vite adapters 3.10.0; TypeScript 5.9.3; Vite+ 1.4.0.
The standard app/, bootstrap/, config/, database/, routes/, resources/js/,
and tests/ structure is retained. No domain modules were added.
Requests enter public/index.php, pass through bootstrap/middleware to routes
or controllers, then Inertia supplies the selected Vue page and its data.

The Vue starter kit uses Vite+, not plain Vite. Generated configuration is
vite.config.ts. Its backend/.editorconfig and formatter use four spaces;
this overrides the root frontend default and is retained deliberately.

### Runtime and local storage

Windows Node was updated through existing nvm 1.1.11 from 22.16.0 to 22.23.4;
active bundled npm is 10.9.9. A fresh terminal was needed after switching.
Vite+ 1.4.0 requires ^22.18.0, ^24.11.0, or >=26; Node 22.16.0 did not meet
that requirement despite passing the Task 02 standard Vite checks.
Build, frontend types, and lint/format checks passed again after the update.

APP_URL is http://fieldops.test. The installer-generated duplicated port
was corrected. The app key exists; never record its value in documentation.
SQLite is temporary local development storage; sessions/cache/queues use
database drivers. Five generated migrations ran. PHPUnit uses a separate
SQLite :memory: database. PostgreSQL remains the intended business database.
The generated .env.example remains a generic template, not proof of actual
configured services. Its phpredis setting does not mean PHP Redis is installed.

### Verification

User-executed commands and user-reported browser observations:

- Laravel test suite: 40 passed, 138 assertions, 42.37 seconds.
- Production build on Node 22.23.4: passed, 2.34 seconds.
- vue-tsc --noEmit: passed.
- vp check: 68 files formatted; 62 linted without warnings/errors.
- Pint --parallel --test: 54 files passed.
- Default PHPStan command: failed at 128 MiB; analysis incomplete.
- PHPStan retry with --memory-limit=512M: 39 files, no errors.
- Installed Composer platform requirements: passed under PHP 8.5.10, exit 0.
- Welcome page rendered with Vite+ dev server and generated Wayfinder types.
- With Vite stopped and public/hot absent, welcome/login/register pages
  remained styled and rendered without reported console errors.
- Secrets, SQLite data, vendor/, node_modules/, and public/build are ignored.
- No nested Git repository, staging, or commits.

### Remaining limitations

- Optional fontaine font-fallback optimization warning remains; no dependency added.
- React 19.3.1 remains extraneous via optional @laravel/passkeys peer.
  npm prune --dry-run proposed no cleanup and exposed the Node engine issue;
  no actual prune was executed.
- Frontend dependencies existed at first inspection despite declining the
  installer's final npm prompt; their exact installation origin is unverified.
- Default PHPStan memory failure is not persistently fixed; use the recorded override.
- Manual registration/login submission, HMR after editing, production SSR,
  PostgreSQL authentication, PHP Redis operations, Horizon/Reverb, and mobile
  workflows are not verified. No business features have been implemented.
- Generated backend/.github workflow is not configured as root repository CI.

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
## Task 05 — Staff authorization and provisioning design

### Implementation status

The following design was approved during Task 05.
It is not implemented yet.

Current starter behavior still permits public registration and staff
self-deletion. Roles, activation enforcement, invitations, and sensitive
operation auditing do not yet exist.

Redis and queue infrastructure are deferred to a later foundation task.

### Staff roles

Use three predefined roles:

- Administrator: staff accounts, role assignments, activation, system configuration.
- Dispatcher: customer/site/equipment operations, service requests,
  work planning, assignments, scheduling, completion review, operational reporting.
- Technician: execution of assigned work, findings, blockers, completion submission.

Dispatcher includes the service-manager responsibilities agreed in Task 01.
A separate Manager role is deferred.

An account may hold multiple roles. Permissions combine explicitly.
Administrator does not automatically receive operational permissions.
No global Administrator authorization bypass is permitted.

Future customer portal accounts require separate onboarding and access rules.
No multi-tenancy is introduced.

### Role representation and authorization

Use a fixed PHP StaffRole enum and a user_roles assignment table:

- user_id foreign key.
- Constrained role value.
- Unique combination of user_id and role.

No dynamic permission tables, role inheritance, per-user permission overrides,
or authorization package are required initially.

Use middleware for authentication, email verification, and active staff access.
Use Policies for resource operations and Gates for broader capabilities.
Shared application logic enforces business invariants across entry points.

Scope lists, searches, exports, and related records as well as individual actions.
Vue capability flags help presentation but never replace server enforcement.
Return selected fields; exclude authentication secrets and unnecessary staff data.

### Initial permission matrix

All permissions require active, verified staff access.

| Capability | Administrator | Dispatcher | Technician |
|---|---|---|---|
| Staff invitations, roles, activation | Yes, with safeguards | No | No |
| Staff administration details | Yes, excluding secrets | No | Own profile |
| Scheduling directory | No | Necessary scheduling information | No |
| Customers, contacts, sites | No | Manage | Relevant assigned-work details |
| Equipment | No | Manage | Relevant assigned-work details |
| Service requests | No | Assess/manage | Relevant assigned-work details |
| Work-order creation and planning | No | Yes | No |
| Assignment and scheduling changes | No | Yes | No |
| Field progress/findings/blockers | No | Read for coordination | Assigned work |
| Completion submission | No | No | Assigned work |
| Review, return, closure | No | Independent reviewer only | No |
| Operational reporting | No | Company operational scope | Own work summary |
| System configuration | Yes | No | No |
| Sensitive staff/security audit | Yes | No | No |
| Work-order history | No | Operational scope | Permitted assigned-work history |

A No entry means that role grants no permission; additional assigned roles
may grant it, subject to all record and business restrictions.

Dispatcher + Technician accounts may assign work to themselves using the
audited scheduling operation. Technician update operations cannot change
assignments.

Anyone who contributed technician execution records cannot approve or close
that work order, even with Dispatcher. Reassignment preserves this restriction.

Technician access initially follows current assignment. Reassignment removes
access; existing execution records remain preserved. Broader historical access
requires a later explicit decision.

### Closed staff provisioning

Disable public registration routes and links when implementing provisioning.
Do not silently promote existing accounts or the first public registrant.

Bootstrap the first Administrator through a controlled interactive console
command, with no hardcoded credentials. It operates only when no Administrator
role has been assigned and records an audit event.

Selecting an existing account requires explicit confirmation and retains
password/email-verification protections. Console access is a privileged boundary.

The initial bootstrap account may temporarily be unverified; normal staff
administration remains unavailable until verification succeeds.

Subsequent accounts are invited by active, verified Administrators.
Administrators do not distribute shared or default employee passwords.

Replace staff self-deletion with controlled deactivation to preserve history.
Administrative recovery requires a separate documented console procedure,
not public registration, automatic promotion, or a hidden web bypass.

### Invitations

- Administrator selects a fixed recipient email and explicit roles.
- Expiry is 48 hours.
- No usable staff account exists before acceptance.
- Store a token hash; keep raw invitation secrets out of audit records.
- Permit at most one pending invitation per consistently normalized email.
- Resending replaces the token and starts a new expiry period.
- Acceptance cannot change the recipient or supply additional roles.
- Recheck that the inviter remains active, verified, and authorized.
- Existing accounts require separate role/reactivation/password operations.
- Expired, revoked, replaced, or consumed invitations cannot be accepted.
- Account creation, role assignment, and token consumption are atomic.
- Concurrent acceptance must not produce duplicate accounts.
- Acceptance proves control of the invited email, marks it verified, and
  activates the account with the administrator-selected roles.
- Administrator invitations are privileged, never preselected, and require
  recent password confirmation and audit.
- Deliver invitation notifications after commit. Delivery failure never
  creates an active account.

### Account state and revocation

Use explicit active/inactive state and deactivation actor, time, and reason.
Active staff require at least one role.

Only active, verified Administrators manage activation or roles.
Normal staff-management operations cannot alter the actor's own roles or
activation state. Sensitive changes require recent password confirmation.

Preserve at least one active, verified Administrator after initial bootstrap.
Protect deactivation, role removal, email changes that revoke verification,
and any future deletion operation. Concurrent changes must preserve the invariant.

Retain roles on deactivation; review them explicitly before reactivation.
Password reset never reactivates an account or grants roles.

Inactive accounts must be rejected through password, remember-me, passkey,
and two-factor authentication paths and subsequent protected requests/actions.
Invalidate sessions and remember-me credentials; revoke future API tokens.

Role changes apply on subsequent authorized operations; do not rely on stale
session role snapshots. Sensitive operations recheck state transactionally.
An already completed request cannot be undone by later revocation.

### Privileged input and auditing

Reject privileged fields submitted through profile, registration, or invitation
acceptance. Use dedicated authorized operations for roles, activation,
verification state, and assignments. Internal operations enforce allowed inputs.

Audit bootstrap/recovery, invitations, roles, activation/deactivation,
assignment changes, and review decisions as those capabilities are implemented.

Record actor or console origin, target, operation, time, relevant before/after
values, and required reasons. Exclude passwords, hashes, raw tokens, recovery
codes, and full request bodies.

Required successful-change audit records belong in the same transaction.
Audit failure prevents the sensitive change from succeeding.
Denied privileged actions and failed authentication use limited security logs.

No application interface edits/deletes audit history. This is application-level
accountability, not tamper-proof storage against database administrators.
Retention and external audit storage remain future decisions.

### Verification boundaries

No authorization implementation or tests were executed during Task 05.
Future tasks require approved unit/feature coverage for allowed/denied access,
role combinations, escalation, provisioning, invitations, and revocation.

SQLite test configuration remains present, but runtime isolation was not
verified after Task 04. Obtain approval and verify isolation before running
persistence-dependent tests. No dedicated PostgreSQL integration suite.

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
