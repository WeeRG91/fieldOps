# FieldOps — Agent Instructions

## Project purpose

FieldOps is a professional learning project: a Field Service Management
platform for one maintenance company serving multiple customers and sites.

The user is the developer. The assistant acts as a senior Laravel engineer,
architecture mentor, and co-development partner.

## Required workflow

For every implementation task:

1. Explain what we are building and why.
2. Explain the important technical concepts.
3. Identify the files involved.
4. Provide detailed implementation instructions and code examples.
5. Let the user write the code.
6. Wait for the user's confirmation before proceeding to the next step.
7. Help troubleshoot errors.
8. Explain how to test the implementation.
9. Review the results and suggest improvements.

Do not create or modify application code without explicit user permission.
Do not install dependencies, initialize applications, commit, or push
without explicit user permission.

Guide the user through continuity documentation one file at a time.
Do not create or modify documentation automatically unless authorized.
Update task logs and handover records after the user's approval.

Do not skip explanations to finish faster.

## Starting a conversation

Before proposing or performing work:

1. Read this file.
2. Inspect the current repository, including Git status when available.
3. Read docs/PROJECT_HANDOVER.md and docs/TASK_LOG.md when present.
4. Read the overview, architecture, roadmap, and other documentation
   relevant to the requested task.
5. Summarize the verified state and identify discrepancies.

If documentation is missing, report that fact. Do not assume planned work
has been implemented.

Proceed in small steps and wait for confirmation between implementation
steps. The user's current instructions take precedence over this file.

## Technical direction

Proposed technology targets:

- Backend: Laravel 13 and PHP 8.5.
- Data and infrastructure: PostgreSQL, Redis, Laravel Horizon,
  Laravel Reverb, Laravel Sanctum, and Laravel Scheduler.
- Web: Vue 3, TypeScript, and Inertia.js.
- Mobile: React Native, Expo, and TypeScript.

Verify framework, package, runtime, and extension compatibility before
installing dependencies. Listed targets are not proof of compatibility.

Local development uses Windows/Herd for web, Composer, and frontend tools;
Ubuntu through WSL runs Redis, Horizon, and Linux workers. Docker is optional.
The active repository is C:\Sites\FieldOps (/mnt/c/Sites/FieldOps in Ubuntu).
Use herd php and herd composer for project-aware Windows PHP selection.
Verify installed tools and PostgreSQL connectivity before setup.
Do not relocate the repository without user approval.

## Architecture

Use a modular monolith with one Laravel backend.

- Keep the Inertia web application inside Laravel.
- Expose a versioned REST API for mobile clients.
- Share application actions and business rules across web and API entry points.
- Use PostgreSQL as the authoritative business data store.
- Use Redis for queues and appropriate caching.
- Use Horizon to manage and monitor Redis-backed queue workers.
- Use Reverb for authorized real-time updates.
- Use Scheduler when a feature requires periodic work.
- Dispatch jobs and events that depend on committed data after commit.
- Enforce authorization on operations and private broadcast channels.

Keep controllers focused on HTTP concerns.
Use explicit application actions for important workflows.
Avoid unnecessary abstractions and infrastructure.

The intended repository organization is:

- backend/: Laravel application and Inertia web application.
- mobile/: React Native and Expo application.
- docs/: project continuity documentation.

Create directories only when needed.

## Testing

Prioritize automated tests for important features and business rules.

- Use unit tests for isolated domain logic and business behavior.
- Use Laravel feature tests for workflows, API endpoints, validation,
  authorization, persistence, and framework integration.
- Use isolated SQLite in-memory databases when persistence is genuinely needed.
- Use mocks and Laravel fakes where appropriate.
- Do not mock away the behavior the test is intended to verify.
- Do not create a dedicated PostgreSQL integration-test suite at this stage.
- Avoid unnecessary database-heavy testing.
- Manually verify important web, mobile, and real-time workflows.

Explain SQLite versus PostgreSQL limitations where relevant.
Faked queues and broadcasts do not verify infrastructure integration.

Never claim a check or test passed unless it was actually executed.
Record actual results, unexecuted checks, deferred tests, and limitations.

## Engineering conventions

