<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Inspector</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
</head>

<body class="bg-gray-50 text-gray-900 min-h-screen">

    {{-- ================================================ --}}
    {{-- NAVBAR --}}
    {{-- ================================================ --}}
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">

                {{-- Logo --}}
                <a href="{{ route('queue-inspector.dashboard') }}"
                    class="flex items-center gap-2 text-xl font-bold text-blue-600">
                    ⚡ Queue Inspector
                </a>

                {{-- Navigation Links --}}
                <div class="flex items-center gap-6">
                    <a href="{{ route('queue-inspector.dashboard') }}"
                        class="text-sm font-medium
                       {{ request()->routeIs('queue-inspector.dashboard')
                            ? 'text-blue-600 border-b-2 border-blue-600 pb-1'
                            : 'text-gray-500 hover:text-blue-600' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('queue-inspector.jobs.pending') }}"
                        class="text-sm font-medium
                       {{ request()->routeIs('queue-inspector.jobs.pending')
                            ? 'text-blue-600 border-b-2 border-blue-600 pb-1'
                            : 'text-gray-500 hover:text-blue-600' }}">
                        Pending Jobs
                    </a>
                    <a href="{{ route('queue-inspector.jobs.success') }}"
                        class="text-sm font-medium
       {{ request()->routeIs('queue-inspector.jobs.success')
            ? 'text-green-600 border-b-2 border-green-600 pb-1'
            : 'text-gray-500 hover:text-green-600' }}">
                        Success Jobs
                    </a>

                    <a href="{{ route('queue-inspector.jobs.failed') }}"
                        class="text-sm font-medium
                       {{ request()->routeIs('queue-inspector.jobs.failed')
                            ? 'text-blue-600 border-b-2 border-blue-600 pb-1'
                            : 'text-gray-500 hover:text-blue-600' }}">
                        Failed Jobs
                    </a>
                </div>

            </div>
        </div>
    </nav>

    {{-- ================================================ --}}
    {{-- FLASH MESSAGES --}}
    {{-- ================================================ --}}
    @if(session('success'))
    <div class="max-w-7xl mx-auto px-6 mt-4">
        <div class="bg-green-50 border border-green-200 text-green-800
                        rounded-lg px-4 py-3 flex items-center gap-2">
            <span>✅</span>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="max-w-7xl mx-auto px-6 mt-4">
        <div class="bg-red-50 border border-red-200 text-red-800
                        rounded-lg px-4 py-3 flex items-center gap-2">
            <span>❌</span>
            <span class="text-sm">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    {{-- ================================================ --}}
    {{-- MAIN CONTENT --}}
    {{-- ================================================ --}}
    <main class="max-w-7xl mx-auto px-6 py-8">
        @yield('content')
    </main>

    {{-- ================================================ --}}
    {{-- FOOTER --}}
    {{-- ================================================ --}}
    <footer class="border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <p class="text-xs text-gray-400 text-center">
                Queue Inspector by Abinash Bhatta
            </p>
        </div>
    </footer>

</body>

</html>