<?php

namespace Database\Factories;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalCase>
 */
class MedicalCaseFactory extends Factory
{
    protected $model = MedicalCase::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 1000;
        $counter++;

        return [
            'case_number' => 'CASE-' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'patient_reference' => 'PAT-' . fake()->unique()->numerify('####'),
            'surgeon_name' => 'Dr. ' . fake()->lastName(),
            'implant_type' => fake()->randomElement([
                'Titanium Cranial Plate',
                'Custom Mandibular Implant',
                'Patient-Specific PEEK Mesh',
                'Orthopedic Knee Alignment Plate',
                'Spinal Fusion Cage',
                'Bio-Ceramic Hip Joint',
                'Zirconia Dental Substructure'
            ]),
            'status' => fake()->randomElement(MedicalCase::STATUSES),
            'priority' => fake()->randomElement(MedicalCase::PRIORITIES),
            'surgery_date' => fake()->boolean(80) ? fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d') : null,
            'created_by' => User::factory(),
        ];
    }
}
