<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Implant Cases Directory') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Manage and monitor surgical cases and manufacturing workflow</p>
            </div>
            @can('create', App\Models\MedicalCase::class)
                <a href="{{ route('cases.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create New Case
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-md text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Search and Filter Bar -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('cases.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search input -->
                    <div>
                        <label for="search" class="block text-xs font-semibold text-gray-600 uppercase mb-1">Search Case / Surgeon</label>
                        <div class="relative">
                            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="CASE-0001 or Dr. Smith..." class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-9">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-xs font-semibold text-gray-600 uppercase mb-1">Filter by Status</label>
                        <select id="status" name="status" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Statuses</option>
                            @foreach(App\Models\MedicalCase::STATUSES as $st)
                                <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority Filter (Bonus) -->
                    <div>
                        <label for="priority" class="block text-xs font-semibold text-gray-600 uppercase mb-1">Filter by Priority</label>
                        <select id="priority" name="priority" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Priorities</option>
                            @foreach(App\Models\MedicalCase::PRIORITIES as $pr)
                                <option value="{{ $pr }}" {{ request('priority') === $pr ? 'selected' : '' }}>{{ $pr }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-gray-900 hover:bg-black text-white text-sm font-semibold py-2 px-4 rounded-lg shadow-sm transition">
                            Apply Filters
                        </button>
                        @if(request()->hasAny(['search', 'status', 'priority', 'sort']))
                            <a href="{{ route('cases.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition" title="Clear Filters">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                                @php
                                    $currentSort = request('sort', 'created_at');
                                    $currentDir = request('direction', 'desc');
                                    $toggleDir = $currentDir === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <th class="py-3.5 px-6">
                                    <a href="{{ route('cases.index', array_merge(request()->query(), ['sort' => 'case_number', 'direction' => $currentSort === 'case_number' ? $toggleDir : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-900">
                                        Case Number
                                        @if($currentSort === 'case_number')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="py-3.5 px-6">Patient Reference</th>
                                <th class="py-3.5 px-6">
                                    <a href="{{ route('cases.index', array_merge(request()->query(), ['sort' => 'surgeon_name', 'direction' => $currentSort === 'surgeon_name' ? $toggleDir : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-900">
                                        Surgeon Name
                                        @if($currentSort === 'surgeon_name')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="py-3.5 px-6">Implant Type</th>
                                <th class="py-3.5 px-6">
                                    <a href="{{ route('cases.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => $currentSort === 'status' ? $toggleDir : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-900">
                                        Status
                                        @if($currentSort === 'status')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="py-3.5 px-6">
                                    <a href="{{ route('cases.index', array_merge(request()->query(), ['sort' => 'priority', 'direction' => $currentSort === 'priority' ? $toggleDir : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-900">
                                        Priority
                                        @if($currentSort === 'priority')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="py-3.5 px-6">
                                    <a href="{{ route('cases.index', array_merge(request()->query(), ['sort' => 'surgery_date', 'direction' => $currentSort === 'surgery_date' ? $toggleDir : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-900">
                                        Surgery Date
                                        @if($currentSort === 'surgery_date')
                                            <span>{{ $currentDir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($cases as $case)
                                <tr class="hover:bg-gray-50/75 transition">
                                    <td class="py-3.5 px-6 font-semibold text-gray-900">
                                        <a href="{{ route('cases.show', $case) }}" class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                            {{ $case->case_number }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-6 font-mono text-xs text-gray-600">{{ $case->patient_reference }}</td>
                                    <td class="py-3.5 px-6 font-medium text-gray-800">{{ $case->surgeon_name }}</td>
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
                                    <td class="py-3.5 px-6 text-gray-600 text-xs">
                                        {{ $case->surgery_date ? $case->surgery_date->format('M d, Y') : '—' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <!-- View Button (All roles) -->
                                            <a href="{{ route('cases.show', $case) }}" class="p-1.5 text-gray-600 hover:text-indigo-600 rounded-md hover:bg-gray-100 transition" title="View details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>

                                            <!-- Edit Button (Admin & Engineer only) -->
                                            @can('update', $case)
                                                <a href="{{ route('cases.edit', $case) }}" class="p-1.5 text-amber-600 hover:text-amber-800 rounded-md hover:bg-amber-50 transition" title="Edit case">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                            @endcan

                                            <!-- Delete Button (Admin only) -->
                                            @can('delete', $case)
                                                <form action="{{ route('cases.destroy', $case) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete case #{{ $case->case_number }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-rose-600 hover:text-rose-800 rounded-md hover:bg-rose-50 transition" title="Delete case">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 px-6 text-center text-gray-500">
                                        <p class="text-base font-semibold">No implant cases matched your query.</p>
                                        <p class="text-xs text-gray-400 mt-1">Try adjusting your search criteria or clear active filters.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (10 per page) -->
                @if($cases->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $cases->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
