@extends('queue-inspector::layouts.app')

@section('content')

{{-- Page Header --}}
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-500 text-sm mt-1">
        Overview of your Laravel database queue
    </p>
</div>

{{-- ================================================ --}}
{{-- STATS CARDS --}}
{{-- ================================================ --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-10">

    @include('queue-inspector::partials.stats-card', [
    'label' => 'Pending Jobs',
    'value' => $stats['total_pending'],
    'icon' => '⏳',
    'color' => 'blue',
    ])

    @include('queue-inspector::partials.stats-card', [
    'label' => 'Failed Jobs',
    'value' => $stats['total_failed'],
    'icon' => '❌',
    'color' => 'red',
    ])

    @include('queue-inspector::partials.stats-card', [
    'label' => 'Successful Jobs',
    'value' => $stats['total_success'],
    'icon' => '✅',
    'color' => 'green',
    ])

    @include('queue-inspector::partials.stats-card', [
    'label' => 'Avg Execution Time',
    'value' => $stats['avg_execution_time']
    ? $stats['avg_execution_time'].' ms'
    : 'No data yet',
    'icon' => '⚡',
    'color' => 'green',
    ])

    @include('queue-inspector::partials.stats-card', [
    'label' => 'Top Failing Job',
    'value' => $stats['top_failing_job']
    ? $stats['top_failing_job']->job_class
    : 'None',
    'icon' => '🔥',
    'color' => 'orange',
    ])

</div>

{{-- ================================================ --}}
{{-- QUICK ACTIONS --}}
{{-- ================================================ --}}
<div class="bg-white border border-gray-200 rounded-xl p-6 mb-8">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>

    <div class="flex flex-wrap gap-3">

        {{-- Retry All Failed Jobs --}}
        <form method="POST"
            action="{{ route('queue-inspector.jobs.retry-all') }}">
            @csrf
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white
                               text-sm font-medium px-4 py-2 rounded-lg
                               transition-colors"
                onclick="return confirm('Retry all failed jobs?')">
                🔄 Retry All Failed
            </button>
        </form>

        {{-- Clear All Failed Jobs --}}
        <form method="POST"
            action="{{ route('queue-inspector.jobs.clear-all') }}">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white
                               text-sm font-medium px-4 py-2 rounded-lg
                               transition-colors"
                onclick="return confirm('Delete ALL failed jobs? This cannot be undone!')">
                🗑 Clear All Failed
            </button>
        </form>

        {{-- View Pending --}}
        <a href="{{ route('queue-inspector.jobs.pending') }}"
            class="bg-gray-100 hover:bg-gray-200 text-gray-700
                      text-sm font-medium px-4 py-2 rounded-lg
                      transition-colors">
            📋 View Pending Jobs
        </a>

        {{-- View Failed --}}
        <a href="{{ route('queue-inspector.jobs.failed') }}"
            class="bg-gray-100 hover:bg-gray-200 text-gray-700
                      text-sm font-medium px-4 py-2 rounded-lg
                      transition-colors">
            💀 View Failed Jobs
        </a>

    </div>
</div>

{{-- ================================================ --}}
{{-- AVAILABLE QUEUES --}}
{{-- ================================================ --}}
@if($stats['available_queues']->isNotEmpty())
<div class="bg-white border border-gray-200 rounded-xl p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        Active Queues
    </h2>
    <div class="flex flex-wrap gap-2">
        @foreach($stats['available_queues'] as $queue)
        <span class="bg-blue-100 text-blue-700 text-sm
                                 font-medium px-3 py-1 rounded-full">
            {{ $queue }}
        </span>
        @endforeach
    </div>
</div>
@endif

@endsection