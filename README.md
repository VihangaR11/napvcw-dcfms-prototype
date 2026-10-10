# NAPVCW Digital Case Flow Management System (DCFMS)

A role-based digital case management prototype developed for the **National Authority for the Protection of Victims of Crime and Witnesses (NAPVCW), Sri Lanka**.

The system is designed to support operational case-flow processes by replacing fragmented manual tracking with a centralized, auditable, role-based workflow for case registration, routing, legal processing, protection services, police protection assessment, assistance services, executive decisions, reporting, and case closure.

> **Project status:** Prototype / internal demonstration build  
> **Framework:** Laravel 13  
> **Database:** PostgreSQL 17  
> **Primary focus:** Digital case management, workflow control, reporting, auditability, and role-based access

---

## Project Objectives

The DCFMS prototype was developed to demonstrate how NAPVCW case-handling activities can be digitized while preserving operational responsibilities across divisions.

The main objectives are to:

- centralize case registration and case tracking;
- route cases to the appropriate operational divisions;
- maintain case-specific workflow records;
- support Police Protection threat-assessment workflows;
- support Director General protection decisions;
- maintain case-status history and audit trails;
- provide role-specific dashboards and decision-support statistics;
- generate downloadable case-summary reports;
- support English and Sinhala interfaces and reports;
- support controlled migration of historical case records;
- improve traceability, accountability, and visibility across the case lifecycle.

---

## Operational Scope

The current prototype focuses primarily on:

- **Board Secretariat**
- **Law and Law Enforcement Division**
- **Protection Services Division**
- **Police Protection Division**
- **Assistance Services**
- **Director General oversight and decision-making**
- **System Administration**

The prototype intentionally excludes broader administrative and financial functions that are outside the current case-management scope.

---

## Key Features

### Case Registration

Authorized users can register a new master case with information such as:

- received date;
- complaint source;
- complaint mode;
- complainant details;
- victim/witness type;
- complaint summary;
- complaint category;
- urgency;
- primary division;
- initial case status.

Each case receives a unique case number and becomes part of the central case registry.

### Case Routing

Cases can be routed to one or more operational divisions depending on the nature of the complaint.

Supported operational destinations include:

- Law and Law Enforcement;
- Protection Services;
- Police Protection;
- Assistance Services.

Active assignments are tracked separately from the master case record.

### Law and Law Enforcement Workflow

The legal workflow supports activities such as:

- RE/reference number management;
- Legal Officer assignment;
- Investigation Officer assignment;
- inquiry status;
- observation requests and due dates;
- reminders;
- field visits;
- investigation findings;
- legal recommendations;
- case conferences;
- Board submissions;
- Board decisions;
- remarks and workflow updates.

### Protection Services Workflow

Protection Services can maintain protection-related case information and coordinate with Police Protection and management where necessary.

### Police Protection Workflow

The Police Protection workflow supports:

- threat-assessment case allocation;
- assessment progress;
- threat-assessment findings;
- Director-level threat recommendation;
- threat status classification;
- workflow updates and case history.

Threat classifications currently supported include:

- Very High
- High
- Low
- Very Low

### Director General Protection Decision

Cases requiring executive protection decisions can be surfaced on the Director General dashboard.

The Director General can select a protection type such as:

- Interim Protection
- Body-to-Body Protection
- Close Protection

The system stores:

- selected protection type;
- decision maker;
- decision date/time;
- related threat recommendation.

The decision is also included in the generated case-summary report.

### Assistance Services Workflow

Assistance Services supports recording:

- assistance status;
- assistance type;
- assistance required;
- assistance provided;
- referral details;
- follow-up actions;
- remarks.

Assistance work can be handled collaboratively by authorized officers based on assignment and access rules.

### Case Lifecycle Management

The prototype supports controlled movement through statuses such as:

- Registered
- Routed
- Under Processing
- Awaiting Decision
- Closed

Status changes are recorded in the shared case timeline.

### Case Closure

Authorized users can close a case after required actions are completed.

Once closed:

- the case status is updated to `Closed`;
- the closure timestamp is stored;
- the case registry reflects the closed status;
- closure history remains auditable.

### Case Summary Reports

The system generates case-summary PDF reports containing relevant case information and workflow data.

