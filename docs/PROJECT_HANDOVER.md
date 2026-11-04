# FieldOps — Project Handover

## Current milestone and task

Identity/access foundation. Tasks 01–09 complete with user approval.
Task 09 invitation-only provisioning was implemented by the developer.
On 2026-11-04 the user approved manual-only closeout with the stated gaps and
authorized continuity updates. TASK_LOG.md preserves historical evidence.
Next: Task 10 authorization coverage and controlled console recovery; agree its
scope and verification approach before implementation.

## Collaboration and verification boundary

User writes application code and executes commands. Assistant mentors/reviews;
this authorization covers documentation only. No assistant application changes,
installs, migrations, staging, commits or pushes. Batch meaningful checks/reuse
unchanged evidence. Task 09: no automated tests/new tests/separate php -l or
test-runtime isolation check. Source review/static analysis are not workflow tests.
Agree future scope/isolation before database tests. PostgreSQL-only actions cannot
run unchanged on SQLite; no dedicated PostgreSQL integration suite introduced.

## Repository and runtime

Active C:\Sites\FieldOps; backend/ Laravel/Inertia; WSL /mnt/c/Sites/FieldOps.
No mobile app/operational domain modules. Windows/Herd web/Composer/frontend;
Ubuntu/WSL Redis/future Linux workers; Docker optional.
Herd http://fieldops.test -> backend/ PHP8.5. Use herd php/herd composer there.
Recorded, not freshly audited Task 09: Laravel13.39.0/PHP8.5.10, Composer2.11.1,
Node22.23.4/npm10.9.9, Vue3.5.44/TS5.9.3, Inertia Laravel3.6.0/frontend3.10.0,
Vite+1.4.0; WSL PHP8.5.4/Redis8.10.1. Existing MailDev2.2.1 used.

Origin https://github.com/WeeRG91/fieldOps.git. Task 08 committed at edc9cc9
(feat: add staff administration with safeguards and access revocation).
Current feat/staff-invitations tracks origin/feat/staff-invitations.
Task 09 staged/unstaged/untracked; several AM entries contain older staged
scaffolds plus fuller working versions. Do not commit partial index as-is.
project-commands.txt untracked local notes. No Task 09 commit/merge claimed.
Earlier Git snapshots are historical.

## Database, mail and retained fixtures

PostgreSQL18.6 localhost5432 fieldops_dev/fieldops_app, target verified before
migrations/fixture writes. Dedicated owner without superuser/CREATEDB/CREATEROLE/
replication/BYPASSRLS. Three migrations applied successfully:
- 2026_11_02_230327_create_staff_invitations_table.php.
- 2026_11_03_062457_create_staff_invitation_roles_table.php.
- 2026_11_03_063154_add_normalized_email_unique_index_to_users_table.php.
Normalized-user-email collision query returned empty collection first.
Eleven migrations accounted for from prior eight plus three applied; no redundant
final migrate:status/reset claimed. Sessions/cache/queues remain database-backed.

MailDev SMTP127.0.0.1:1025/UI http://127.0.0.1:1080. User changed ignored .env and
.env.example to SMTP, null credentials, fieldops@example.test / FieldOps.
Invitation notification forces SMTP instead of log transport. Harmless SMTP
capture and actual invitations captured; external delivery/failure not verified.
Existing MailDev start command (separate PowerShell terminal, active Node runtime):
node "C:\Users\ACER\.config\herd\bin\nvm\v22.16.0\node_modules\maildev\bin\maildev" --ip 127.0.0.1 --web-ip 127.0.0.1
No project mail dependency installed.

Retain fixture/history, no deletion or cleanup:
- Primary1 existing Administrator issued Task 09 invitations; no role/state changes
  requested. Prior active/verified state and admin UI use, not new final SQL join.
- User2 fieldops.manual@example.test remains INACTIVE per Task 08; last Dispatcher,
  deactivated_by1, reason 'Task 08 manual verification complete'.
- User3 Invitation Staff, fieldops.invitation@example.test, active/verified,
  Dispatcher true/Administrator false. Invitation1 accepted, linked to3.
  One acceptance audit inspected: user actor3/subject3, invited_by1, dispatcher.
- fieldops.resend@example.test: original replaced, replacement revoked via UI;
  exact IDs not independently queried; never accepted.
- Invitation4 fieldops.expiry@example.test: fixture created_at moved to now-49h,
  expires_at = created_at+48h, status expired and link rejection verified.
  Artificial adjustment leaves original issuance audit unchanged.
- fieldops.validation@example.test: Technician pending at verification, duplicate
  issue rejected and tampered link rejected. No acceptance/revocation requested;
  subsequent expiry depends on time.
