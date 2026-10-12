<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalCaseRequest;
use App\Http\Requests\UpdateMedicalCaseRequest;
use App\Models\MedicalCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CaseApiController extends Controller
{
    /**
     * List cases (paginated).
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', MedicalCase::class);

        $query = MedicalCase::with('creator:id,name,email,role');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('surgeon_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $sortField = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('order', 'desc') === 'asc' ? 'asc' : 'desc';
        if (in_array($sortField, ['case_number', 'surgeon_name', 'status', 'priority', 'surgery_date', 'created_at'])) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->latest();
        }

        $cases = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $cases,
            'message' => 'Cases retrieved successfully.',
        ], 200);
    }

    /**
     * Store a new case.
     */
    public function store(StoreMedicalCaseRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

        $case = MedicalCase::create($validated);
        $case->load('creator:id,name,email,role');

        return response()->json([
            'success' => true,
            'data' => $case,
            'message' => 'Case created successfully.',
        ], 201);
    }

    /**
     * Display a specific case.
     */
    public function show(string|int $id): JsonResponse
    {
        $case = MedicalCase::with('creator:id,name,email,role')->find($id);

        if (! $case) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Case not found.',
            ], 404);
        }

        Gate::authorize('view', $case);

        return response()->json([
            'success' => true,
            'data' => $case,
            'message' => 'Case details retrieved successfully.',
        ], 200);
    }

    /**
     * Update an existing case.
     */
    public function update(UpdateMedicalCaseRequest $request, string|int $id): JsonResponse
    {
        $case = MedicalCase::find($id);

        if (! $case) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Case not found.',
            ], 404);
        }

        Gate::authorize('update', $case);

        $case->update($request->validated());
        $case->load('creator:id,name,email,role');

        return response()->json([
            'success' => true,
            'data' => $case,
            'message' => 'Case updated successfully.',
        ], 200);
    }

    /**
     * Delete a case.
     */
    public function destroy(Request $request, string|int $id): JsonResponse
    {
        $case = MedicalCase::find($id);

        if (! $case) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Case not found.',
            ], 404);
        }

        Gate::forUser($request->user())->authorize('delete', $case);

        $case->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Case deleted successfully.',
        ], 200);
    }
}
