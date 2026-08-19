# Enterprise Workflow Automation & BPM Platform

A production-grade **Enterprise Workflow Automation & Business Process Management (BPM) Platform** built with **Laravel 12**, **PHP 8.2+**, **MySQL**, **Redis**, **Spatie Laravel-Permission**, and a custom **Light Sky Blue (`#87CEEB`) / Dark Purple (`#4B2E83`) / Cream (`#FAF7EF`)** design system.

---

## 🚀 Key Features

### 🎨 1. Interactive Drag & Drop Canvas & Builder
- **Visual Canvas Assembly**: Drag step nodes onto the canvas to create, rearrange, or delete approval steps.
- **HTML5 Real-Time Reordering**: Drag step cards up or down to reorder step positions in real-time with automated step badge re-indexing.
- **Canvas Persistence**: Deploy updated step chains directly into the live process definition.

### 📝 2. Dynamic Form & Field Builder
- **Custom Schema Definition**: Create dynamic input forms with custom labels, field keys, and field types (**Text Input**, **Number**, **Textarea**, **Dropdown Select**, **Date Picker**).
- **Client & Server Validation**: Enforce required field constraints, numeric limits, and dropdown options per workflow template.

### ⚙️ 3. Catalog Lifecycle & Administrative Management
- **Full CRUD Management**: Super Admins and Department Admins can edit process metadata, SLA durations, department ownership, or soft-delete/archive catalog items.
- **Multi-Category Classification**: Group workflows across 9 enterprise categories:
  - 💳 **Payments, Billing & Expense Claims**
  - 🛍️ **Procurement & Purchasing**
  - 👥 **Human Resources & Onboarding**
  - 📊 **Finance & Accounting**
  - ⚖️ **Legal, Contracts & Compliance**
  - 💻 **IT & Cyber Security Operations**
  - 📈 **Sales, Marketing & Commercial**
  - 🎧 **Customer Service & Support**
  - 🏢 **General Corporate & Operations**

### 🔐 4. Cryptographic SHA-256 Digital Signatures
- **Immutable Approval Verification**: Every task approval generates a cryptographically signed 64-character SHA-256 digest:
  $$\text{SHA-256}\left(\text{UUID} \parallel \text{Signer ID} \parallel \text{Task ID} \parallel \text{Payload JSON} \parallel \text{Timestamp} \parallel \text{APP\_KEY}\right)$$
- **Tamper Protection & Non-Repudiation**: Binds signer identity, IP address, timestamp, and request payload to detect data corruption or unauthorized modifications.

### 📐 5. BPMN 2.0 Import & Export
- **Standardized Process Interchange**: Export workflow definitions into compliant BPMN 2.0 XML files (`.bpmn20.xml`) and import external BPMN XML diagrams directly into active workflow models.

### 📊 6. Immutable Audit Trail Inspector
- **Comprehensive Activity Logging**: Captures entity mutations, user actions, IP addresses, user-agent metadata, and timestamps.
- **Flex Display Controls**: View all logs simultaneously (`per_page=all`) or customize row display limits (**50**, **100**, **250** per page) with total record counters.

### ⚡ 7. SLA Intelligence & Auto-Escalation Engine
- **Deadline Monitoring**: Monitors step execution times against SLA thresholds and auto-escalates overdue tasks to designated escalation handlers.
- **Process Bottleneck Heatmaps**: AI-driven analysis of step durations to identify approval bottlenecks.

### 🛡️ 8. Role-Based Access Control (RBAC) & Multi-Tenancy
- **5 Enterprise Roles**: **Super Admin**, **Department Admin**, **Manager**, **Employee**, and **Auditor**.
- **Multi-Tenant SaaS Support**: Scoped tenant data boundaries.

### 🔌 9. RESTful API V1
- Endpoints for external service integrations (`/api/v1/workflows`, `/api/v1/tasks/approve`).

---

## 📦 Quick Setup Instructions

### 1. Environment Requirements
- **PHP**: `>= 8.2` (PHP 8.2+ recommended)
- **Composer**: `2.x`
- **Node.js**: `>= 18` (NPM `>= 9`)
- **Database**: MySQL 8.0+ or SQLite

---

### 2. Installation Steps

```bash
# 1. Clone Repository & Navigate to directory
git clone https://github.com/Rayan-974/enterprise-bpm-platform.git
cd enterprise-bpm-platform

# 2. Install PHP Dependencies
composer install

# 3. Install NPM Dependencies & Build Production Assets
npm install
npm run build

# 4. Environment Configuration
cp .env.example .env
php artisan key:generate
```

---

### 3. Database Configuration

#### Option A: MySQL (Default Production Setup)
Ensure MySQL is running and set your credentials in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=enterprise_bpm_v2
DB_USERNAME=root
DB_PASSWORD=
```

#### Option B: SQLite (Quickest Local Run)
Set SQLite configuration in `.env`:
```env
DB_CONNECTION=sqlite
DB_DATABASE=e:/project/database/database.sqlite
```

---

### 4. Execute Migrations & Seeders

```bash
php artisan migrate:fresh --seed
```

---

### 5. Run the Application

```bash
# Start Laravel Development Server
php artisan serve
```

Access the application in your browser at: **`http://127.0.0.1:8000`**

---

## 👥 Pre-Seeded Enterprise Demo Accounts

All pre-seeded demo accounts use default password: **`password123`**

| User Email | Role | Department | Default Password |
| :--- | :--- | :--- | :--- |
| `admin@enterprise.com` | Super Admin | IT | `password123` |
| `hr.head@enterprise.com` | Department Admin | HR | `password123` |
| `finance.head@enterprise.com` | Manager | Finance | `password123` |
| `procurement.head@enterprise.com` | Manager | Procurement | `password123` |
| `legal.head@enterprise.com` | Manager | Legal | `password123` |
| `john.doe@enterprise.com` | Employee | IT | `password123` |
| `auditor@enterprise.com` | Auditor | Finance | `password123` |

---

## 🧪 Running Automated Test Suite

Run the full PHPUnit automated feature and unit test suite:

```bash
php artisan test
```

### Test Coverage Highlights:
- ✅ Multi-Tenant SaaS Isolation
- ✅ BPMN 2.0 Export & Import Engine
- ✅ Cryptographic SHA-256 Digital Signature Generation
- ✅ Workflow Versioning (V1 &rarr; V2)
- ✅ AI Bottleneck Optimization Engine
- ✅ SLA Breach & Auto-Escalation Tasks
- ✅ REST API V1 Integration Endpoints

---

## 📄 License
This platform is open-source software licensed under the [MIT License](LICENSE).
