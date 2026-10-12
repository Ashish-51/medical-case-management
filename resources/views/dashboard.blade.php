<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Clinical Case Overview') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Medical Device Implant Case Management & Status Dashboard</p>
            </div>
            @can('create', App\Models\MedicalCase::class)
                <a href="{{ route('cases.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    New Medical Case
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Metrics Grid -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <!-- Total Cases -->
                <div class="col-span-2 md:col-span-1 bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Cases</p>
                            <h3 class="text-3xl font-extrabold text-gray-900 mt-2">{{ $totalCases }}</h3>
                        </div>
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('cases.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                            View All Cases &rarr;
                        </a>
                    </div>
                </div>

                <!-- Planning -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Planning</p>
                            <h3 class="text-3xl font-extrabold text-gray-900 mt-2">{{ $planningCount }}</h3>
                        </div>
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('cases.index', ['status' => 'Planning']) }}" class="text-xs font-medium text-amber-600 hover:text-amber-800 flex items-center gap-1">
                            Filter Planning &rarr;
                        </a>
                    </div>
                </div>

                <!-- In Review -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider">In Review</p>
                            <h3 class="text-3xl font-extrabold text-gray-900 mt-2">{{ $inReviewCount }}</h3>
                        </div>
                        <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('cases.index', ['status' => 'In Review']) }}" class="text-xs font-medium text-purple-600 hover:text-purple-800 flex items-center gap-1">
                            Filter Review &rarr;
                        </a>
                    </div>
                </div>

                <!-- Approved -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Approved</p>
                            <h3 class="text-3xl font-extrabold text-gray-900 mt-2">{{ $approvedCount }}</h3>
                        </div>
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('cases.index', ['status' => 'Approved']) }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
                            Filter Approved &rarr;
                        </a>
                    </div>
                </div>

                <!-- Completed -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-cyan-600 uppercase tracking-wider">Completed</p>
                            <h3 class="text-3xl font-extrabold text-gray-900 mt-2">{{ $completedCount }}</h3>
                        </div>
                        <div class="p-3 bg-cyan-50 text-cyan-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('cases.index', ['status' => 'Completed']) }}" class="text-xs font-medium text-cyan-600 hover:text-cyan-800 flex items-center gap-1">
                            Filter Completed &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Cases Table Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Recent Implant Cases</h3>
                        <p class="text-xs text-gray-500">Latest active records submitted into the system</p>
                    </div>
                    <a href="{{ route('cases.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                        View Table &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                                <th class="py-3.5 px-6">Case Number</th>
                                <th class="py-3.5 px-6">Patient Ref</th>
                                <th class="py-3.5 px-6">Surgeon</th>
                                <th class="py-3.5 px-6">Implant Type</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6">Priority</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($recentCases as $case)
                                <tr class="hover:bg-gray-50/75 transition">
                                    <td class="py-3.5 px-6 font-semibold text-gray-900">
                                        <a href="{{ route('cases.show', $case) }}" class="text-indigo-600 hover:underline">
                                            {{ $case->case_number }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-6 text-gray-600">{{ $case->patient_reference }}</td>
                                    <td class="py-3.5 px-6 text-gray-800 font-medium">{{ $case->surgeon_name }}</td>
                                    <td class="py-3.5 px-6 text-gray-600">{{ $case->implant_type }}</td>
                                    <td class="py-3.5 px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            {{ $case->status === 'Approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $case->status === 'Completed' ? 'bg-cyan-100 text-cyan-800' : '' }}
                                            {{ $case->status === 'In Review' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $case->status === 'Planning' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $case->status === 'Draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $case->status === 'Cancelled' ? 'bg-rose-100 text-rose-800' : '' }}
                                        ">
                                            {{ $case->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                                            {{ $case->priority === 'High' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}
                                            {{ $case->priority === 'Medium' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                            {{ $case->priority === 'Low' ? 'bg-green-50 text-green-700 border border-green-200' : '' }}
                                        ">
                                            {{ $case->priority }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-6 text-right">
                                        <a href="{{ route('cases.show', $case) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">
                                            Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 px-6 text-center text-gray-500 text-sm">
                                        No cases registered yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
