{{--
    Reusable stats card component
    Usage:
    @include('queue-inspector::partials.stats-card', [
        'label' => 'Total Pending',
        'value' => 42,
        'icon'  => '⏳',
        'color' => 'blue'   (blue / red / green / orange)
    ])
--}}

@php
    $colors = [
        'blue'   => 'bg-blue-50 border-blue-200 text-blue-700',
        'red'    => 'bg-red-50 border-red-200 text-red-700',
        'green'  => 'bg-green-50 border-green-200 text-green-700',
        'orange' => 'bg-orange-50 border-orange-200 text-orange-700',
    ];
    $colorClass = $colors[$color ?? 'blue'];
@endphp

<div class="border rounded-xl p-4 {{ $colorClass }}">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-medium opacity-75">{{ $label }}</p>
            <p class="text-2xl font-bold mt-1">{{ $value ?? '—' }}</p>
        </div>
        <div class="text-3xl opacity-60">{{ $icon }}</div>
    </div>
</div>