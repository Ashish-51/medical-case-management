<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalCaseRequest;
use App\Http\Requests\UpdateMedicalCaseRequest;
use App\Models\MedicalCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class CaseController extends Controller
{
    /**
     * Display a dashboard with status counts.
     */
    public function dashboard(): View
    {
        Gate::authorize('viewAny', MedicalCase::class);

        $totalCases = MedicalCase::count();
        $planningCount = MedicalCase::where('status', 'Planning')->count();
        $inReviewCount = MedicalCase::where('status', 'In Review')->count();
        $approvedCount = MedicalCase::where('status', 'Approved')->count();
        $completedCount = MedicalCase::where('status', 'Completed')->count();
        $draftCount = MedicalCase::where('status', 'Draft')->count();
        $cancelledCount = MedicalCase::where('status', 'Cancelled')->count();

        $recentCases = MedicalCase::with('creator')->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalCases',
            'planningCount',
            'inReviewCount',
            'approvedCount',
            'completedCount',
            'draftCount',
            'cancelledCount',
            'recentCases'
        ));
    }

    /**
     * Display a paginated list of cases with search, status & priority filter, and sorting.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', MedicalCase::class);

        $query = MedicalCase::with('creator');

        // Search by Case Number or Surgeon Name
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('surgeon_name', 'like', "%{$search}%");
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Bonus: Filter by Priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        // Bonus: Column sorting
        $sortable = ['case_number', 'surgeon_name', 'status', 'priority', 'surgery_date', 'created_at'];
        $sort = $request->input('sort', 'created_at');
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (in_array($sort, $sortable)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $cases = $query->paginate(10)->withQueryString();

        return view('cases.index', compact('cases'));
    }

    /**
     * Show form for creating a new case.
     */
    public function create(): View
    {
        Gate::authorize('create', MedicalCase::class);

        $case = new MedicalCase();
        $holidays = $this->fetchUpcomingHolidays();

        return view('cases.form', [
            'case' => $case,
            'isEdit' => false,
            'holidays' => $holidays,
        ]);
    }

    /**
     * Store a newly created case in storage.
     */
    public function store(StoreMedicalCaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

        $case = MedicalCase::create($validated);

        return redirect()->route('cases.show', $case)
            ->with('success', "Medical Case #{$case->case_number} was created successfully.");
    }

    /**
     * Display the specified case details.
     */
    public function show(MedicalCase $case): View
    {
        Gate::authorize('view', $case);

        $case->load('creator');

        return view('cases.show', compact('case'));
    }

    /**
     * Show the form for editing the specified case.
     */
    public function edit(MedicalCase $case): View
    {
        Gate::authorize('update', $case);

        $holidays = $this->fetchUpcomingHolidays();

        return view('cases.form', [
            'case' => $case,
            'isEdit' => true,
            'holidays' => $holidays,
        ]);
    }

    /**
     * Update the specified case in storage.
     */
    public function update(UpdateMedicalCaseRequest $request, MedicalCase $case): RedirectResponse
    {
        Gate::authorize('update', $case);

        $case->update($request->validated());

        return redirect()->route('cases.show', $case)
            ->with('success', "Medical Case #{$case->case_number} was updated successfully.");
    }

    /**
     * Remove the specified case from storage.
     * Note: Server blocks non-admin users via CasePolicy.
     */
    public function destroy(MedicalCase $case): RedirectResponse
    {
        Gate::authorize('delete', $case);

        $caseNumber = $case->case_number;
        $case->delete();

        return redirect()->route('cases.index')
            ->with('success', "Case #{$caseNumber} has been permanently deleted.");
    }

    /**
     * Bonus task: Call public API (Nager.Date) with Laravel HTTP client.
     * Includes timeout, error handling, and graceful fallback.
     */
    private function fetchUpcomingHolidays(): array
    {
        $apiUrl = config('services.nager_date.url', env('NAGER_DATE_API_URL', 'https://date.nager.at/api/v3/NextPublicHolidays/US'));

        try {
            $response = Http::timeout(3)->get($apiUrl);
            if ($response->successful()) {
                return array_slice($response->json(), 0, 3);
            }
        } catch (\Throwable $e) {
            // Graceful fallback if offline or API is down
            logger()->warning('Nager.Date public holiday API call failed: ' . $e->getMessage());
        }

        return [];
    }
}
