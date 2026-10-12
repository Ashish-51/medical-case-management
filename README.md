# Medical Case Management System

A robust, modern Medical Case Management Web Application built with **Laravel 11**, **MySQL / SQLite**, **Blade**, **Tailwind CSS**, and **Laravel Sanctum**. 

Developed for tracking patient-specific medical implant cases (from initial draft to clinical approval and surgery completion) with role-based access control, strict server-side policy enforcement, validation form requests, and full REST APIs.

---

## 1. Project Overview & Tech Stack

- **Backend**: Laravel 11.x (PHP 8.2+)
- **Database**: MySQL / SQLite (configured via migrations, seeders, and factories)
- **Frontend / UI**: Laravel Blade, Tailwind CSS, Alpine.js (via Vite)
- **Authentication**: Laravel Breeze (Web sessions) & Laravel Sanctum (API tokens)
- **Authorization**: Laravel Policy (`CasePolicy`)
- **Testing**: PHPUnit 11 (Feature & Unit tests covering 100% of requirements)
- **External Integration**: Nager.Date Public Holidays API via Laravel HTTP Client

---

## 2. User Roles & Permissions

The system enforces three distinct user tiers:

| Role | View Cases | Create / Edit Cases | Change Status | Delete Cases |
|---|---|---|---|---|
| **Admin** | Yes | Yes | Yes | **Yes** |
| **Engineer** *(Bonus)* | Yes | Yes | Yes | **No** |
| **Viewer** | Yes | **No** | **No** | **No** |

> **Security Note**: Delete permissions are strictly enforced on both the UI and the backend controller via `CasePolicy`. Even if a non-admin directly submits a DELETE HTTP request, the server blocks it with a `403 Forbidden` response.

---

## 3. Pre-Seeded Test Credentials

Use these dummy credentials to log into the web application or obtain an API token:

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@jajalmedical.com` | `password123` |
| **Viewer** | `viewer@jajalmedical.com` | `password123` |
| **Engineer** | `engineer@jajalmedical.com` | `password123` |

*(Note: In accordance with project instructions, dummy passwords are kept in this README for evaluation and testing).*

---

## 4. Setup & Installation Instructions

Follow these steps to run the application locally:

### 1. Clone the repository
```bash
git clone <repository_url>
cd <repository_directory>
```

### 2. Install PHP & Node dependencies
```bash
composer install
npm install
```

### 3. Setup environment configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database & Run Migrations + Seeders
By default, the application runs seamlessly out-of-the-box with SQLite, or you can point `.env` to MySQL.
```bash
# Create SQLite database file if it does not exist
touch database/database.sqlite

# Run migrations and seed the initial users & 25 dummy implant cases
php artisan migrate:fresh --seed
```

### 5. Build assets & Start local servers
In terminal 1 (Asset build):
```bash
npm run build
# OR for live development:
npm run dev
```

In terminal 2 (Laravel server):
```bash
php artisan serve
```
Open **[http://127.0.0.1:8000](http://127.0.0.1:8000)** in your browser.

---

## 5. Running Automated Tests

Run the full automated test suite (including the 6 mandatory feature scenarios and bonus edge cases):

```bash
php artisan test
```

To run only the Medical Case Management feature test suite:
```bash
php artisan test --filter=MedicalCaseManagementTest
```

### Test Coverage Highlights:
1. `test_valid_login_succeeds`: Verifies Web and API login token generation.
2. `test_invalid_login_is_rejected`: Verifies rejection of incorrect credentials.
3. `test_guest_cannot_open_case_pages_or_apis`: Verifies 401/redirects for unauthenticated users.
4. `test_admin_can_create_a_case`: Verifies case creation via Web and API.
5. `test_creating_a_case_with_a_duplicate_case_number_fails`: Verifies 422 validation response and unique index enforcement.
6. `test_viewer_cannot_delete_a_case`: Verifies 403 Forbidden on both Web and API when a Viewer attempts deletion.

---

## 6. REST API Endpoints

All API endpoints follow a unified response shape:
```json
{
  "success": true,
  "data": { ... },
  "message": "..."
}
```

### API Table
| Method | Endpoint | Description | Who Can Call | Required Headers |
|---|---|---|---|---|
| `POST` | `/api/login` | Issue Sanctum API token | Anyone | `Accept: application/json` |
| `POST` | `/api/logout` | Revoke current token | Authenticated | `Authorization: Bearer <token>` |
| `GET` | `/api/cases` | Paginated case listing | Admin, Viewer, Engineer | `Authorization: Bearer <token>` |
| `POST` | `/api/cases` | Create new implant case | Admin, Engineer | `Authorization: Bearer <token>` |
| `GET` | `/api/cases/{id}` | Retrieve case details | Admin, Viewer, Engineer | `Authorization: Bearer <token>` |
| `PUT` | `/api/cases/{id}` | Update implant case | Admin, Engineer | `Authorization: Bearer <token>` |
| `DELETE` | `/api/cases/{id}` | Delete case permanently | **Admin Only** | `Authorization: Bearer <token>` |

> A ready-to-import Postman / Thunder Client collection is provided at `docs/api-collection.json`.

---

## 7. Medical Traceability & Cloud Deployment Docs

- **Database Architecture**: Refer to [`docs/database.md`](file:///c:/Users/ashis/Desktop/New%20folder/docs/database.md) for entity schemas, indexes, foreign keys, and ER diagrams.
- **Traceability Matrix**: Refer to [`docs/traceability.md`](file:///c:/Users/ashis/Desktop/New%20folder/docs/traceability.md) linking requirements (REQ-001 through REQ-003) to code and test verifications.
- **AWS Cloud Hosting**: Refer to [`docs/deployment.md`](file:///c:/Users/ashis/Desktop/New%20folder/docs/deployment.md) for the EC2, RDS Multi-AZ, S3, ALB, and KMS topology.

---

## 8. Assumptions & Known Limitations

1. **Synthetic Data**: In adherence with HIPAA / medical compliance standards, no real patient information or protected health information (PHI) is collected or stored. Synthetic codes (`PAT-XXXX`) are used.
2. **Offline Mode for External APIs**: The Nager.Date public holiday API integration has an enforced 3-second timeout and graceful fallback; if offline, the case creation form continues operating uninterrupted.
