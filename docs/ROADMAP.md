# FieldOps — Roadmap

## Status

The user approved the Task 01 outcome on 2026-10-30.
All six continuity documents have been created and reviewed.
Milestone 0 and Task 01 are complete.
Task 02 is complete and was reviewed and approved by the user on 2026-10-31.
Task 02 excluded scaffolding. Task 03 initialization is complete and its results
were approved on 2026-11-01. Task 04 PostgreSQL development and authentication
smoke verification are complete; automated tests were skipped by user request.
Task 04 results and that testing exception were approved on 2026-11-01.

Tasks 01–08 are complete and approved; see the latest progress section below.
Business scope, architecture, and engineering conventions are approved.

The Laravel/Vue starter application exists in backend/; no business modules exist.
This roadmap describes planned work, not completed capabilities.

## Delivery principles

- Deliver small, reviewable increments.
- Explain each implementation step before the developer writes code.
- Verify compatibility before installing dependencies.
- Test important business rules and workflows.
- Record actual checks, deferred tests, and limitations.
- Keep the approved MVP boundaries.
- Introduce infrastructure when an increment needs it.
- Require user review and approval before marking a task complete.

Milestones can span multiple tasks.
Detailed task acceptance criteria are agreed before implementation.

## Milestone 0 — Discovery and continuity

### Task 01

Establish the business scenario, roles, workflows, MVP scope, architecture,
repository conventions, and continuity documentation.

### Acceptance criteria

- Current workspace state has been inspected and explained.
- Business scenario and roles are documented.
- Modules and main workflows are documented.
- MVP scope and future capabilities are distinguished.
- Modular-monolith architecture and WSL direction are documented.
- Repository organization and engineering conventions are documented.
- All six required continuity files exist and have been reviewed.
- Documentation consistency has been checked.
- Actual checks, limitations, and deferred verification are recorded.
- No dependencies have been installed or applications initialized.
- The user has reviewed and approved completion.
- Task log and handover reflect the approved outcome.

## Milestone 1 — Environment and application foundation

### Scope

- Inspect Ubuntu/WSL and installed development tools.
- Verify Laravel, PHP, frontend, mobile, and package compatibility
  before their respective installations.
- Confirm PostgreSQL location and connectivity from WSL.
- Confirm the repository location and editor access.
- Establish Git and safe ignore rules.
- Initialize Laravel with the selected Vue, TypeScript, and Inertia setup.
- Configure local application and test environments.
- Establish formatting and relevant static checks.

Expo initialization and its detailed runtime verification may occur in
Milestone 2 when mobile development begins.

### Evidence of completion

The application starts locally, a database connection is verified, and
the initial generated tests and selected checks have actually been run.

Record installed versions and reproducible setup instructions.
Do not commit secrets.

## Milestone 2 — First complete service workflow

Deliver a small vertical slice: a usable path through the web application,
backend, API, and technician mobile application.

### Scope

- Authentication and the initial three staff roles.
- Policies and record-level authorization.
- Minimal customer and site data needed for a work order.
- Work-order creation, assignment, and a planned visit window.
- Explicit work-order transitions and important change history.
- Technician start, findings, completion report, and submission.
- Dispatcher review, closure, and return for further work.
- Versioned mobile API and response contracts.
- Expo application with secure authentication and assigned-work screens.
- Clear handling of failed saves and expired authentication.

Service-request intake and the full equipment register can follow in
Milestone 3. Work orders in this increment do not require a service request.

### Evidence of completion

A dispatcher can create and assign work through the web application.
The assigned technician can execute and submit it through the mobile app.
The dispatcher can review and close it or return it for further work.

Automated checks cover authorization, allowed transitions, required
completion information, and the main HTTP workflows.
Manual checks cover the complete web-to-mobile workflow.

## Milestone 3 — Remaining MVP business operations

### Scope

- Customer, contact, and site management.
- Basic equipment register.
- Optional work-order equipment association with site consistency rules.
- Service-request recording, assessment, rejection, and cancellation.
- Conversion of a request into at most one work order.
- List-based scheduling, reassignment, and rescheduling.
- Blocker reporting and the agreed continuation workflow.
- Required status and assignment history.

### Evidence of completion

The main workflow in PROJECT_OVERVIEW.md is usable.
Important alternative paths and business invariants are tested.

Define detailed cancellation, blocker, and concurrency rules before
implementing them.

## Milestone 4 — Asynchronous and real-time workflows

### Scope

- Redis-backed queues and Horizon.
- Queued assignment and status notifications.
- Reverb and authorized private channels.
- Web and mobile real-time updates.
- Refresh of authoritative state after reconnecting.
- Job retry and duplicate-processing behavior.
- Scheduler only if an approved feature needs periodic execution.

### Evidence of completion

Actual workers process representative jobs.
Authorized clients receive relevant updates.
Unauthorized subscriptions are rejected.
Reconnect behavior and notification failures are manually checked.

Automated tests verify relevant dispatch, payload, and authorization rules.
Queue and broadcast fakes are not treated as infrastructure verification.

## Milestone 5 — MVP acceptance and hardening

### Scope

