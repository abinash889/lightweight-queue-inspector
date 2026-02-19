<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Inspector — Setup Required</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="max-w-2xl w-full mx-auto px-6">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">⚡</div>
            <h1 class="text-3xl font-bold text-gray-900">
                Queue Inspector
            </h1>
            <p class="text-gray-500 mt-2">Setup Required</p>
        </div>

        {{-- Warning Card --}}
        <div class="bg-white border-2 border-orange-300 rounded-2xl
                    overflow-hidden shadow-sm">

            {{-- Warning Header --}}
            <div class="bg-orange-50 border-b border-orange-200
                        px-6 py-4 flex items-center gap-3">
                <span class="text-2xl">⚠️</span>
                <div>
                    <h2 class="font-bold text-orange-800">
                        Auth Middleware Configured — But No Login Route Found
                    </h2>
                    <p class="text-orange-600 text-sm mt-0.5">
                        Your app has no authentication system set up yet.
                    </p>
                </div>
            </div>

            {{-- Explanation --}}
            <div class="px-6 py-5 border-b border-gray-100">
                <p class="text-gray-600 text-sm leading-relaxed">
                    You have <code class="bg-gray-100 px-1.5 py-0.5 rounded
                    text-orange-600 font-mono text-xs">'auth'</code>
                    middleware in your
                    <code class="bg-gray-100 px-1.5 py-0.5 rounded
                    text-blue-600 font-mono text-xs">
                        config/queue-inspector.php
                    </code>
                    which is the right thing for production.
                    However, your Laravel app does not have a
                    <code class="bg-gray-100 px-1.5 py-0.5 rounded
                    text-orange-600 font-mono text-xs">login</code>
                    route — so the auth redirect has nowhere to go.
                </p>
            </div>

            {{-- Options --}}
            <div class="px-6 py-5 space-y-6">

                {{-- Option A --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="bg-green-100 text-green-700 text-xs
                                     font-bold px-2 py-1 rounded-full">
                            OPTION A
                        </span>
                        <span class="font-semibold text-gray-800">
                            Set up Laravel authentication (Recommended)
                        </span>
                    </div>
                    <p class="text-gray-500 text-sm mb-3">
                        Install Laravel Breeze to get a full login system
                        with register, login, and logout pages.
                    </p>
                    <div class="bg-gray-900 rounded-xl p-4 space-y-1">
                        <p class="text-green-400 font-mono text-sm">
                            composer require laravel/breeze --dev
                        </p>
                        <p class="text-green-400 font-mono text-sm">
                            php artisan breeze:install
                        </p>
                        <p class="text-green-400 font-mono text-sm">
                            php artisan migrate
                        </p>
                        <p class="text-green-400 font-mono text-sm">
                            npm install && npm run dev
                        </p>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="flex items-center gap-4">
                    <div class="flex-1 border-t border-gray-200"></div>
                    <span class="text-gray-400 text-xs font-medium">OR</span>
                    <div class="flex-1 border-t border-gray-200"></div>
                </div>

                {{-- Option B --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="bg-orange-100 text-orange-700 text-xs
                                     font-bold px-2 py-1 rounded-full">
                            OPTION B
                        </span>
                        <span class="font-semibold text-gray-800">
                            Remove auth for local development only
                        </span>
                    </div>
                    <p class="text-gray-500 text-sm mb-3">
                        Open
                        <code class="bg-gray-100 px-1.5 py-0.5 rounded
                        text-blue-600 font-mono text-xs">
                            config/queue-inspector.php
                        </code>
                        and remove
                        <code class="bg-gray-100 px-1.5 py-0.5 rounded
                        text-orange-600 font-mono text-xs">'auth'</code>
                        from middleware:
                    </p>
                    <div class="bg-gray-900 rounded-xl p-4">
                        <p class="text-gray-500 font-mono text-sm line-through">
                            'middleware' => ['web', 'auth'],
                        </p>
                        <p class="text-green-400 font-mono text-sm">
                            'middleware' => ['web'],
                        </p>
                    </div>
                    <div class="mt-3 bg-red-50 border border-red-200
                                rounded-lg px-4 py-3 flex items-start gap-2">
                        <span class="text-red-500 mt-0.5">⚠</span>
                        <p class="text-red-700 text-xs">
                            <strong>Never do this in production.</strong>
                            Without auth, anyone who knows the URL can view
                            your job data, retry jobs, and delete records.
                        </p>
                    </div>
                </div>

            </div>

        </div>

        {{-- Footer note --}}
        <p class="text-center text-gray-400 text-xs mt-6">
            This message only shows when auth is misconfigured.
            Once fixed, your dashboard will load normally.
        </p>

    </div>

</body>
</html>