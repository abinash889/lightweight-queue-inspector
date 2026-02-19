@extends('queue-inspector::layouts.app')

@section('content')

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Successful Jobs
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                Jobs that completed successfully with execution metrics
            </p>
        </div>
        <span class="bg-green-100 text-green-700 font-semibold
                     px-4 py-2 rounded-full text-sm">
            {{ $jobs->total() }} total
        </span>
    </div>

    {{-- Filters --}}
    <form method="GET"
          action="{{ route('queue-inspector.jobs.success') }}"
          class="bg-white border border-gray-200 rounded-xl p-4 mb-6
                 flex flex-wrap gap-4 items-end">

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">
                Queue Name
            </label>
            <input type="text"
                   name="queue"
                   value="{{ request('queue') }}"
                   placeholder="e.g. default"
                   class="border border-gray-300 rounded-lg px-3 py-2
                          text-sm focus:outline-none focus:ring-2
                          focus:ring-green-500 w-48">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">
                Job Class
            </label>
            <input type="text"
                   name="job_class"
                   value="{{ request('job_class') }}"
                   placeholder="e.g. SendEmailJob"
                   class="border border-gray-300 rounded-lg px-3 py-2
                          text-sm focus:outline-none focus:ring-2
                          focus:ring-green-500 w-48">
        </div>

        <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white
                       text-sm font-medium px-4 py-2 rounded-lg">
            Filter
        </button>

        @if(request('queue') || request('job_class'))
            <a href="{{ route('queue-inspector.jobs.success') }}"
               class="text-sm text-gray-500 hover:text-gray-700 py-2">
                Clear filters
            </a>
        @endif

    </form>

    {{-- Jobs Table --}}
    @if($jobs->isEmpty())
        <div class="bg-white border border-gray-200 rounded-xl
                    p-16 text-center">
            <div class="text-5xl mb-4">⏳</div>
            <p class="text-gray-500 font-medium">
                No successful jobs recorded yet.
            </p>
            <p class="text-gray-400 text-sm mt-1">
                Run php artisan queue:work to process jobs
                and see metrics here.
            </p>
        </div>
    @else
        <div class="bg-white border border-gray-200 rounded-xl
                    overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium
                                   text-gray-600">
                            ID
                        </th>
                        <th class="text-left px-4 py-3 font-medium
                                   text-gray-600">
                            Job Class
                        </th>
                        <th class="text-left px-4 py-3 font-medium
                                   text-gray-600">
                            Queue
                        </th>
                        <th class="text-left px-4 py-3 font-medium
                                   text-gray-600">
                            Started At
                        </th>
                        <th class="text-left px-4 py-3 font-medium
                                   text-gray-600">
                            Finished At
                        </th>
                        <th class="text-left px-4 py-3 font-medium
                                   text-gray-600">
                            Execution Time
                        </th>
                        <th class="text-left px-4 py-3 font-medium
                                   text-gray-600">
                            Memory Used
                        </th>
                        <th class="text-left px-4 py-3 font-medium
                                   text-gray-600">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($jobs as $job)
                        <tr class="hover:bg-gray-50 transition-colors">

                            {{-- ID --}}
                            <td class="px-4 py-3 text-gray-500 font-mono">
                                #{{ $job->id }}
                            </td>

                            {{-- Job Class --}}
                            <td class="px-4 py-3">
                                <span class="bg-green-100 text-green-700
                                             font-medium px-2 py-1
                                             rounded text-xs">
                                    {{ $job->job_class }}
                                </span>
                            </td>

                            {{-- Queue --}}
                            <td class="px-4 py-3">
                                <span class="bg-gray-100 text-gray-600
                                             px-2 py-1 rounded text-xs">
                                    {{ $job->queue }}
                                </span>
                            </td>

                            {{-- Started At --}}
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $job->started_at
                                    ? \Carbon\Carbon::parse($job->started_at)
                                                    ->format('Y-m-d H:i:s')
                                    : '—' }}
                            </td>

                            {{-- Finished At --}}
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $job->finished_at
                                    ? \Carbon\Carbon::parse($job->finished_at)
                                                    ->format('Y-m-d H:i:s')
                                    : '—' }}
                            </td>

                            {{-- Execution Time --}}
                            <td class="px-4 py-3">
                                @if($job->execution_time_ms)
                                    <span class="font-medium
                                        {{-- Color code by speed --}}
                                        {{ $job->execution_time_ms < 500
                                            ? 'text-green-600'
                                            : ($job->execution_time_ms < 2000
                                                ? 'text-orange-500'
                                                : 'text-red-600') }}">
                                        {{ number_format($job->execution_time_ms) }} ms
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Memory --}}
                            <td class="px-4 py-3 text-gray-600 text-sm">
                                {{ $job->memory_usage_mb
                                    ? $job->memory_usage_mb.' MB'
                                    : '—' }}
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3">
                                <span class="bg-green-100 text-green-700
                                             text-xs font-semibold px-2
                                             py-1 rounded-full">
                                    ✅ success
                                </span>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $jobs->links() }}
        </div>
    @endif

@endsection