<div class="mb-4">
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

        {{-- Job Role --}}
        <div>
            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">
                Job Role
            </label>
            <select
                wire:model.live="filter_job_role"
                class="fi-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                       text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500
                       dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-white/50">
                <option value="">All</option>
                @foreach($this->getJobRoleOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Job ID --}}
        <div>
            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">
                Job ID
            </label>
            <input
                type="text"
                placeholder="e.g. JOB101"
                wire:model.live.debounce.500ms="filter_job_id"
                class="fi-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                       text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500
                       dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-white/50" />
        </div>

        {{-- Job Posted (Applied On) --}}
        <div>
            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">
                Job Posted (Applied On)
            </label>
            <div class="flex gap-2">
                <input
                    type="date"
                    wire:model.live="filter_posted.from"
                    class="fi-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                           text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500
                           dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-white/50" />
                <input
                    type="date"
                    wire:model.live="filter_posted.to"
                    class="fi-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                           text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500
                           dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-white/50" />
            </div>
        </div>

    </div>
</div>