No passwords/full bearer links/token hashes in documentation.

## Existing identity foundation

Tasks07–08 remain: eligible staff admission, closed public registration/self-delete,
name-only profile, multiple explicit roles, no global Administrator bypass.
Keep profile email changes blocked. First-admin bootstrap already used; refuses ANY
Administrator assignment, not routine setup or recovery.

UserPolicy active verified admin directory/manage others; no self role/state writes.
ManageStaffAccess owns PostgreSQL READ COMMITTED, shared try-advisory lock(71001,1),
ordered actor/subject row locks, fresh authorization, mutation/revocation/audit,
final usable-admin check. Real changes rotate remember_token/delete stored database
sessions on same connection. Direct SQL/helpers bypass cooperating safeguards;
in-flight requests/session rewrite races unsolved. Task 08 log preserves evidence.
No audit UI/retention/database-owner tamper resistance; controlled recovery future.

## Task 09 architecture and policy

Active verified Administrators list/issue/resend/revoke. Email trim/lowercase;
no existing account, even inactive. Explicit nonempty distinct enum roles, none
preselected. Administrator grants additionally require explicit confirmation and
recent password; supported by code, not manually exercised.

Token: random_bytes(32), 64 hex characters, 256-bit entropy; SHA-256 persisted,
hash_equals comparison. Sensitive in-memory DTO/native SensitiveParameterValue;
debug/JSON omit/redact secret. Raw token only needed for delivery/recipient request,
not props/audit/flash. Defense in depth, not proof against arbitrary logging.

staff_invitations: normalized email, unique hash, inviter, expiry, acceptance/
revocation pairs, supersession, TZ timestamps. staff_invitation_roles composite
key/constrained enum/restrict FK. Checks: email/hash shape, expiry after creation,
paired terminal fields, exclusive accepted/revoked/superseded. Partial unique email
for unterminated records; expired open rows still occupy index until replaced.
users_normalized_email_unique on LOWER(BTRIM(email)), original index retained.
Status: accepted/revoked/replaced, otherwise expired or pending. Exactly48h lifetime.
Resend creates fresh record/token/48h, supersedes old, preserves email/roles,
current actor becomes issuer. Expired/revoked re-invite allowed if no user; no
silent existing-user reset/reactivation/role grant. Not all branches exercised.

ManageStaffInvitations and AcceptStaffInvitation own top-level PostgreSQL READ
COMMITTED and share lock(71001,1) with bootstrap/staff access; reject nesting/other
drivers. Reload actor/issuer under lock, user row before invitation. Acceptance
rechecks token/lifecycle/expiry/issuer authority/roles/account collision, hashes
password before lock, creates active verified user+stored roles, consumes invitation,
writes success audit, commits. Unique violation converted after rollback to generic
unavailable. No simultaneous acceptance/forced rollback evidence.

Preview validates before private data, reserves nothing. Guest-only acceptance:
name/password/confirmation/token; specified privileged fields missing; min12 mixed
case/number/symbol password. Bearer acceptance counts as verified email ownership.
Redirect login with status, no automatic login or additional verification email.
Audits staff.invitation_issued / staff.invitation_resent / staff.invitation_revoked /
staff.invitation_accepted share mutation transaction. First three use admin actor,
null user subject and invitation metadata; accepted uses new user actor/subject.
Only acceptance audit independently inspected.

SendStaffInvitation synchronous AFTER commit. Failure returns warning, keeps pending
record; explicit resend rotates link. Sanitized log event/ID/exception class only.
No outbox/queue; SMTP/database not atomic. Revocation can race email delivery;
acceptance remains authoritative. Local mail/browser memory contain bearer secrets;
production TLS/provider/logging/monitoring remain separate work.

## HTTP and frontend

Admin /staff/invitations index/store/resend/revoke use auth/staff/verified,
read policy, writes authorization before password confirmation, requests and fresh
action checks. Typed binding for writes; expired confirmation returns to GET listing,
requires explicit resubmission.
Public GET /invitations/{invitation}/accept only ID/mustSignOut;
POST /invitations/{invitation}/inspect token-gated private preview;
POST /invitations/{invitation}/accept creation.
Limits GET30/min IP, preview20/min IP, acceptance5/min IP. CSRF retained,
dontFlash invitation_token extends default password exclusions; no-store responses,
no-referrer GET. Signed-in users asked to sign out/reopen; untested branch.

