<?php

namespace App\Http\Requests;

use App\Models\MedicalCase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMedicalCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $case = $this->route('medical_case') ?? $this->route('case') ?? $this->route('id');
        if (is_numeric($case)) {
            $case = MedicalCase::find($case);
        }

        return $this->user() && $case && $this->user()->can('update', $case);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $case = $this->route('medical_case') ?? $this->route('case') ?? $this->route('id');
        $caseId = is_object($case) ? $case->id : $case;

        return [
            'case_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('cases', 'case_number')->ignore($caseId),
            ],
            'patient_reference' => ['required', 'string', 'max:100'],
            'surgeon_name' => ['required', 'string', 'max:150'],
            'implant_type' => ['required', 'string', 'max:150'],
            'status' => ['required', 'string', Rule::in(MedicalCase::STATUSES)],
            'priority' => ['required', 'string', Rule::in(MedicalCase::PRIORITIES)],
            'surgery_date' => ['nullable', 'date'],
        ];
    }
}
