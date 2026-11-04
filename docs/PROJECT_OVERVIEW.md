# FieldOps — Project Overview

## Status

The business scenario, MVP scope, architecture, and engineering conventions
were approved during Task 01.

All six continuity documents have been created and reviewed.
The user approved the Task 01 outcome on 2026-10-30.
Task 01 is complete.

Task 02 environment setup is complete. Task 03 initialized the Laravel/Vue
application in backend/; results were approved on 2026-11-01.
Task 04 switched development storage to PostgreSQL and manually verified
starter authentication; results and automated-test skip approved on 2026-11-01.
Task 05 authorization design is approved. Task 06 implemented role and activation
persistence, approved on 2026-11-02 with database-backed tests deferred.
Task 07 controlled staff access, closed public registration/self-deletion and
first-Administrator bootstrap with audit are implemented. Results and verification
limitations were approved on 2026-11-02. Task 08 existing-account administration,
protected role/state changes, audit and supported browser revocation are implemented;
manual results and gaps approved on 2026-11-02. Task 09 invitation-only provisioning
is implemented; manual results/gaps and closeout approved on 2026-11-04. Operational
modules remain future work.
Business scope is unchanged.

## Purpose

FieldOps is a Field Service Management platform for a maintenance company
that inspects, maintains, and repairs equipment at customer sites.

The project is also a professional learning exercise. The developer writes
the code, supported by an assistant acting as an engineering mentor.

## Business scenario

The company currently coordinates service work through spreadsheets,
phone calls, and messages.

FieldOps will centralize:

- Customer and site information.
- Equipment records.
- Reported service needs.
- Work planning and technician assignments.
- Field progress and completion reports.
- Review, closure, and service history.

Example:

A customer reports a failed air-conditioning unit. A dispatcher records
the request, identifies the site and equipment when known, creates a work
order, and assigns a technician.

The technician visits the site, records findings and work performed, and
submits a completion report. The dispatcher reviews the result and closes
the work order or returns it for further work.

## Organizational boundary

The initial platform serves one maintenance company with multiple
customers and sites.

Customers are organizations receiving service. They are not independent
service-company tenants.

Supporting multiple independent service companies is a future capability,
not an MVP requirement.

## User roles

### Administrator

Manages user access and company configuration through the web application.

### Dispatcher / Service manager

Records and assesses requests, creates work orders, schedules work,
assigns technicians, and reviews completion through the web application.

Dispatcher and service manager responsibilities share one initial role.
Separate approval permissions may be introduced later if required.

### Technician

Uses the mobile application to view assigned work, record progress and
findings, report blockers, and submit completion reports.

### Customer contact

A person associated with a customer who reports needs and receives
service information.

Customer contacts do not require platform accounts in the MVP.
Staff record requests on their behalf.

A customer portal is a future capability.

## Business vocabulary

| Term | Meaning |
|---|---|
| Customer | An organization receiving maintenance services |
| Customer contact | A person associated with a customer |
| Site | A physical service location belonging to a customer |
| Equipment | A maintainable asset located at a site |
| Service request | A reported need that requires assessment |
| Work order | An authorized and tracked unit of service work |
| Assignment | The technician currently responsible for a work order |
| Planned visit window | The intended start and end of a service visit |
| Completion report | The technician's account of findings and work performed |
| Completed | The technician has submitted the work for review |
| Closed | The dispatcher has reviewed and accepted the completed work |
| Blocker | A reason the technician cannot continue the work |

These terms describe business concepts. They do not prescribe database
tables or final status names.

## Business modules

- Identity and Access.
- Customers and Sites.
- Equipment.
- Service Requests.
- Work Orders.
- Scheduling and Assignment.
- Field Execution.
- Notifications.

Modules group business responsibilities. Their final code organization
is described in the architecture documentation.

## Main workflow

