# Enterprise Workflow Automation & BPM Platform

A production-grade **Enterprise Workflow Automation & Business Process Management (BPM) Platform** built with **Laravel 12**, **PHP 8.2+**, **MySQL**, **Redis**, **Spatie Laravel-Permission**, **Chart.js**, and a custom **Light Sky Blue (`#87CEEB`) / Dark Purple (`#4B2E83`) / Cream (`#FAF7EF`)** design system.

---

## 🚀 Key Features

### 📄 1. PDF & Document Attachment Uploads
- **Dynamic File Field Types**: Employees submitting requests (e.g. Procurement Quotes, Medical Certificates, Invoices, Contract Drafts) can upload PDF and image attachments directly within dynamic workflow forms.
- **Secure Storage & Validation**: Handled securely via Laravel storage disks (`storage/app/public/attachments/`) with instant downloadable attachment badges on task review and tracking screens.

### 📋 2. My Requests & Live Process Tracking Hub
- **Dedicated Employee Sidebar Hub**: Navigation item in the left sidebar (**📋 My Requests**) allowing employees to track all submitted workflow requests at any time.
- **Live Progress Steppers**: View real-time step execution timelines, current assignees, SLA due dates, and full approval decision logs.
- **✏️ Edit & 🗑️ Delete/Cancel Capabilities**: Requesters can update form inputs/attachments or delete/cancel pending requests directly from the tracking hub.

### 📊 3. Interactive Analytics & 1-Click CSV Exports
- **Chart.js Visual Dashboards**: Interactive Bar and Doughnut charts displaying Department Approval Performance & SLA Execution Breakdowns on the Analytics screen.
- **1-Click CSV Report Exports**: Instant streaming CSV report generation for **Audit Trail Logs** (`/audit/export/csv`) and **Workflow Performance Metrics** (`/analytics/export/csv`).

### 🎨 4. Interactive Drag & Drop Canvas & Builder
- **Visual Canvas Assembly**: Drag step nodes onto the canvas to create, rearrange, or delete approval steps.
- **HTML5 Real-Time Reordering**: Drag step cards up or down to reorder step positions in real-time with automated step badge re-indexing.
- **Canvas Persistence**: Deploy updated step chains directly into active process definitions.

### 📝 5. Dynamic Form & Field Builder
- **Custom Schema Definition**: Create dynamic input forms with custom labels, field keys, and field types (**Text Input**, **Number**, **Textarea**, **Dropdown Select**, **Date Picker**, **File Attachment**).
- **Client & Server Validation**: Enforce required field constraints, numeric limits, file extensions, and dropdown options per workflow template.

### ⚙️ 6. Catalog Lifecycle & Administrative Management
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

### 🔐 7. Cryptographic SHA-256 Digital Signatures
- **Immutable Approval Verification**: Every task approval generates a cryptographically signed 64-character SHA-256 digest:
  $$\text{SHA-256}\left(\text{UUID} \parallel \text{Signer ID} \parallel \text{Task ID} \parallel \text{Payload JSON} \parallel \text{Timestamp} \parallel \text{APP\_KEY}\right)$$
- **Tamper Protection & Non-Repudiation**: Binds signer identity, IP address, timestamp, and request payload to detect data corruption or unauthorized modifications.

### 📐 8. BPMN 2.0 Import & Export
- **Standardized Process Interchange**: Export workflow definitions into compliant BPMN 2.0 XML files (`.bpmn20.xml`) and import external BPMN XML diagrams directly into active workflow models.

### 📊 9. Immutable Audit Trail Inspector
- **Comprehensive Activity Logging**: Captures entity mutations, user actions, IP addresses, user-agent metadata, and timestamps.
- **Flex Display Controls**: View all logs simultaneously (`per_page=all`) or customize row display limits (**50**, **100**, **250** per page) with total record counters.

### ⚡ 10. SLA Intelligence & Auto-Escalation Engine
- **Deadline Monitoring**: Monitors step execution times against SLA thresholds and auto-escalates overdue tasks to designated escalation handlers.
- **Process Bottleneck Heatmaps**: Automated analytical analysis of step durations to identify approval bottlenecks.

### 🛡️ 11. Role-Based Access Control (RBAC) & Multi-Tenancy
- **5 Enterprise Roles**: **Super Admin**, **Department Admin**, **Manager**, **Employee**, and **Auditor**.
- **Multi-Tenant SaaS Support**: Scoped tenant data boundaries.

### 📱 12. Responsive Mobile Drawer & UI Polish
- **Off-Canvas Slide-In Drawer**: Modern off-canvas mobile navigation drawer with backdrop blur and touch targets (`<768px`).
- **Stacked Mobile Data Cards**: Automatically converts data tables into key-value stacked cards on mobile devices.

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

# 5. Link Public Storage Disk for Attachments
php artisan storage:link
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
- ✅ Process Optimization Recommendations
- ✅ SLA Breach & Auto-Escalation Tasks
- ✅ REST API V1 Integration Endpoints

---

## 📄 License
This platform is open-source software licensed under the [MIT License](LICENSE).