Current report support includes:

- English case summary;
- Sinhala case summary;
- case metadata;
- division assignments;
- legal information;
- protection information;
- Police Protection assessment;
- Assistance Services information;
- Director General protection decision;
- status history;
- approval information.

### Dashboards

Role-specific dashboards provide relevant workload and decision information.

Examples include:

- assigned cases;
- cases awaiting routing;
- cases awaiting officer assignment;
- threat-assessment work;
- protection decisions;
- active/closed case counts;
- priority cases;
- management statistics.

The Director General dashboard also includes analytical views such as:

- cases by status;
- cases by urgency;
- cases by complaint category;
- cases by primary division;
- cases by threat-assessment status;
- cases by DG protection type;
- time-based filters.

### Audit Logging

Important actions are recorded through the audit-log mechanism.

Audit records can capture:

- action/event;
- related case;
- user;
- previous values;
- new values;
- division/context;
- timestamp.

### User and Role Management

The system supports role-based access using roles such as:

- System Administrator
- Director General
- Board Secretary
- Legal Director
- Legal Officer
- Investigation Officer
- Protection Director
- Protection Officer
- Police Protection Director
- Police Protection Officer
- Chairman
- Policy / Programme oversight roles where configured

Access is enforced through controllers, policies, assignments, and role-specific interfaces.

### English / Sinhala Support

The prototype supports bilingual operation using Laravel language resources.

Current language resources include:

- authentication;
- dashboards;
- case registration;
- case registry;
- case details;
- legal workflow;
- protection workflow;
- Police Protection workflow;
- Assistance Services;
- reports;
- shared interface text.

---

## Historical Data Migration

The project includes a controlled staging approach for historical data migration.

A staging import has been implemented for Law and Law Enforcement **Right and Entitlement** records.

Example command:

```bash
php artisan dcfms:stage-right-entitlement "storage/app/imports/Right And Entitlement.xlsx" --batch=LAW-RE-2026-001
```

The staging process:

1. reads the historical workbook;
2. validates records;
3. flags records requiring manual review;
4. identifies possible duplicate-review cases;
5. stores records in staging tables;
6. does **not** automatically create operational DCFMS cases.

This separation is intentional so historical data can be reviewed before promotion into the live case registry.

---

## Technology Stack

### Backend

- PHP 8.4
- Laravel 13
- Eloquent ORM
- Laravel Blade
- Laravel validation, middleware, policies, migrations, and services

### Database

- PostgreSQL 17

### Frontend

- Blade templates
- Tailwind CSS
- JavaScript
- Vite

### Reporting

- `barryvdh/laravel-dompdf`
- mPDF for Sinhala-compatible PDF rendering where required

### Development Tools

- Composer
- Node.js / npm
- Git / GitHub
- VS Code
- pgAdmin 4
- Laravel Herd / PHP development environment

---

## Project Structure

```text
app/
├── Console/
│   └── Commands/
├── Enums/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── ...
├── Models/
├── Policies/
└── Services/

config/
database/
├── migrations/
└── seeders/

resources/
├── lang/
│   ├── en/
│   └── si/
├── views/
│   ├── admin/
│   ├── assistance/
│   ├── auth/
│   ├── cases/
│   ├── dashboard/
│   ├── legal/
│   ├── police-protection/
│   ├── protection/
│   └── reports/
└── ...

routes/
storage/
tests/
```

---

## Main Domain Models

The prototype currently includes models such as:

- `DcfmsCase`
- `CaseAssignment`
- `CaseStatusHistory`
- `LegalCaseDetail`
- `ProtectionCaseDetail`
- `PoliceProtectionDetail`
- `AssistanceCaseDetail`
- `AuditLog`
- `MigrationBatch`
- `StagingCaseImport`
- `CaseMigrationRecord`
- `User`

---

## Installation

### Prerequisites

Install:

- PHP 8.4+
- Composer
- PostgreSQL 17+
- Node.js
- npm
- Git

### Clone the Repository

```bash
git clone https://github.com/VihangaR11/napvcw-dcfms-prototype.git
cd napvcw-dcfms-prototype
```

### Install PHP Dependencies

```bash
composer install
```

### Install Frontend Dependencies

