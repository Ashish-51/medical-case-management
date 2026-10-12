<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('cases.index') }}" class="text-xs text-indigo-600 hover:underline flex items-center gap-1 mb-1">
                    &larr; Back to Case Directory
                </a>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ $isEdit ? __('Edit Implant Case') . ' #' . $case->case_number : __('Create New Implant Case') }}
                </h2>
            </div>
            @if($isEdit)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                    {{ $case->status === 'Approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                    {{ $case->status === 'Completed' ? 'bg-cyan-100 text-cyan-800' : '' }}
                    {{ $case->status === 'In Review' ? 'bg-purple-100 text-purple-800' : '' }}
                    {{ $case->status === 'Planning' ? 'bg-amber-100 text-amber-800' : '' }}
                    {{ $case->status === 'Draft' ? 'bg-gray-100 text-gray-800' : '' }}
                    {{ $case->status === 'Cancelled' ? 'bg-rose-100 text-rose-800' : '' }}
                ">
                    Status: {{ $case->status }}
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 sm:p-8">

                    <form method="POST" action="{{ $isEdit ? route('cases.update', $case) : route('cases.store') }}" class="space-y-6">
                        @csrf
                        @if($isEdit)
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Case Number -->
                            <div>
                                <label for="case_number" class="block text-sm font-semibold text-gray-700">
                                    Case Number <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="case_number" name="case_number"
                                    value="{{ old('case_number', $case->case_number) }}"
                                    placeholder="e.g. CASE-0001"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('case_number') border-rose-500 text-rose-900 @enderror">
                                @error('case_number')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Patient Reference (dummy code, not real name) -->
                            <div>
                                <label for="patient_reference" class="block text-sm font-semibold text-gray-700">
                                    Patient Reference <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="patient_reference" name="patient_reference"
                                    value="{{ old('patient_reference', $case->patient_reference) }}"
                                    placeholder="e.g. PAT-1098"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('patient_reference') border-rose-500 text-rose-900 @enderror">
                                <span class="text-xs text-gray-400">Use dummy code only, do not enter real patient names.</span>
                                @error('patient_reference')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Surgeon Name -->
                            <div>
                                <label for="surgeon_name" class="block text-sm font-semibold text-gray-700">
                                    Surgeon Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="surgeon_name" name="surgeon_name"
                                    value="{{ old('surgeon_name', $case->surgeon_name) }}"
                                    placeholder="e.g. Dr. Eleanor Vance"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('surgeon_name') border-rose-500 text-rose-900 @enderror">
                                @error('surgeon_name')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Implant Type -->
                            <div>
                                <label for="implant_type" class="block text-sm font-semibold text-gray-700">
                                    Implant Type <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="implant_type" name="implant_type"
                                    value="{{ old('implant_type', $case->implant_type) }}"
                                    placeholder="e.g. Titanium Cranial Plate, PEEK Mesh"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('implant_type') border-rose-500 text-rose-900 @enderror">
                                @error('implant_type')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-semibold text-gray-700">
                                    Status <span class="text-rose-500">*</span>
                                </label>
                                <select id="status" name="status"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-rose-500 text-rose-900 @enderror">
                                    <option value="">Select Status</option>
                                    @foreach(App\Models\MedicalCase::STATUSES as $statusOption)
                                        <option value="{{ $statusOption }}" {{ old('status', $case->status) === $statusOption ? 'selected' : '' }}>
                                            {{ $statusOption }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div>
                                <label for="priority" class="block text-sm font-semibold text-gray-700">
                                    Priority <span class="text-rose-500">*</span>
                                </label>
                                <select id="priority" name="priority"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('priority') border-rose-500 text-rose-900 @enderror">
                                    <option value="">Select Priority</option>
                                    @foreach(App\Models\MedicalCase::PRIORITIES as $priorityOption)
                                        <option value="{{ $priorityOption }}" {{ old('priority', $case->priority) === $priorityOption ? 'selected' : '' }}>
                                            {{ $priorityOption }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Surgery Date -->
                        <div>
                            <label for="surgery_date" class="block text-sm font-semibold text-gray-700">
                                Surgery Date <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="date" id="surgery_date" name="surgery_date"
                                value="{{ old('surgery_date', $case->surgery_date ? $case->surgery_date->format('Y-m-d') : '') }}"
                                class="mt-1 block w-full sm:w-1/2 rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('surgery_date') border-rose-500 text-rose-900 @enderror">
                            @error('surgery_date')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    {{ $message }}
                                </p>
                            @enderror

                            <!-- Bonus Task: Public Holidays Integration Display -->
                            @if(!empty($holidays))
                                <div class="mt-3 p-3 bg-blue-50/70 border border-blue-100 rounded-lg text-xs text-blue-900">
                                    <div class="flex items-center gap-1 font-semibold text-blue-800 mb-1">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span>Upcoming Public Holidays (via Nager.Date API):</span>
                                    </div>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        @foreach($holidays as $h)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[11px]">
                                                {{ $h['name'] ?? $h['localName'] }}: {{ $h['date'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Form Actions -->
                        <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                            <a href="{{ $isEdit ? route('cases.show', $case) : route('cases.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                                {{ $isEdit ? 'Update Case' : 'Save & Register Case' }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