- Review all approved MVP capabilities against acceptance criteria.
- Fill meaningful business-rule and workflow test gaps.
- Check validation errors, authorization failures, and empty states.
- Verify important web, mobile, and real-time journeys.
- Review secrets handling, API exposure, and token lifecycle.
- Review relevant query performance and pagination.
- Document local startup, worker processes, and troubleshooting.
- Record remaining limitations and deferred work.

### Evidence of completion

The user has reviewed and accepted the MVP.
Actual automated and manual results are recorded.
Known gaps are explicit.

No dedicated PostgreSQL integration-test suite is required at this stage.
SQLite limitations remain documented.

## Milestone 6 — Delivery automation and deployment

### Scope

- Select hosting and production service topology.
- Establish CI checks and deployment automation.
- Define production configuration and secret management.
- Configure process supervision for long-running services.
- Establish backups, logging, monitoring, and recovery procedures.
- Verify deployment and rollback procedures.

Detailed scope and acceptance criteria will be agreed before this milestone.

## Future capability backlog

Outside the approved MVP:

- Multiple visits and technician teams.
- Photos, attachments, checklists, and customer signatures.
- Preventive maintenance and recurring work generation.
- Calendar views, availability, and skills matching.
- Maps and route optimization.
- Offline mobile operation and synchronization.
- Customer portal.
- Inventory, time tracking, quotations, and invoicing.
- Service contracts and SLA tracking.
- Advanced dashboards, reporting, and exports.
- Multiple independent service companies.
- External integrations and configurable permissions.

Prioritize these after reviewing the working MVP.

## Recommended next task after Task 01

Task 02: inspect and verify the WSL development environment and compatibility.

Begin with read-only checks.
Confirm the repository location and PostgreSQL connectivity.
Guide any required changes step by step with the user's approval.

Do not assume application initialization is automatically part of Task 02.
Agree its exact scope and acceptance criteria at the start of that task.
## Historical recommendation after Task 03 — fulfilled by Task 04

Task 04 proposal: PostgreSQL development configuration and starter authentication
smoke verification. Agree scope first; do not assume database credentials or
permissions exist. Preserve SQLite :memory: tests and defer a dedicated
PostgreSQL integration-test suite. Font optimization, PHPStan memory defaults,
optional peer cleanup, and eventual CI placement remain explicit follow-ups.

## Historical next task proposal after Task 04 — Task 05

Agree identity/authorization design and implementation scope, including staff roles,
account provisioning/registration policy, and test expectations before coding.
Task 04 used manual PostgreSQL smoke checks and intentionally skipped the
automated suite; runtime test isolation still requires verification before
future persistence-dependent tests. Do not add a PostgreSQL integration suite.
## Task 05 — Approved authorization implementation sequence

Task 05 defines the authorization and provisioning design only.
The design is recorded in ARCHITECTURE.md; implementation has not started.

Redis and queue infrastructure are deliberately deferred to a later
foundation task. They are not prerequisites for the initial staff workflow.
Invitation notifications may initially use synchronous delivery after commit.

The following task boundaries are approved planning decisions, not permission
to execute implementation automatically.

### Task 06 — Staff identity foundation

Scope:

- Obtain approval for the intended automated checks.
- Verify test database isolation before persistence-dependent tests.
- Add the fixed StaffRole enum and user_roles assignments.
- Add explicit account-state and deactivation metadata.
- Add minimal sensitive-operation audit storage.

Acceptance:

- Role values and duplicate assignments are constrained.
- Existing accounts receive no implicit roles or elevated privileges.
- Account-state defaults and existing-account migration behavior are explicit.
- Approved focused checks are executed and their actual results recorded.
- No provisioning UI or unrelated domain modules are introduced.

### Task 07 — Controlled access and first-admin bootstrap

Scope:

- Controlled interactive first-Administrator console command.
- Disable public registration routes and links.
- Disable staff self-deletion.
- Enforce active, verified staff access across authentication entry points.
- Preserve an accessible email-verification path during initial bootstrap.

Acceptance:

- Bootstrap assigns roles explicitly and refuses repeat initialization
  when an Administrator role already exists.
- No default credentials or automatic first-user promotion.
- Public registration and staff self-deletion are inaccessible.
- Inactive and roleless accounts cannot use protected staff features.
- Bootstrap and sensitive changes are audited.
- Existing development accounts are handled by an explicit decision.

### Task 08 — Staff administration and access revocation

Scope:

- Protected role and activation operations.
- Last-usable-Administrator safeguards.
- Session and remember-me revocation.
- Minimal staff-management interface.
- Required audit events and recent password confirmation.

Acceptance:

- Unauthorized changes and normal self-directed role/state changes are denied.
- Concurrent operations cannot remove the last usable Administrator.
- Email changes cannot bypass Administrator continuity safeguards.
- Deactivation blocks subsequent protected operations and authentication paths.
- Reactivation includes role review.
- Audit records and sensitive changes succeed or fail together.

### Task 09 — Staff invitations

Scope:

- Issue, resend, revoke, and accept invitations.
- Fixed recipient, explicit roles, 48-hour expiry, and hashed single-use tokens.
- Password setup, email verification, and activation upon acceptance.
- Notification delivery after commit.

