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
