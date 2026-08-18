# Enterprise Workflow Automation & BPM Platform

A production-grade Enterprise Workflow Automation & Business Process Management (BPM) Platform built with **Laravel 12**, **PHP 8.3**, **MySQL**, **Redis**, **Spatie Laravel-Permission**, and a custom **Light Sky Blue (`#87CEEB`) / Dark Purple (`#4B2E83`) / Cream (`#FAF7EF`)** design system.

---

## Key Features

- **Workflow Designer & Step Engine:** Sequential, parallel, and conditional step approvals with multi-level escalation.
- **Dynamic Form Builder:** Store field definitions and JSON validation rules dynamically; render forms with client/server validation.
- **Workflow Engine & State Machine:** Robust state machine transitions (`draft` -> `in_progress` -> `approved` / `rejected` / `escalated` -> `completed`).
- **Interactive Drag & Drop Builder:** Visually drag step nodes and assemble process flows.
- **BPMN 2.0 Import & Export:** Export processes to standard BPMN 2.0 XML schema and import BPMN files.
- **Cryptographic Digital Signatures:** SHA-256 digital signature digests stored on task approvals.
- **Multi-Tenant SaaS Support:** Tenant model and tenant scoping.
- **Process Optimization Engine:** Intelligent analysis of step completion times, bottleneck heatmaps, and SLA breach risks.
- **Task Management Center:** Dedicated workspace for pending approvals, completed history, delegated tasks, and overdue items.
- **SLA Intelligence & Auto-Escalation:** Monitors task durations against SLA deadlines and auto-escalates overdue tasks.
- **Immutable Audit Trail:** Comprehensive logging of user actions, entity mutations, IP addresses, and timestamps.
- **Role-Based Access Control (RBAC):** 5 enterprise roles (**Super Admin**, **Department Admin**, **Manager**, **Employee**, **Auditor**) via Spatie Permission.
- **RESTful API V1:** Endpoints for external integrations (`/api/v1/workflows`, `/api/v1/tasks/approve`).

---

## Quick Setup Instructions

### 1. Environment Requirements
- PHP >= 8.2 (PHP 8.3 recommended)
- Composer 2.x
- SQLite / MySQL

### 2. Installation Steps
```bash
# Clone & Navigate to directory
cd project

# Install Dependencies
composer install

# Environment File Setup
cp .env.example .env
php artisan key:generate

# Execute Database Migrations & Seeders
php artisan migrate:fresh --seed
```

### 3. Run Application & Test Suite
```bash
# Execute Test Suite
php artisan test

# Start Local Dev Server
php artisan serve
```

Access the application at `http://127.0.0.1:8000`.

---

## Pre-Seeded Enterprise Demo Users

| User Email | Role | Department | Password |
| :--- | :--- | :--- | :--- |
| `admin@enterprise.com` | Super Admin | IT | `password123` |
| `hr.head@enterprise.com` | Department Admin | HR | `password123` |
| `finance.head@enterprise.com` | Manager | Finance | `password123` |
| `procurement.head@enterprise.com` | Manager | Procurement | `password123` |
| `legal.head@enterprise.com` | Manager | Legal | `password123` |
| `john.doe@enterprise.com` | Employee | IT | `password123` |
| `auditor@enterprise.com` | Auditor | Finance | `password123` |
