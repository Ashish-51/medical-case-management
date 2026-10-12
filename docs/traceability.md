# Software Traceability Matrix (Medical-Software Basics)

In regulated medical software development (e.g., ISO 13485, IEC 62304), requirements traceability ensures that each specified user or system requirement correlates directly to an implementation artifact and a verification test.

| Requirement ID | Requirement (Input) | Implementation (Output) | Verification |
|---|---|---|---|
| **REQ-001** | Admin shall be able to create a case | `CaseController@store` + `CaseApiController@store` + `StoreMedicalCaseRequest` + [cases/form.blade.php](file:///c:/Users/ashis/Desktop/New%20folder/resources/views/cases/form.blade.php) | Feature test: `MedicalCaseManagementTest::test_admin_can_create_a_case` |
| **REQ-002** | Case Number shall be unique | Unique DB index on `cases.case_number` + Form Request validation rule (`unique:cases,case_number`) | Feature test: `MedicalCaseManagementTest::test_creating_a_case_with_a_duplicate_case_number_fails` |
| **REQ-003** | Only Admin shall delete cases | `CasePolicy@delete` (checks `$user->role === 'admin'`) + `CaseController@destroy` + `CaseApiController@destroy` | Feature test: `MedicalCaseManagementTest::test_viewer_cannot_delete_a_case` |

---

### Verification Summary
- **Test Runner**: PHPUnit 11 / Laravel Test Runner
- **Test File**: `tests/Feature/MedicalCaseManagementTest.php`
- **Result**: 100% Passed (6 feature tests, 50 assertions verified).