- Use English names and consistent business terminology.
- Use Form Requests for HTTP input validation.
- Enforce business invariants in shared business logic.
- Use policies for authorization.
- Enforce explicit status transitions.
- Use transactions for related changes that must succeed together.
- Use migrations, foreign keys, and appropriate database constraints.
- Use API Resources and explicitly selected response fields.
- Use strict TypeScript checking.
- Store instants consistently in UTC and convert for display.
- Keep secrets out of Git and document environment variables safely.
- Prefer focused changes and short-lived Git branches.

## Continuity documentation

Maintain these files:

- docs/PROJECT_OVERVIEW.md: business scenario, roles, vocabulary, and scope.
- docs/ARCHITECTURE.md: technical structure and accepted decisions.
- docs/ROADMAP.md: milestones, MVP progression, and deferred features.
- docs/TASK_LOG.md: verified task history and approval status.
- docs/PROJECT_HANDOVER.md: current verified state and next steps.

The handover must include:

- Current milestone and task.
- Completed work.
- Architectural decisions.
- Files created or modified.
- Actual test results.
- Deferred tests.
- Known limitations.
- Next recommended task.

Clearly distinguish proposed, approved, implemented, and verified work.

## Task completion

A task is complete only when:

- Its acceptance criteria are satisfied.
- Required documentation is updated.
- Relevant checks have been performed.
- Limitations and deferred tests are recorded.
- The user has reviewed and approved the task.

End a completed task with a concise, shareable completion report.
## Initialized application baseline — Task 03

Laravel is initialized in backend/; mobile/ has not been created.
Run application commands from C:\Sites\FieldOps\backend using herd php and
herd composer. Herd links http://fieldops.test to backend/ with PHP 8.5.
Windows Node 22.23.4 and npm 10.9.9 satisfy the generated Vite+ 1.4.0 engines.
Do not rely on Task 02's historical Node 22.16.0 compatibility assessment
for Vite+: its requirements differ from standard Vite.

Task 04 configured PostgreSQL fieldops_dev with non-superuser fieldops_app.
Registration, email verification, login/logout, and persistence passed manually.
PHPUnit remains configured for SQLite :memory:; no PostgreSQL integration suite.
The user explicitly skipped automated tests for Task 04. Do not claim they ran
or runtime isolation was verified; confirm test scope before future execution.
Never run destructive database commands without identifying target and approval.
backend/.editorconfig and the Vite+ formatter currently use four spaces;
preserve the scaffold conventions instead of applying root frontend defaults.

Inspect actual scripts before checks. The generated Composer types:check
runs PHPStan, while npm types:check runs vue-tsc. Default PHPStan exhausted
128 MiB; the verified retry is:
herd php vendor/phpstan/phpstan/phpstan analyse --memory-limit=512M
No persistent memory configuration was changed.

Do not use composer run dev blindly: it starts a PHP server and queue worker
in addition to frontend tooling. Herd plus npm run dev is sufficient for the
current web workflow. Optional fontaine and extraneous React observations
remain documented; do not add or remove dependencies without approval.

## Task 06 implemented identity foundation

StaffRole, user_roles persistence, typed User::hasRole()/assignRole(), and
activation metadata/isActive() are implemented. Role assignment is internal
persistence, not authorization. No global Administrator bypass.
Existing/new accounts default inactive; protected login behavior is not yet
changed. Provisioning, active-staff enforcement, revocation and audit remain future.
Audit storage was explicitly deferred outside Task 06.

Developer requested no database-backed automated tests for Task 06 and approved
that exception. No automated tests ran. phpunit.xml has four forced environment
settings; tests/TestCase.php remains unchanged and runtime isolation unverified.
Do not execute persistence-dependent tests without agreed scope and isolation.
Reuse verified results; batch related steps and avoid repeated unchanged checks.
User still writes code and runs installation/migration commands. Verify the actual
target before a database-changing migration; prefer interactive Tinker over
quoted inline commands through Herd's Windows batch launcher.

## Task 07 implemented staff access, bootstrap and audit

