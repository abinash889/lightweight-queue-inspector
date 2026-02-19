<?php

namespace AbinashBhatta\QueueInspector\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class QueueInspectorAuth
{
    public function handle(Request $request, Closure $next)
    {
        $middleware = config('queue-inspector.middleware', []);

        $hasAuth = collect($middleware)->contains(function ($m) {
            return str_starts_with($m, 'auth');
        });

        if ($hasAuth) {
            $loginRouteExists = collect(\Route::getRoutes())
                ->contains(fn($r) => $r->getName() === 'login');

            if (! $loginRouteExists) {
                return response(
                    view('queue-inspector::auth-setup'),
                    503
                );
            }

            if (! $request->user()) {
                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}