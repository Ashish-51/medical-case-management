<?php

namespace Database\Seeders;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@jajalmedical.com'],
            [
                'name' => 'Dr. Admin Lead',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // 2. Create Viewer User
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@jajalmedical.com'],
            [
                'name' => 'Nurse Viewer Staff',
                'password' => Hash::make('password123'),
                'role' => 'viewer',
            ]
        );

        // 3. Create Engineer User (Bonus role)
        $engineer = User::firstOrCreate(
            ['email' => 'engineer@jajalmedical.com'],
            [
                'name' => 'Biomedical Engineer',
                'password' => Hash::make('password123'),
                'role' => 'engineer',
            ]
        );

        // 4. Seed at least 20 dummy cases
        $seedCases = [
            [
                'case_number' => 'CASE-0001',
                'patient_reference' => 'PAT-1082',
                'surgeon_name' => 'Dr. Eleanor Vance',
                'implant_type' => 'Titanium Cranial Plate',
                'status' => 'Approved',
                'priority' => 'High',
                'surgery_date' => now()->addDays(5)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-0002',
                'patient_reference' => 'PAT-2041',
                'surgeon_name' => 'Dr. Marcus Brody',
                'implant_type' => 'Custom Mandibular Implant',
                'status' => 'In Review',
                'priority' => 'Medium',
                'surgery_date' => now()->addDays(12)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-0003',
                'patient_reference' => 'PAT-3390',
                'surgeon_name' => 'Dr. Sarah Connor',
                'implant_type' => 'Spinal Fusion Cage',
                'status' => 'Planning',
                'priority' => 'High',
                'surgery_date' => now()->addDays(20)->format('Y-m-d'),
                'created_by' => $engineer->id,
            ],
            [
                'case_number' => 'CASE-0004',
                'patient_reference' => 'PAT-4112',
                'surgeon_name' => 'Dr. James Wilson',
                'implant_type' => 'Orthopedic Knee Alignment Plate',
                'status' => 'Completed',
                'priority' => 'Low',
                'surgery_date' => now()->subDays(3)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-0005',
                'patient_reference' => 'PAT-5509',
                'surgeon_name' => 'Dr. Robert Chase',
                'implant_type' => 'Patient-Specific PEEK Mesh',
                'status' => 'Draft',
                'priority' => 'Medium',
                'surgery_date' => null,
                'created_by' => $engineer->id,
            ],
        ];

        foreach ($seedCases as $caseData) {
            MedicalCase::updateOrCreate(
                ['case_number' => $caseData['case_number']],
                $caseData
            );
        }

        // Generate remaining up to 25 total dummy cases using Factory
        $remainingCount = 20;
        for ($i = 0; $i < $remainingCount; $i++) {
            $num = str_pad((string) ($i + 6), 4, '0', STR_PAD_LEFT);
            $assignedUser = ($i % 2 === 0) ? $admin : $engineer;

            MedicalCase::updateOrCreate(
                ['case_number' => "CASE-{$num}"],
                [
                    'patient_reference' => 'PAT-' . (6000 + $i),
                    'surgeon_name' => fake()->randomElement(['Dr. Alistair Chen', 'Dr. Meredith Grey', 'Dr. Gregory House', 'Dr. John Watson', 'Dr. Shaun Murphy']),
                    'implant_type' => fake()->randomElement([
                        'Titanium Cranial Plate',
                        'Custom Mandibular Implant',
                        'Patient-Specific PEEK Mesh',
                        'Orthopedic Knee Alignment Plate',
                        'Spinal Fusion Cage',
                        'Bio-Ceramic Hip Joint',
                    ]),
                    'status' => fake()->randomElement(MedicalCase::STATUSES),
                    'priority' => fake()->randomElement(MedicalCase::PRIORITIES),
                    'surgery_date' => fake()->boolean(75) ? now()->addDays(rand(1, 45))->format('Y-m-d') : null,
                    'created_by' => $assignedUser->id,
                ]
            );
        }
    }
}
