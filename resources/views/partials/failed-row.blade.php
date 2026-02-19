<div class="bg-white border border-red-200 rounded-xl overflow-hidden">

    {{-- Job Header --}}
    <div class="flex items-center justify-between px-6 py-4
                bg-red-50 border-b border-red-200">
        <div class="flex items-center gap-3">
            <span class="text-red-500 text-lg">❌</span>
            <div>
                <span class="font-semibold text-gray-800">
                    {{ $job->job_class }}
                </span>
                <span class="text-gray-400 text-xs ml-2">
                    #{{ $job->id }}
                </span>
            </div>
            <span class="bg-gray-100 text-gray-600
                         px-2 py-1 rounded text-xs">
                {{ $job->queue }}
            </span>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">

            {{-- Retry Button --}}
            <form method="POST"
                  action="{{ route('queue-inspector.jobs.retry', $job->id) }}">
                @csrf
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white
                               text-xs font-medium px-3 py-1.5 rounded-lg
                               transition-colors">
                    🔄 Retry
                </button>
            </form>

            {{-- Delete Button --}}
            <form method="POST"
                  action="{{ route('queue-inspector.jobs.destroy', $job->id) }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white
                               text-xs font-medium px-3 py-1.5 rounded-lg
                               transition-colors"
                        onclick="return confirm('Delete this job?')">
                    🗑 Delete
                </button>
            </form>

        </div>
    </div>

    {{-- Job Details --}}
    <div class="px-6 py-4 grid grid-cols-2 gap-4 text-sm border-b
                border-gray-100">
        <div>
            <span class="text-gray-400 text-xs">Failed At</span>
            <p class="font-medium text-gray-700 mt-0.5">
                {{ \Carbon\Carbon::parse($job->failed_at)
                                 ->format('Y-m-d H:i:s') }}
            </p>
        </div>
        <div>
            <span class="text-gray-400 text-xs">Connection</span>
            <p class="font-medium text-gray-700 mt-0.5">
                {{ $job->connection ?? 'database' }}
            </p>
        </div>
    </div>

    {{-- Exception Message --}}
    @php
        $payload = json_decode($job->payload, true);
        // Exception is stored inside the payload JSON
        $exceptionMessage = $payload['data']['exception'] ?? $job->exception ?? null;
        // Get just the first line — the actual error message
        $firstLine = $exceptionMessage
                        ? strtok($exceptionMessage, "\n")
                        : 'No exception message available';
    @endphp

    <div class="px-6 py-4 border-b border-gray-100">
        <p class="text-xs font-medium text-gray-400 mb-2">
            Exception Message
        </p>
        <p class="text-red-600 text-sm font-medium bg-red-50
                  rounded-lg px-3 py-2">
            {{ $firstLine }}
        </p>
    </div>

    {{-- Stack Trace Toggle --}}
    <div class="px-6 py-3">
        <button onclick="toggleTrace('trace-{{ $job->id }}')"
                class="text-xs text-blue-600 hover:text-blue-800
                       font-medium underline">
            View Stack Trace & Payload
        </button>
    </div>

    {{-- Collapsible Stack Trace --}}
    <div id="trace-{{ $job->id }}" class="hidden px-6 pb-4">
        <p class="text-xs font-medium text-gray-400 mb-2">Stack Trace</p>
        <pre class="bg-gray-900 text-green-400 rounded-lg p-4 text-xs
                    overflow-x-auto max-h-64 overflow-y-auto
                    whitespace-pre-wrap">{{ $job->exception }}</pre>

        <p class="text-xs font-medium text-gray-400 mb-2 mt-4">
            Raw Payload
        </p>
        <pre class="bg-gray-900 text-green-400 rounded-lg p-4 text-xs
                    overflow-x-auto max-h-64 overflow-y-auto
                    whitespace-pre-wrap">{{ json_encode($job->decoded_payload, JSON_PRETTY_PRINT) }}</pre>
    </div>

</div>

@once
<script>
    function toggleTrace(id) {
        const el = document.getElementById(id);
        el.classList.toggle('hidden');
    }
</script>
@endonce
