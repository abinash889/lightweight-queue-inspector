# ⚡ Lightweight Queue Inspector

A lightweight debugging dashboard for Laravel applications using the **database queue driver**.
No Redis. No Horizon. Just your existing `jobs` and `failed_jobs` tables.

![Laravel](https://img.shields.io/badge/Laravel-10%20%7C%2011-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue?style=flat-square&logo=php)
![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)

---

## What It Does

| Page | Description |
|---|---|
| `/queue-inspector` | Dashboard with summary stats |
| `/queue-inspector/pending` | All pending jobs with collapsible payload viewer |
| `/queue-inspector/success` | Successfully completed jobs with execution time + memory |
| `/queue-inspector/failed` | Failed jobs with exception messages, stack traces, retry + delete |

---

## Features

- **Pending Jobs** — view all jobs waiting in the queue with ID, class name, queue, attempts, timestamps and raw payload
- **Failed Jobs** — view exception messages, full stack traces, retry or delete individual jobs, bulk retry all, clear all
- **Success Jobs** — track every completed job with execution time (color coded: green/orange/red) and memory usage
- **Execution Tracking** — automatically records job metrics via Laravel queue events. No extra setup needed
- **Dashboard Stats** — pending count, failed count, success count, average execution time, top failing job, active queues
- **Filters** — filter any page by queue name or job class
- **Installation Warnings** — terminal warnings if required tables are missing, wrong queue driver, or auth not configured
- **Security Guard** — friendly browser page if auth middleware is configured but no login route exists yet

---

## Requirements

- PHP 8.1 or higher
- Laravel 10 or Laravel 11
- Queue connection set to `database`

---

## Installation

### Step 1 — Install via Composer

```bash
composer require abinashbhatta/lightweight-queue-inspector
```

### Step 2 — Make sure required tables exist

If you don't have the `jobs` table:

```bash
php artisan queue:table
php artisan migrate
```

If you don't have the `failed_jobs` table:

```bash
php artisan queue:failed-table
php artisan migrate
```

### Step 3 — Run the package migration

This creates the `queue_job_metrics` table used for execution tracking:

```bash
php artisan migrate
```

### Step 4 — Set queue driver to database

In your `.env` file:

```env
QUEUE_CONNECTION=database
```

### Step 5 — Publish the config (optional but recommended)

```bash
php artisan vendor:publish --tag=queue-inspector-config
```

### Step 6 — Visit the dashboard

```
http://yourapp.com/queue-inspector
```

That's it. The package auto-discovers via Laravel's package discovery — no need to add anything to `config/app.php`.

---

## Configuration

After publishing, open `config/queue-inspector.php`:

```php
return [

    // The URL prefix for the dashboard
    // Change this to move the dashboard to a different URL
    'path' => env('QUEUE_INSPECTOR_PATH', 'queue-inspector'),

    // Middleware applied to ALL dashboard routes
    // ⚠ Always keep 'auth' here in production!
    'middleware' => ['web', 'auth'],

    // Your jobs table name — only change if you renamed it
    'jobs_table' => env('QUEUE_JOBS_TABLE', 'jobs'),

    // Your failed jobs table name — only change if you renamed it
    'failed_jobs_table' => env('QUEUE_FAILED_JOBS_TABLE', 'failed_jobs'),

    // The metrics table created by this package — do not rename
    'metrics_table' => 'queue_job_metrics',

    // How many jobs to show per page
    'per_page' => 20,

];
```

---

## Security

> ⚠ **IMPORTANT**: The dashboard exposes sensitive job data — payloads, exception messages,
> stack traces, and allows retrying or deleting jobs.
> **Always protect it with authentication in production.**

### The package will warn you in the terminal if auth is missing

Every time you run any `php artisan` command, you will see:

```
[Queue Inspector] SECURITY WARNING
→ The dashboard is publicly accessible!
→ No 'auth' middleware found in queue-inspector config.
→ Fix: Add 'auth' to config/queue-inspector.php:
  'middleware' => ['web', 'auth'],
```

### The package will warn you if auth is set but no login route exists

If you have `'auth'` in the config but your app has no login system installed:

**Terminal warning:**
```
[Queue Inspector] AUTH ROUTE WARNING
→ You have 'auth' middleware in queue-inspector config.
→ BUT your app has no 'login' route!
→ Visiting the dashboard will cause a RouteNotFoundException.

  Option A: Set up Laravel auth in your app:
    composer require laravel/breeze --dev
    php artisan breeze:install
    php artisan migrate

  Option B: Remove auth for now (NOT for production):
    'middleware' => ['web'],  // in config/queue-inspector.php
```

**Browser page:** Instead of a confusing error, the dashboard shows a friendly setup guide page with both options explained clearly.

### Recommended middleware setups

```php
// Basic — any logged in user can access
'middleware' => ['web', 'auth'],

// Specific guard
'middleware' => ['web', 'auth:sanctum'],

// Admin only (recommended for production teams)
'middleware' => ['web', 'auth', App\Http\Middleware\EnsureUserIsAdmin::class],
```

---

## Execution Tracking

The package automatically listens to Laravel's built-in queue events. No configuration needed.

| Event | What gets recorded |
|---|---|
| `JobProcessing` | job class, queue name, started\_at timestamp |
| `JobProcessed` | finished\_at, execution\_time\_ms, memory\_usage\_mb, status = success |
| `JobFailed` | finished\_at, execution\_time\_ms, status = failed, exception message |

> **Note:** These events only fire when jobs are processed by `php artisan queue:work`.
> They do not fire for jobs sitting in the pending queue.

### Execution time color coding on the Success Jobs page

| Color | Time | Meaning |
|---|---|---|
| 🟢 Green | Under 500ms | Fast — no action needed |
| 🟠 Orange | 500ms – 2000ms | Acceptable — keep an eye on it |
| 🔴 Red | Over 2000ms | Slow — consider optimising |

---

## Installation Warnings Reference

The package checks your setup on every artisan command and warns you clearly:

| Warning | Cause | Fix |
|---|---|---|
| Table 'jobs' not found | jobs table missing | `php artisan queue:table && php artisan migrate` |
| Table 'failed_jobs' not found | failed_jobs table missing | `php artisan queue:failed-table && php artisan migrate` |
| Table 'queue_job_metrics' not found | package migration not run | `php artisan migrate` |
| Queue driver is not 'database' | wrong driver in .env | Set `QUEUE_CONNECTION=database` |
| Dashboard publicly accessible | no auth in middleware config | Add `'auth'` to middleware |
| Auth set but no login route | auth on but no login system | Install Breeze or remove auth |

---

## Facade Usage

Use the `QueueInspector` facade anywhere in your application:

```php
use AbinashBhatta\QueueInspector\Facades\QueueInspector;

// Get dashboard stats
$stats = QueueInspector::getStats();
// Returns: total_pending, total_failed, total_success,
//          avg_execution_time, top_failing_job, available_queues

// Get pending jobs (optional filters)
$jobs = QueueInspector::getPendingJobs();
$jobs = QueueInspector::getPendingJobs(queue: 'emails');
$jobs = QueueInspector::getPendingJobs(jobClass: 'SendEmailJob');

// Get failed jobs
$jobs = QueueInspector::getFailedJobs();
$jobs = QueueInspector::getFailedJobs(queue: 'payments');

// Get successful jobs
$jobs = QueueInspector::getSuccessJobs();

// Actions
QueueInspector::retryJob($id);           // Retry one failed job
QueueInspector::deleteFailedJob($id);    // Delete one failed job
$count = QueueInspector::retryAllJobs();        // Retry all failed jobs
$count = QueueInspector::clearAllFailedJobs();  // Delete all failed jobs
```

---

## Customising Views

Publish the views to customise the dashboard UI:

```bash
php artisan vendor:publish --tag=queue-inspector-views
```

This copies all Blade files to `resources/views/vendor/queue-inspector/` in your app.
Laravel automatically loads your customised versions instead of the package defaults.

---

## Changelog

### v1.0.0 — Initial Release
- Dashboard with stats summary
- Pending jobs list with payload viewer
- Failed jobs list with retry and delete actions
- Success jobs list with execution metrics
- Automatic execution tracking via queue events
- Installation warnings system
- Auth misconfiguration detection with friendly browser error page
- Facade support
- Configurable path, middleware, and table names

---

## License

MIT — free to use in personal and commercial projects.

---

## Author

**Abinash Bhatta**

Built with ❤️ for the Laravel community.