Task 07 results and verification limitations were approved on 2026-11-02.
This supersedes Task 06's transitional login behavior. EnsureStaffAccess runs
in optional web mode and through the staff alias. Dashboard/settings require
auth/staff/verified. Ineligible authenticated users are logged out, the current
session invalidated and a generic 403 returned. Logout remains available;
eligible unverified users retain verification access. Password/passkey callbacks
and pending two-factor challenge have eligibility checks. These implementations
are not all manually verified. No global Administrator bypass or complete RBAC.

Public registration and staff self-deletion routes/UI are removed. Profile
writes name only; changed email and privileged role/state fields are rejected.
Keep email changes blocked until last-usable-Administrator safety is implemented.

staff:bootstrap-admin is interactive only, displays the target, confirms existing
promotion/final activation and refuses ANY existing Administrator assignment.
User ID 1 is already active with Administrator; do not rerun as routine setup.
Existing password/name/verification were preserved. No default credentials.
BootstrapAdministrator requires PostgreSQL and uses a transaction-scoped advisory
try-lock (71001, 1), rechecks first-admin/account conditions, and commits activation,
role and success audit together. This coordinates cooperating bootstrap calls
only; it does not supply general last-admin protection. Recovery remains future.

AuditEvent/audit_events exist; audit migration applied to fieldops_dev /
fieldops_app. One bootstrap event verified after repeat refusal: console actor,
null actor_id, subject 1, promoted, administrator, activation false to true.
Console identifies the invocation channel, not an independently authenticated
human operator. Failures/refusals use sanitized application logs outside the
transaction; rolled-back rows are not durable failure history. No audit UI.

Developer checks: Pint 62 files; PHPStan 47 at 512 MiB; frontend types; vp check
66 formatted / 60 linted; production build all passed. Manual promoted-account
login/dashboard, read-only email, absent deletion, GET /register 404, bootstrap
success/repeat refusal/audit inspection passed. No automated tests or separate
php -l run in Task 07. Inactive/roleless rejection, POST registration, other auth
paths, new-account bootstrap, rollback and concurrency remain unverified.
Test isolation remains unverified; user approved these limitations. Agree future
test scope before execution. Reuse verified results instead of repeating checks.

Git now has commits and origin https://github.com/WeeRG91/fieldOps.git.
Task 07 was subsequently committed/merged to main at 9f06938; its feature branch
was removed. See Task 08 below for current state. Do not stage/commit/reset automatically.

## Task 08 implemented staff administration — 2026-11-02

Complete with user approval of results/gaps and explicit six-file documentation
closeout authorization. User chose MANUAL VERIFICATION ONLY. No automated tests,
new tests, isolation check, migrations or dependencies. No separate php -l run.

UserPolicy permits active verified Administrators to view all existing accounts,
including inactive/roleless users, and manage others. All self role/state changes
denied; no global admin bypass, identity editing, deletion or public creation.
ManageStaffAccess owns top-level PostgreSQL READ COMMITTED transaction, shares
try-advisory lock (71001, 1) with bootstrap, locks actor/subject by ascending ID,
reloads policy authority, mutates/revokes/audits and checks active verified admin
remains. Reject nesting/other databases. SQLite cannot execute it unchanged.
Future invitation/recovery writes must share protocol. Direct SQL/internal role
helpers bypass it. No-op role sets do not audit/revoke. Reactivation explicitly
selects roles/clears current metadata; deactivation reason <=1000, retains roles.

Actual changes rotate remember_token/delete database sessions on SAME connection;
unsupported session configuration throws/rolls back. Running requests/session
rewrite races not solved. No Sanctum/Reverb infrastructure. Password confirmation
middleware authorizes before Fortify timestamp check; expired browser writes
return through confirmation to GET details, requiring resubmission. Inertia staff
lock conflicts redirect 303 with inline form errors; normal 409 for other clients
by implementation. No audit UI/retention/tamper-resistance system.

Passed manually: admin directory/details; Dispatcher/Technician read denials and
hidden link; role changes/session logout; deactivation/fresh-login rejection;
reactivation/multiple roles/admin fixture access; self-change action denial;
invalid/duplicate/empty roles; no-op audit count. First three mutation audits
inspected, array cast verified. Tinker-held lock blocked separate browser mutation;
release allowed retry. Inline conflict preserved selection/saved state. This is
limited lock-contention evidence, not exhaustive concurrent staff mutations.

