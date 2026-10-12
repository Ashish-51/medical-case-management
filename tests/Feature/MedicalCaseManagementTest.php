<?php

namespace Tests\Feature;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalCaseManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Requirement 1: Valid login succeeds.
     */
    public function test_valid_login_succeeds(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@jajalmedical.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        // Web login
        $webResponse = $this->post('/login', [
            'email' => 'admin@jajalmedical.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $webResponse->assertRedirect(route('dashboard', absolute: false));

        // API login
        $apiResponse = $this->postJson('/api/login', [
            'email' => 'admin@jajalmedical.com',
            'password' => 'password123',
        ]);

        $apiResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                ]
            ]);
    }

    /**
     * Requirement 2: Invalid login is rejected.
     */
    public function test_invalid_login_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'viewer@jajalmedical.com',
            'password' => bcrypt('correctpassword'),
            'role' => 'viewer',
        ]);

        // Web invalid login
        $webResponse = $this->from('/login')->post('/login', [
            'email' => 'viewer@jajalmedical.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $webResponse->assertRedirect('/login');
        $webResponse->assertSessionHasErrors('email');

        // API invalid login
        $apiResponse = $this->postJson('/api/login', [
            'email' => 'viewer@jajalmedical.com',
            'password' => 'wrongpassword',
        ]);

        $apiResponse->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid email or password.',
            ]);
    }

    /**
     * Requirement 3: A guest cannot open case pages or APIs (401 / redirect).
     */
    public function test_guest_cannot_open_case_pages_or_apis(): void
    {
        // Web routes should redirect guest to login
        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertRedirect('/login');

        $casesIndexResponse = $this->get('/cases');
        $casesIndexResponse->assertRedirect('/login');

        $caseCreateResponse = $this->get('/cases/create');
        $caseCreateResponse->assertRedirect('/login');

        // API routes should return 401 Unauthenticated
        $apiCasesResponse = $this->getJson('/api/cases');
        $apiCasesResponse->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated.',
            ]);

        $apiCaseCreateResponse = $this->postJson('/api/cases', [
            'case_number' => 'CASE-TEST',
        ]);
        $apiCaseCreateResponse->assertStatus(401);
    }

    /**
     * Requirement 4: Admin can create a case.
     */
    public function test_admin_can_create_a_case(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Web case creation
        $webData = [
            'case_number' => 'CASE-WEB-001',
            'patient_reference' => 'PAT-8811',
            'surgeon_name' => 'Dr. Gregory House',
            'implant_type' => 'Custom Mandibular Implant',
            'status' => 'Planning',
            'priority' => 'High',
            'surgery_date' => '2026-11-20',
        ];

        $webResponse = $this->actingAs($admin)->post('/cases', $webData);
        $webResponse->assertRedirect();
        $this->assertDatabaseHas('cases', [
            'case_number' => 'CASE-WEB-001',
            'created_by' => $admin->id,
        ]);

        // API case creation
        $apiData = [
            'case_number' => 'CASE-API-002',
            'patient_reference' => 'PAT-8822',
            'surgeon_name' => 'Dr. Robert Chase',
            'implant_type' => 'Titanium Cranial Plate',
            'status' => 'In Review',
            'priority' => 'Medium',
            'surgery_date' => '2026-12-05',
        ];

        $token = $admin->createToken('admin-test-token')->plainTextToken;
        $apiResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/cases', $apiData);

        $apiResponse->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Case created successfully.',
            ])
            ->assertJsonPath('data.case_number', 'CASE-API-002');

        $this->assertDatabaseHas('cases', [
            'case_number' => 'CASE-API-002',
            'created_by' => $admin->id,
        ]);
    }

    /**
     * Requirement 5: Creating a case with a duplicate Case Number fails (422).
     */
    public function test_creating_a_case_with_a_duplicate_case_number_fails(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Seed existing case
        MedicalCase::create([
            'case_number' => 'CASE-DUP-001',
            'patient_reference' => 'PAT-0001',
            'surgeon_name' => 'Dr. James Wilson',
            'implant_type' => 'PEEK Mesh',
            'status' => 'Draft',
            'priority' => 'Low',
            'created_by' => $admin->id,
        ]);

        // Try creating duplicate via API
        $token = $admin->createToken('admin-dup-test')->plainTextToken;
        $duplicateData = [
            'case_number' => 'CASE-DUP-001', // Duplicate!
            'patient_reference' => 'PAT-9999',
            'surgeon_name' => 'Dr. Lisa Cuddy',
            'implant_type' => 'Spinal Cage',
            'status' => 'Approved',
            'priority' => 'High',
        ];

        $apiResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/cases', $duplicateData);

        $apiResponse->assertStatus(422)
            ->assertJsonValidationErrors(['case_number'])
            ->assertJson([
                'success' => false,
            ]);

        // Try creating duplicate via Web Form
        $webResponse = $this->actingAs($admin)->post('/cases', $duplicateData);
        $webResponse->assertSessionHasErrors(['case_number']);
    }

    /**
     * Requirement 6: Viewer cannot delete a case (403).
     */
    public function test_viewer_cannot_delete_a_case(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $viewer = User::factory()->create(['role' => 'viewer']);

        $case = MedicalCase::create([
            'case_number' => 'CASE-DEL-001',
            'patient_reference' => 'PAT-4433',
            'surgeon_name' => 'Dr. Eric Foreman',
            'implant_type' => 'Bio-Ceramic Hip Joint',
            'status' => 'Planning',
            'priority' => 'Medium',
            'created_by' => $admin->id,
        ]);

        // Web: Viewer attempts delete -> 403 Forbidden
        $webResponse = $this->actingAs($viewer)->delete("/cases/{$case->id}");
        $webResponse->assertStatus(403);

        // Verify case was NOT deleted
        $this->assertDatabaseHas('cases', [
            'id' => $case->id,
        ]);

        // API: Viewer attempts delete -> 403 Forbidden
        $viewerToken = $viewer->createToken('viewer-test')->plainTextToken;
        $apiResponse = $this->withHeader('Authorization', "Bearer {$viewerToken}")
            ->deleteJson("/api/cases/{$case->id}");

        $apiResponse->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);

        // Verify case is still in DB
        $this->assertDatabaseHas('cases', [
            'id' => $case->id,
        ]);

        // Contrast test: Admin CAN delete the case
        $adminDeleteResponse = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/cases/{$case->id}");

        $adminDeleteResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Case deleted successfully.',
            ]);

        $this->assertDatabaseMissing('cases', [
            'id' => $case->id,
        ]);
    }
}