Acceptance:

- Wrong, expired, revoked, replaced, or consumed tokens are rejected.
- Recipient and roles cannot be changed through acceptance input.
- Inviter authority is rechecked.
- Existing accounts are not silently modified.
- Concurrent acceptance cannot create duplicate accounts.
- Account creation, roles, and token consumption are atomic.
- Delivery failure does not create active staff access.

### Task 10 — Authorization coverage and recovery

Scope:

- Review combined-role behavior and sensitive response scoping.
- Verify audit access restrictions and revocation coverage.
- Define and implement controlled console recovery.
- Close identified authorization gaps.

Acceptance:

- Allowed and denied operations have approved verification coverage.
- No global Administrator bypass.
- No recovery through public registration or hidden web access.
- No sensitive authentication data in responses or audit records.
- Recovery and sensitive operations are auditable.

Resource-specific policies for customers, equipment, requests, and work orders
will be implemented alongside those modules. Do not create empty domain
structures merely to populate the permission matrix.

### Testing boundary

Task 04 automated tests were skipped at the developer's request.
SQLite :memory: configuration exists, but runtime isolation remains unverified.

Future tasks must agree test scope and verify isolation before executing
persistence-dependent tests. No dedicated PostgreSQL integration-test suite.

### Historical next task recommendation after Task 05

Task 06 — Staff identity foundation (now completed; see status below).

Start by recovering project context and agreeing exact scope, account-state
defaults, existing-account handling, acceptance criteria, and test authorization.
Do not treat this roadmap as approval to run migrations or change accounts.

## Historical checkpoint after Task 06 — 2026-11-02

Tasks 01–06 are complete and approved. Task 06 implements role/activation
persistence only; role and activation enforcement remain future work.
Audit storage was explicitly removed from Task 06 scope and deferred.
The historical Task 06 plan above is retained; this approved scope change supersedes it.

Database-backed automated checks and runtime-isolation verification were waived
for Task 06 by explicit developer direction. No automated tests ran. Manual
PostgreSQL persistence, syntax, Pint (57 files), PHPStan (42 files at 512 MiB),
and existing-login smoke checks passed as recorded in TASK_LOG.md.

Next: Task 07 — Controlled staff access and first-admin bootstrap.
Agree exact scope before coding, including placement of required audit storage,
bootstrap/existing-account handling, verification access, and test expectations.
Do not begin audit-dependent privileged workflows without their required audit
foundation. No Redis/queue/Reverb/Expo or domain features are implicitly authorized.

## Historical checkpoint after Task 07 — 2026-11-02

Tasks 01–07 complete with user approval. Task 07 implemented baseline staff
admission, closed public registration/self-deletion, name-only profile updates,
interactive first-admin bootstrap and transactional audit. Audit storage deferred
from Task 06 is now implemented. User ID 1 was promoted with one success event.

Static/build checks and selected browser/PostgreSQL checks passed; see TASK_LOG.md.
No automated tests ran. User approved gaps including inactive/roleless denial,
other authentication/recovery paths, rollback/new-account/concurrency scenarios
and isolation verification. These are follow-ups, not verified scenarios.

Recommended next Task 08: staff administration for active verified Administrators,
reject self role/state changes, last-usable-Administrator concurrency safeguards,
session/remember-me/token revocation where applicable, and transactional audit
for privileged changes. Agree exact scope and verification before coding.
Keep profile email changes blocked until safety rules exist. Invitations remain
Task 09; broader authorization coverage and controlled recovery Task 10.
Deferred Task 07 checks require agreed scope; no infrastructure implicitly authorized.

## Current progress after Task 08 — 2026-11-02

Tasks 01–08 complete with user approval. Task 08 delivered staff directory/details,
admin policy, no self changes, validated roles/state, PostgreSQL shared locking/
last-usable-admin invariant, transactional audit, supported browser revocation,
password confirmation and inline Inertia conflicts. No migration/dependency/tests.
User selected manual-only verification. Selected browser/direct action/audit and
lock-contention checks passed. PHP/frontend quality passed. Forced rollback,
last-admin failure branch, remember-me/races, HTTP privileged payloads, pagination,
isolation and exhaustive concurrency deferred. Source review is not execution.
Fixture 2 retained inactive; primary 1 active verified Administrator.

Next Task 09: invitation-only provisioning per Task 05: normalized fixed email,
explicit roles, 48-hour hashed single-use token, resend/revoke invalidation,
rechecked inviter authority and atomic acceptance/account/role/audit. Existing
accounts separate; no silent reset/reactivation. Shared lock (71001, 1) for protected
state writes. Agree scope, notification delivery and verification first.
Task 10 coverage/controlled recovery remain. Keep profile email changes blocked
until separate safety flow designed. Existing gaps remain explicit follow-ups.

Redis PHP client, queues/workers, Horizon cross-platform dependencies and Reverb
integration remain planned infrastructure; none installed/configured in Task 08.
Initial notifications may send synchronously after commit. Sanctum/mobile/Expo
and root CI future; no infrastructure install authorization implied.