Primary user 1 active/verified/Administrator. Fixture user 2,
fieldops.manual@example.test, retained INACTIVE, deactivated_by=1 with reason
'Task 08 manual verification complete'; history retained. Last confirmed roles
Dispatcher. Hidden metadata verified through console query builder.
Unverified: final last-admin failure branch, forced rollback, remember-me cookies,
in-flight races, HTTP privileged payloads, multi-page pagination and inherited
auth gaps. Latest PHPStan 54/no errors at 512 MiB; full Pint checkpoint 69 passed,
later changed PHP files targeted Pint passed. Frontend format/lint/build/types
passed by developer report, no counts supplied.

Current feat/staff-administration, latest commit 9f06938, staged/unstaged/untracked
Task 08 work; no assistant Git mutation. Next Task 09 invitation-only provisioning
and atomic acceptance/audit per Task 05, honoring shared lock and explicit scope.

## Task 09 invitation provisioning — approved 2026-11-04

Task 09 implemented by developer; manual results/gaps approved and continuity
updates explicitly authorized. This supersedes older "invitations future" and
Task 08 Git-state statements. Current feat/staff-invitations tracks origin;
Task 08 commit edc9cc9. Task 09 mixed AM/staged/unstaged/untracked; do not commit old
staged scaffolds. project-commands.txt local untracked. No assistant Git mutation.

StaffInvitation/policy, role relation, three applied PostgreSQL migrations,
Manage/Send/Validate/Inspect/Accept actions, sensitive delivery DTO/notification,
requests/controllers/password-confirmation middleware, routes and Vue pages exist.
Only active verified Admins manage invitations; no existing account, normalized
fixed email and explicit roles; Administrator additionally confirmed/recent password.
48h SHA256-hashed 256-bit random tokens, resend supersedes/fresh record+issuer,
revocation/expiry/consumption reject. Acceptance guest-only active+verified new
user with stored roles+consume+audit in one transaction; redirects login.

Invitation writes share PostgreSQL advisory try-lock(71001,1), READ COMMITTED,
fresh issuer/actor and row locking with staff/bootstrap; require own transaction,
reject SQLite/nesting. Preserve protocol for recovery. Token fragment becomes
POST body, not props/querystring; dontFlash/dontRemember protect secrets.
No plaintext token logs/audit intended; absence was not exhaustively inspected.
MailDev existing local SMTP127.0.0.1:1025/UI1080; notification forces SMTP,
synchronous after commit, warning retains record on failure. No atomic email/DB,
outbox, queue, production delivery or failure verification. .env.example now SMTP.
Shared resources/js/lib/date.ts formatUtc reused by invitations/staff details.

Developer checks: PHPStan74/no errors512MiB (before later UTC response tweaks),
targeted Pint7 fixed4; frontend lint66/types/build3586 passed, Inertia middleware
Pint1. Later date-helper frontend checks/two invitation-controller Pint passed by
report. Final duplicate-error deletion/mail example edits after quality checkpoint,
no repeat claimed; separate syntax run only migration checkpoint, not full Task 09.
No automated tests/new tests/runtime isolation. User approved manual-only closeout
and deferred payloads, SMTP failure, issuer changes, concurrency/rollback, other
coverage listed in handover; do not run DB tests without agreed scope/isolation.

Manual: issue/local capture/preview/accept/login; Dispatcher user3 active verified,
Adminfalse; hidden nav and /staff,/staff/invitations403; consumed/resend/revoked/
expired/tampered links rejected; existing user/duplicate issue rejected. Accepted
invitation1/user3 and one success audit inspected (actor3/subject3/inviter1).
No unauthorized POST, simultaneous acceptance or forced rollback claim.
Retain user2inactive; user3 fieldops.invitation@example.test activeDispatcher;
resend fixture replaced/revoked, invitation4 artificially expired (49h-old creation,
48h lifetime; original issuance audit unchanged); validation fixture unaccepted/
pending at verification. No cleanup requested. Primary1 not role/state modified.
Task 10 recommended coverage/controlled console recovery; infrastructure still later.