```bash
npm install
```

### Create Environment File

On Windows:

```bash
copy .env.example .env
```

On Linux/macOS:

```bash
cp .env.example .env
```

### Generate Application Key

```bash
php artisan key:generate
```

### Configure PostgreSQL

Update `.env` with your local database configuration:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=napvcw_dcfms
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

Do not commit `.env` to GitHub.

### Run Migrations

```bash
php artisan migrate
```

If the project uses seed data:

```bash
php artisan db:seed
```

### Build Frontend Assets

Development:

```bash
npm run dev
```

Production build:

```bash
npm run build
```

### Start Laravel

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

---

## Useful Development Commands

Clear Laravel caches:

```bash
php artisan optimize:clear
```

Clear compiled views:

```bash
php artisan view:clear
```

Inspect routes:

```bash
php artisan route:list
```

Run database migrations:

```bash
php artisan migrate
```

Start Tinker:

```bash
php artisan tinker
```

Check PHP syntax of a file:

```bash
php -l app/Http/Controllers/DashboardController.php
```

---

## Data Protection and Repository Safety

This repository must **not** contain real victim, witness, complainant, or operational case data.

The following should remain excluded from source control:

```text
.env
storage/app/imports/
storage/app/private/
storage/app/reports/
storage/app/exports/
database dumps
real Excel migration files
generated case PDFs containing personal information
logs containing sensitive operational data
```

Historical migration workbooks should be stored securely outside the public repository.

The repository should preferably remain **private** while the project is under organizational review and development.

---

## Security Considerations

The prototype includes or is designed around:

- authenticated access;
- role-based authorization;
- assignment-based operational access;
- restricted division access;
- audit logging;
- controlled case status transitions;
- protected configuration through environment variables;
- controlled reporting;
- avoidance of unrestricted document uploads;
- protection of sensitive case data.

Before production deployment, the system should undergo:

- formal security review;
- penetration testing;
- privacy review;
- access-control verification;
- backup/recovery testing;
- logging and monitoring review;
- government infrastructure/security compliance assessment.

---

## Current Prototype Constraints

The current implementation is a prototype and should not yet be treated as a fully production-certified government information system.

Known prototype-level constraints include:

- historical data migration still requires review and controlled promotion;
- some workflows may evolve after further stakeholder validation;
- deployment architecture is still subject to organizational approval;
- formal production security hardening is still required;
- complete UAT and acceptance testing are pending;
- some management analytics may require refinement as more operational data becomes available.

---

## Development Approach

The prototype was developed using a business-analysis and digital-transformation approach:

1. study the existing manual workflows;
2. identify operational stakeholders;
3. model the As-Is processes;
4. define functional and non-functional requirements;
5. design role-based workflows;
6. implement the Laravel prototype;
7. validate workflows incrementally with operational users;
8. refine reporting and dashboards;
9. prepare controlled historical-data migration;
10. prepare the system for demonstration, UAT, and future procurement/development decisions.

---

## Future Enhancements

Potential next steps include:

- controlled promotion of staged historical records;
- improved migration validation and duplicate resolution;
- advanced management analytics;
- SLA / aging indicators;
- notifications and reminders;
- configurable workflow rules;
- enhanced search and filtering;
- stronger audit reporting;
- formal UAT workflows;
- automated test coverage;
- deployment hardening;
- backup and disaster-recovery procedures;
- approved cloud hosting architecture;
- production security and privacy controls.

---

## Repository

GitHub:

```text
https://github.com/VihangaR11/napvcw-dcfms-prototype
```

---

## Author

**Vihanga Rathnayake**  
Digital Transformation Intern  
National Authority for the Protection of Victims of Crime and Witnesses (NAPVCW)

Areas of work:

- Business Analysis
- Requirements Engineering
- Digital Transformation
- Case Management Workflow Design
- Laravel Prototype Development
- Data Migration Planning
- Reporting and Dashboard Design

---

## Disclaimer

This repository represents a prototype developed for digital-transformation analysis, workflow validation, demonstration, and further system-development planning.

It must not be interpreted as an official production deployment or final approved information system of NAPVCW unless formally authorized by the relevant institution.

Sensitive operational information, real case data, and personally identifiable information must not be committed to this repository.
