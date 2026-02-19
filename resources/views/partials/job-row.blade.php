<tr class="hover:bg-gray-50 transition-colors">

    {{-- ID --}}
    <td class="px-4 py-3 text-gray-500 font-mono">
        #{{ $job->id }}
    </td>

    {{-- Job Class --}}
    <td class="px-4 py-3">
        <span class="bg-blue-100 text-blue-700 font-medium
                     px-2 py-1 rounded text-xs">
            {{ $job->job_class }}
        </span>
    </td>

    {{-- Queue --}}
    <td class="px-4 py-3">
        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
            {{ $job->queue }}
        </span>
    </td>

    {{-- Attempts --}}
    <td class="px-4 py-3">
        <span class="{{ $job->attempts > 0
                        ? 'text-orange-600 font-semibold'
                        : 'text-gray-500' }}">
            {{ $job->attempts }}
        </span>
    </td>

    {{-- Available At --}}
    <td class="px-4 py-3 text-gray-500 text-xs">
        {{ $job->available_at
            ? \Carbon\Carbon::createFromTimestamp($job->available_at)
                            ->format('Y-m-d H:i:s')
            : '—' }}
    </td>

    {{-- Created At --}}
    <td class="px-4 py-3 text-gray-500 text-xs">
        {{ $job->created_at
            ? \Carbon\Carbon::createFromTimestamp($job->created_at)
                            ->format('Y-m-d H:i:s')
            : '—' }}
    </td>

    {{-- Payload Toggle --}}
    <td class="px-4 py-3">
        <button onclick="togglePayload('payload-{{ $job->id }}')"
                class="text-xs text-blue-600 hover:text-blue-800
                       font-medium underline">
            View
        </button>
    </td>

</tr>

{{-- Collapsible Payload Row --}}
<tr id="payload-{{ $job->id }}" class="hidden bg-gray-50">
    <td colspan="7" class="px-4 py-4">
        <div class="text-xs font-semibold text-gray-500 mb-2">
            Raw Payload:
        </div>
        <pre class="bg-gray-900 text-green-400 rounded-lg p-4
                    text-xs overflow-x-auto max-h-64 overflow-y-auto
                    whitespace-pre-wrap">{{ json_encode($job->decoded_payload, JSON_PRETTY_PRINT) }}</pre>
    </td>
</tr>

{{-- JavaScript for toggle --}}
@once
<script>
    function togglePayload(id) {
        const row = document.getElementById(id);
        row.classList.toggle('hidden');
    }
</script>
@endonce