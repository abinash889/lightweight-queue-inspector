@extends('queue-inspector::layouts.app')

@section('content')

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Failed Jobs</h1>
            <p class="text-gray-500 text-sm mt-1">
                Jobs that failed during processing
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="bg-red-100 text-red-700 font-semibold
                         px-4 py-2 rounded-full text-sm">
                {{ $jobs->total() }} failed
            </span>

            {{-- Bulk Actions --}}
            @if($jobs->isNotEmpty())
                <form method="POST"
                      action="{{ route('queue-inspector.jobs.retry-all') }}">
                    @csrf
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white
                                   text-sm font-medium px-3 py-2 rounded-lg"
                            onclick="return confirm('Retry all failed jobs?')">
                        🔄 Retry All
                    </button>
                </form>

                <form method="POST"
                      action="{{ route('queue-inspector.jobs.clear-all') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white
                                   text-sm font-medium px-3 py-2 rounded-lg"
                            onclick="return confirm('Delete ALL failed jobs?')">
                        🗑 Clear All
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET"
          action="{{ route('queue-inspector.jobs.failed') }}"
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
            <a href="{{ route('queue-inspector.jobs.failed') }}"
               class="text-sm text-gray-500 hover:text-gray-700 py-2">
                Clear filters
            </a>
        @endif

    </form>

    {{-- Failed Jobs List --}}
    @if($jobs->isEmpty())
        <div class="bg-white border border-gray-200 rounded-xl
                    p-16 text-center">
            <div class="text-5xl mb-4">🎉</div>
            <p class="text-gray-500 font-medium">No failed jobs!</p>
            <p class="text-gray-400 text-sm mt-1">
                Everything is running smoothly.
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($jobs as $job)
                @include('queue-inspector::partials.failed-row',
                         ['job' => $job])
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $jobs->links() }}
        </div>
    @endif

@endsection