1. A dispatcher records a service request.
2. The dispatcher assesses the need.
3. The dispatcher creates a work order when work is required.
4. The dispatcher schedules the work and assigns a technician.
5. The technician performs the work and records findings and actions.
6. The technician submits a completion report.
7. The dispatcher reviews the report.
8. The dispatcher closes the work order or returns it for further work.

Alternative paths include:

- Rejecting or cancelling a request before work begins.
- Rescheduling or reassigning a work order.
- Reporting a blocker with a reason.
- Returning completed work for correction.

Detailed status transitions and cancellation rules will be defined before
their implementation.

## MVP scope

### Access

Authenticated access for administrators, dispatchers/service managers,
and technicians, with authorization for individual operations and records.

### Customers, sites, and equipment

Create, view, and update customers, contacts, sites, and basic equipment
records.

Equipment belongs to a site. A work order requires a site but may omit
equipment when the work concerns the location generally or the equipment
has not yet been registered.

### Service requests

Record and assess requests, reject or cancel them, and create a work order
for an accepted need.

One service request produces at most one work order in the MVP.

### Work orders and scheduling

Track the work description, priority, status, site, optional equipment,
assigned technician, planned visit window, and completion report.

Each work order has at most one assigned technician and one planned visit
window at a time.

Reassignment and rescheduling preserve change history.
Initial scheduling uses list-based views.

### Mobile execution

Technicians can:

- View assigned work and its details.
- Start work.
- Record findings and work performed.
- Report a blocker.
- Submit a completion report.

The mobile application requires connectivity to save changes.
Failed saves must be clearly shown, and unsaved changes must not be
presented as saved.

### Review and history

Dispatchers review submitted completion reports and either close the
work order or return it for further work.

Important status and assignment changes record who made the change
and when.

### Notifications

Provide basic in-app assignment and status notifications with real-time
updates.

## Future capabilities

- Multiple visits and technician teams.
- Photos, attachments, checklists, and customer signatures.
- Recurring preventive maintenance.
- Calendar scheduling, availability, and skills matching.
- Maps and route optimization.
- Offline mobile storage, synchronization, and conflict handling.
- Customer portal and customer-submitted requests.
- Parts inventory and time tracking.
- Quotations, invoicing, and service contracts.
- SLA tracking, dashboards, reporting, and exports.
- Multiple independent service companies.
- External integrations and configurable permissions.

## Delivery approach

Build the MVP incrementally.

The first functional milestone should demonstrate a small complete path:
authentication, a work order, technician execution, and dispatcher review.

Remaining MVP capabilities follow in later increments.
The roadmap defines their sequence.

## Quality expectations

Important business rules and workflows require automated tests.
Web, mobile, and real-time workflows also require appropriate manual checks.

Testing must verify authorization, valid state changes, assignment,
completion requirements, and review before closure.

Actual checks, limitations, and deferred tests are recorded in the task
log and project handover.

## Implemented staff onboarding — Task 09, 2026-11-04

An active verified Administrator can invite a new staff member with a fixed email
and explicitly selected roles. No role is preselected. Administrator access needs
extra explicit confirmation and recent password confirmation. Multiple explicit
roles/no inherited Administrator bypass remain the approved model.

Invitations last48h, are single-use and can be revoked. Resend replaces the old
link while retaining email/roles. Existing accounts are managed through staff
administration, never silently reactivated/reset/granted roles by an invitation.
On acceptance the recipient supplies name/password; valid bearer link establishes
email ownership. Account active+verified with invitation roles and success audit
are committed together, then recipient logs in normally. Public registration
remains closed. Local invitation mail capture uses MailDev, not external delivery.

Selected lifecycle/authorization/persistence checks passed manually; atomicity and
concurrency safeguards are implemented but not verified by forced failure or
simultaneous acceptance. No automated Task 09 tests; approved gaps are in TASK_LOG
and handover. Task 09 changes identity onboarding, not the operational MVP scope.
Customers/sites/work orders/API/mobile and real-time operations remain future.
