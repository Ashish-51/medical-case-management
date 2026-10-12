<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalCase extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalCaseFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cases';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'case_number',
        'patient_reference',
        'surgeon_name',
        'implant_type',
        'status',
        'priority',
        'surgery_date',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'surgery_date' => 'date',
        ];
    }

    /**
     * Allowed statuses.
     */
    public const STATUSES = [
        'Draft',
        'Planning',
        'In Review',
        'Approved',
        'Completed',
        'Cancelled',
    ];

    /**
     * Allowed priorities.
     */
    public const PRIORITIES = [
        'Low',
        'Medium',
        'High',
    ];

    /**
     * Get the user who created this case.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
