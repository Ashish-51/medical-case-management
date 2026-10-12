# Database Schema Documentation

This document describes the database design for the **Jajal Medical Case Management Application**.

---

## 1. Entity Relationship Overview

The application utilizes a relational database structure designed in MySQL / SQLite:
- `users` holds authentication details and system access roles (`admin`, `viewer`, `engineer`).
- `cases` holds patient-specific medical implant cases, linked to the user who authored the entry via `created_by`.
- Standard Laravel support tables (`personal_access_tokens`, `sessions`, `cache`, `jobs`).

```
+------------------------------------+
|               users                |
+------------------------------------+
| id (PK, BIGINT, AUTO_INCREMENT)    |
| name (VARCHAR)                     |
| email (VARCHAR, UNIQUE)            |
| role (VARCHAR: admin/viewer/eng)   |
| password (VARCHAR, HASHED)         |
| remember_token (VARCHAR, NULLABLE) |
| created_at (TIMESTAMP)             |
| updated_at (TIMESTAMP)             |
+------------------------------------+
                  |
                  | 1 (One)
                  |
                  | creates
                  |
                  | N (Many)
                  v
+------------------------------------+
|               cases                |
+------------------------------------+
| id (PK, BIGINT, AUTO_INCREMENT)    |
| case_number (VARCHAR, UNIQUE)      |
| patient_reference (VARCHAR)        |
| surgeon_name (VARCHAR)             |
| implant_type (VARCHAR)             |
| status (VARCHAR, INDEXED)          |
| priority (VARCHAR)                 |
| surgery_date (DATE, NULLABLE)      |
| created_by (FK -> users.id)        |
| created_at (TIMESTAMP)             |
| updated_at (TIMESTAMP)             |
+------------------------------------+
```

---

## 2. Table Specifications

### 2.1 Table: `users`
Stores user credentials, profile information, and role-based permissions.

| Column | Type | Constraints / Attributes | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | Unique identifier for the user. |
| `name` | `VARCHAR(255)` | `NOT NULL` | Full name of the user. |
| `email` | `VARCHAR(255)` | `UNIQUE`, `NOT NULL` | Login email address. |
| `role` | `VARCHAR(50)` | `NOT NULL`, `DEFAULT 'viewer'` | Access control role: `admin`, `viewer`, `engineer`. |
| `email_verified_at` | `TIMESTAMP` | `NULLABLE` | Timestamp when email was verified. |
| `password` | `VARCHAR(255)` | `NOT NULL` | Bcrypt / Argon2 hashed user password. |
| `remember_token` | `VARCHAR(100)` | `NULLABLE` | Web session remember token. |
| `created_at` | `TIMESTAMP` | `NULLABLE` | Timestamp when record was created. |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | Timestamp when record was last modified. |

### 2.2 Table: `cases`
Stores patient-specific implant design and surgical tracking cases.

| Column | Type | Constraints / Attributes | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | Unique auto-generated case record identifier. |
| `case_number` | `VARCHAR(50)` | `UNIQUE`, `NOT NULL` | Human-readable unique identifier (e.g. `CASE-0001`). |
| `patient_reference` | `VARCHAR(100)` | `NOT NULL` | Dummy patient code (e.g. `PAT-1082`, no real PII). |
| `surgeon_name` | `VARCHAR(150)` | `NOT NULL` | Name of the operating surgeon. |
| `implant_type` | `VARCHAR(150)` | `NOT NULL` | Custom medical device type (e.g., Titanium Cranial Plate). |
| `status` | `VARCHAR(50)` | `INDEX`, `NOT NULL` | Workflow status (`Draft`, `Planning`, `In Review`, `Approved`, `Completed`, `Cancelled`). |
| `priority` | `VARCHAR(50)` | `NOT NULL` | Priority urgency level (`Low`, `Medium`, `High`). |
| `surgery_date` | `DATE` | `NULLABLE` | Optional scheduled date of surgical intervention. |
| `created_by` | `BIGINT UNSIGNED` | `FOREIGN KEY -> users.id`, `ON DELETE CASCADE` | ID of the user who registered the case. |
| `created_at` | `TIMESTAMP` | `NULLABLE` | Case record creation timestamp. |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | Case record last updated timestamp. |

---

## 3. Indexes & Constraints

1. **`cases.case_number` (UNIQUE INDEX)**: Enforces business rule REQ-002 preventing duplicate case numbers across the database.
2. **`cases.status` (INDEX)**: Optimizes filtering and aggregated counting across case workflow stages on the dashboard and case list query.
3. **`cases.created_by` (FOREIGN KEY & INDEX)**: Enforces referential integrity linking each case to an existing user record in `users.id`.