Admin page20/page history, create/resend/revoke, confirmations/errors/toasts/busy
controls; capability auth.canManageInvitations drives sidebar, not authorization.
Public onMounted reads/removes fragment, JSON POST with XSRF cookie; acceptance
useForm dontRemember secrets, clears password/form token after request, local token
kept for retry. Refresh requires reopening email. Fragment removal itself not
separately reported; preview/acceptance passed.
Inertia conflict redirects303/errors and acceptance throttle inline handler;
other clients retain status responses. Task 09 conflict/rate/JSON paths untested.
Shared lib/date.ts formatUtc(en-GB, UTC), both invitation pages and staff details;
controllers convert response dates to UTC. No data migration. Human-readable
display not independently reported; duplicate email error display removed last.

## Files created or modified

Created in backend/:
- app/Models/StaffInvitation.php; app/Policies/StaffInvitationPolicy.php.
- app/Actions/Staff/ManageStaffInvitations.php, SendStaffInvitation.php,
  ValidateStaffInvitation.php, InspectStaffInvitation.php, AcceptStaffInvitation.php.
- app/Data/Staff/IssuedStaffInvitation.php.
- app/Notifications/StaffInvitationNotification.php.
- app/Http/Controllers/Invitations/AcceptStaffInvitationController.php.
- app/Http/Controllers/Staff/StaffInvitationController.php.
- app/Http/Middleware/RequireInvitationPasswordConfirmation.php.
- app/Http/Requests/Invitations/InspectStaffInvitationRequest.php,
  AcceptStaffInvitationRequest.php.
- app/Http/Requests/Staff/IssueStaffInvitationRequest.php,
  ResendStaffInvitationRequest.php, RevokeStaffInvitationRequest.php.
- Three migrations above.
- resources/js/lib/date.ts; resources/js/types/invitations.ts.
- resources/js/pages/auth/AcceptInvitation.vue; staff/invitations/Index.vue.

Modified backend/.env.example, ignored .env, routes/web.php, bootstrap/app.php,
app/Providers/AppServiceProvider.php, app/Http/Middleware/HandleInertiaRequests.php,
resources/js/components/AppSidebar.vue, resources/js/types/auth.ts.
Additional UTC reuse: app/Http/Controllers/Staff/StaffController.php and
resources/js/pages/staff/Show.vue. Wayfinder/assets generated; no new dependencies.
Assistant updated AGENTS and five continuity docs only with user authorization.

## Actual verification and approved gaps

Developer execution, not new assistant test runs:
- Three migrations syntax/Pint passed; applied with reported DONE results.
- Latest PHPStan74 files/no errors, --memory-limit=512M; earlier checkpoints
  57/59/61/63/65/66/69. Latest preceded small UTC response changes.
- Targeted Pint7 HTTP/routing files fixed4 issues; route:list3 public/4 admin correct.
- check:fix completed, lint66 files/no warnings/errors, vue-tsc passed,
  production build3586 modules passed; targeted Inertia middleware Pint1 PASS.
- After date helper changes, developer reported frontend check:fix/types/build
  and two invitation-controller Pint --test all passed; no detailed counts supplied.
- Mail example and final duplicate-error template/import removal occurred after
  that checkpoint; no subsequent check claimed. Extra StaffController UTC change
  source-reviewed, not included in targeted two-controller Pint result.
- No automated tests/new tests/separate php -l/isolation check.

Manual passed: issue/MailDev capture; fixed-email/Dispatcher preview; acceptance
redirect; invited login/dashboard, hidden navigation and both /staff and
/staff/invitations GET403; user3 active/verified/Dispatcher-only persisted,
invitation1 linkage and one audit inspected; consumed link rejection; resend old
link rejected/new correct preview; revoke latest rejection; artificial expiry;
existing-account issue rejection; duplicate pending rejection; valid-format
tampered token rejection. Initial issue user reported expected workflow collectively;
exact password-prompt path not separately described. GET403 is not unauthorized
POST coverage. No invalid-password acceptance test claimed.

Deferred with closeout approval: automated tests/isolation; privileged HTTP payloads,
email/role substitution; SMTP failure; inviter eligibility changes; simultaneous
acceptance/lock contention/forced rollback; Administrator invitation, signed-in
acceptance denial, rate limits/JSON, boundary expiry, case collisions, re-invite
expired/revoked, post-issuance account collision, multi-page pagination, complete
audit/log/secret inspection. Sequential replay rejection is not concurrency proof.
No runtime password-hash or absence-of-plaintext storage/log audit claimed.
Task 08 last-admin failure/remember-me/session-races and inherited auth gaps persist.
Legacy tests/factory assumptions still need revision before blind execution.

## Next recommended task

Task 10 authorization coverage and controlled console recovery. Recover docs/Git;
agree scope/testing, preserve shared lock/last-usable-admin/explicit roles/audit.
No public recovery or self-escalation. Redis client/queues/workers/Horizon/Reverb
remain deferred, as do Sanctum/Expo/mobile/operational domains/CI.
