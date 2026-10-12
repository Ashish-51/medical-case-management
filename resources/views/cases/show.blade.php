<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('cases.index') }}" class="text-xs text-indigo-600 hover:underline flex items-center gap-1 mb-1">
                    &larr; Back to Case Directory
                </a>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Implant Case Details') }}: <span class="font-mono text-indigo-700">{{ $case->case_number }}</span>
                </h2>
            </div>
            <div class="flex items-center gap-2">
                @can('update', $case)
                    <a href="{{ route('cases.edit', $case) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium text-sm rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Case
                    </a>
                @endcan

                @can('delete', $case)
                    <form action="{{ route('cases.destroy', $case) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete case #{{ $case->case_number }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-medium text-sm rounded-lg shadow-sm transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Delete Case
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-md text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Main Case Summary Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 sm:p-8">

                    <!-- Header status banner -->
                    <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-gray-100">
                        <div>
                            <span class="text-xs uppercase font-semibold text-gray-400">Implant Tracking Unit</span>
                            <h3 class="text-xl font-bold text-gray-900 mt-0.5">{{ $case->implant_type }}</h3>
                        </div>

                        <div class="flex items-center gap-3">
                            <div>
                                <span class="text-xs text-gray-400 block text-right">Current Status</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold mt-0.5
                                    {{ $case->status === 'Approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $case->status === 'Completed' ? 'bg-cyan-100 text-cyan-800' : '' }}
                                    {{ $case->status === 'In Review' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $case->status === 'Planning' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $case->status === 'Draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $case->status === 'Cancelled' ? 'bg-rose-100 text-rose-800' : '' }}
                                ">
                                    {{ $case->status }}
                                </span>
                            </div>

                            <div>
                                <span class="text-xs text-gray-400 block text-right">Priority</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold mt-0.5
                                    {{ $case->priority === 'High' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}
                                    {{ $case->priority === 'Medium' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                    {{ $case->priority === 'Low' ? 'bg-green-50 text-green-700 border border-green-200' : '' }}
                                ">
                                    {{ $case->priority }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Fields Details Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 pt-6">
                        <div>
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Case Number</dt>
                            <dd class="mt-1 text-sm font-semibold font-mono text-gray-900">{{ $case->case_number }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Patient Reference</dt>
                            <dd class="mt-1 text-sm font-semibold font-mono text-gray-900">{{ $case->patient_reference }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Assigned Surgeon</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $case->surgeon_name }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Implant Specification</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $case->implant_type }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Scheduled Surgery Date</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-medium">
                                {{ $case->surgery_date ? $case->surgery_date->format('F d, Y') : 'Not Scheduled' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Registered Record ID</dt>
                            <dd class="mt-1 text-sm font-mono text-gray-600">#{{ $case->id }}</dd>
                        </div>
                    </div>

                    <!-- Audit Trail / Creator & Timestamps Info -->
                    <div class="mt-8 pt-6 border-t border-gray-100 bg-gray-50/70 -mx-6 -mb-6 sm:-mx-8 sm:-mb-8 p-6 sm:p-8">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-4">Record Audit & Authorship</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-xs text-gray-600">
                            <div>
                                <span class="block text-gray-400">Created By</span>
                                <span class="font-medium text-gray-800 text-sm mt-0.5 block">
                                    {{ $case->creator ? $case->creator->name : 'System Generated' }}
                                    @if($case->creator)
                                        <span class="text-xs font-normal text-gray-500">({{ $case->creator->email }})</span>
                                    @endif
                                </span>
                                @if($case->creator)
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] uppercase font-semibold mt-1 {{ $case->creator->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-gray-200 text-gray-700' }}">
                                        {{ $case->creator->role }}
                                    </span>
                                @endif
                            </div>

                            <div>
                                <span class="block text-gray-400">Created At</span>
                                <span class="font-medium text-gray-800 text-sm mt-0.5 block">
                                    {{ $case->created_at->format('M d, Y · H:i:s T') }}
                                </span>
                                <span class="text-gray-400">({{ $case->created_at->diffForHumans() }})</span>
                            </div>

                            <div>
                                <span class="block text-gray-400">Last Updated At</span>
                                <span class="font-medium text-gray-800 text-sm mt-0.5 block">
                                    {{ $case->updated_at->format('M d, Y · H:i:s T') }}
                                </span>
                                <span class="text-gray-400">({{ $case->updated_at->diffForHumans() }})</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
