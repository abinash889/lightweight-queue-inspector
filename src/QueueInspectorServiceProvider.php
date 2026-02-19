<?php

namespace AbinashBhatta\QueueInspector;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use AbinashBhatta\QueueInspector\Listeners\TrackJobExecution;

class QueueInspectorServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        $this->mergeConfigFrom(
            __DIR__ . '/../config/queue-inspector.php',
            'queue-inspector'
        );


        $this->app->singleton('queue-inspector', function ($app) {
            return new QueueInspectorService(
                $app->make('db')
            );
        });
    }
    public function boot(): void
    {
        // Load  routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Load  views with namespace 'queue-inspector'
        $this->loadViewsFrom(
            __DIR__ . '/../resources/views',
            'queue-inspector'
        );

        // Load  migrations automatically
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publishable assets
        $this->publishes([
            __DIR__ . '/../config/queue-inspector.php'
            => config_path('queue-inspector.php'),
        ], 'queue-inspector-config');

        $this->publishes([
            __DIR__ . '/../resources/views'
            => resource_path('views/vendor/queue-inspector'),
        ], 'queue-inspector-views');

        $this->registerJobListeners();

        $this->checkInstallation();

        
    }

    private function registerJobListeners(): void
    {
        // Get Laravel's event dispatcher from the container
        $events = $this->app['events'];

        $listener = new TrackJobExecution();

        $events->listen(
            JobProcessing::class,
            [$listener, 'handleJobProcessing']
        );

        $events->listen(
            JobProcessed::class,
            [$listener, 'handleJobProcessed']
        );

        $events->listen(
            JobFailed::class,
            [$listener, 'handleJobFailed']
        );
    }

    // -------------------------------------------------------
    // INSTALLATION CHECKER
    // -------------------------------------------------------

    private function checkInstallation(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->checkRequiredTables();
        $this->checkQueueDriver();
        $this->checkMiddleware();
    }

    private function checkRequiredTables(): void
    {
        $requiredTables = [
            config('queue-inspector.jobs_table')
            => "Run 'php artisan queue:table && php artisan migrate'",

            config('queue-inspector.failed_jobs_table')
            => "Run 'php artisan queue:failed-table && php artisan migrate'",

            config('queue-inspector.metrics_table')
            => "Run 'php artisan migrate'",
        ];

        foreach ($requiredTables as $table => $fix) {
            if (! Schema::hasTable($table)) {
                $this->callAfterResolving('events', function () use ($table, $fix) {
                    echo "\n\033[33m[Queue Inspector] WARNING: Table '{$table}' not found.\033[0m\n";
                    echo "\033[33m→ {$fix}\033[0m\n\n";
                });
            }
        }
    }

    private function checkQueueDriver(): void
    {
        $driver = config('queue.default');

        if ($driver !== 'database') {
            $this->callAfterResolving('events', function () use ($driver) {
                echo "\n\033[33m[Queue Inspector] WARNING: Queue driver is '{$driver}' not 'database'.\033[0m\n";
                echo "\033[33m→ Set QUEUE_CONNECTION=database in your .env file.\033[0m\n\n";
            });
        }
    }

    private function checkMiddleware(): void
    {
        $middleware = config('queue-inspector.middleware', []);

        $hasAuth = collect($middleware)->contains(function ($m) {
            return str_starts_with($m, 'auth');
        });

        // Warning 1: No auth at all — dashboard is public
        if (! $hasAuth) {
            fwrite(
                STDERR,
                "\n\033[41m\033[37m [Queue Inspector] SECURITY WARNING \033[0m\n" .
                    "\033[33m→ The dashboard is publicly accessible!\033[0m\n" .
                    "\033[33m→ No 'auth' middleware found in queue-inspector config.\033[0m\n" .
                    "\033[33m→ Fix: Add 'auth' to config/queue-inspector.php:\033[0m\n" .
                    "\033[32m  'middleware' => ['web', 'auth'],\033[0m\n\n"
            );
            return;
        }

        // Warning 2: Auth is set but login route doesn't exist in the app
        $loginRouteExists = collect(\Illuminate\Support\Facades\Route::getRoutes())
            ->contains(function ($route) {
                return $route->getName() === 'login';
            });

        if (! $loginRouteExists) {
            fwrite(
                STDERR,
                "\n\033[41m\033[37m [Queue Inspector] AUTH ROUTE WARNING \033[0m\n" .
                    "\033[33m→ You have 'auth' middleware in queue-inspector config.\033[0m\n" .
                    "\033[33m→ BUT your app has no 'login' route!\033[0m\n" .
                    "\033[33m→ Visiting the dashboard will cause a RouteNotFoundException.\033[0m\n\n" .
                    "\033[33m  You have two options:\033[0m\n\n" .
                    "\033[32m  Option A: Set up Laravel auth in your app:\033[0m\n" .
                    "\033[37m    composer require laravel/breeze --dev\033[0m\n" .
                    "\033[37m    php artisan breeze:install\033[0m\n" .
                    "\033[37m    php artisan migrate\033[0m\n\n" .
                    "\033[32m  Option B: Remove auth for now (NOT for production):\033[0m\n" .
                    "\033[37m    'middleware' => ['web'],  // in config/queue-inspector.php\033[0m\n\n"
            );
        }
    }
}
