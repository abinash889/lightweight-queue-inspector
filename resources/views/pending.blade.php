@extends('queue-inspector::layouts.app')

@section('content')

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pending Jobs</h1>
            <p class="text-gray-500 text-sm mt-1">
                Jobs waiting to be processed
            </p>
        </div>
        <span class="bg-blue-100 text-blue-700 font-semibold
                     px-4 py-2 rounded-full text-sm">
            {{ $jobs->total() }} total
        </span>
    </div>

    {{-- ================================================ --}}
    {{-- FILTERS --}}
    {{-- ================================================ --}}
    <form method="GET"
          action="{{ route('queue-inspector.jobs.pending') }}"
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
                          focus:ring-blue-500 w-48">
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
                          focus:ring-blue-500 w-48">
        </div>

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white
                       text-sm font-medium px-4 py-2 rounded-lg">
            Filter
        </button>

        @if(request('queue') || request('job_class'))
            <a href="{{ route('queue-inspector.jobs.pending') }}"
               class="text-sm text-gray-500 hover:text-gray-700 py-2">
                Clear filters
            </a>
        @endif

    </form>

    {{-- ================================================ --}}
    {{-- JOBS TABLE --}}
    {{-- ================================================ --}}
    @if($jobs->isEmpty())
        <div class="bg-white border border-gray-200 rounded-xl p-16 text-center">
            <div class="text-5xl mb-4">✅</div>
            <p class="text-gray-500 font-medium">No pending jobs found.</p>
            <p class="text-gray-400 text-sm mt-1">
                Your queue is empty!
            </p>
        </div>
    @else
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">ID</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Job Class</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Queue</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Attempts</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Available At</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Created At</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Payload</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($jobs as $job)
                        @include('queue-inspector::partials.job-row', ['job' => $job])
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