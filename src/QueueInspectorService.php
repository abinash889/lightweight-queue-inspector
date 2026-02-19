<?php

namespace AbinashBhatta\QueueInspector;

use Illuminate\Database\DatabaseManager;
use Illuminate\Pagination\LengthAwarePaginator;

class QueueInspectorService
{
    public function __construct(
        private DatabaseManager $db
    ) {}

    // -------------------------------------------------------
    // PENDING JOBS
    // -------------------------------------------------------

    public function getPendingJobs(
        ?string $queue = null,
        ?string $jobClass = null
    ): LengthAwarePaginator {

        $query = $this->db->table(
            config('queue-inspector.jobs_table')
        );

        // Filter by queue name if provided
        if ($queue) {
            $query->where('queue', $queue);
        }

        // Filter by job class if provided
        if ($jobClass) {
            $query->where('payload', 'like', '%' . $jobClass . '%');
        }

        $jobs = $query->orderBy('created_at', 'desc')
            ->paginate(config('queue-inspector.per_page'));

        // Decode the payload for each job so views can display it nicely
        $jobs->through(function ($job) {
            $job->decoded_payload = json_decode($job->payload, true);
            $job->job_class       = $this->extractJobClass($job->decoded_payload);
            return $job;
        });

        return $jobs;
    }

    // -------------------------------------------------------
    // FAILED JOBS
    // -------------------------------------------------------

    public function getFailedJobs(
        ?string $queue = null,
        ?string $jobClass = null
    ): LengthAwarePaginator {

        $query = $this->db->table(
            config('queue-inspector.failed_jobs_table')
        );

        if ($queue) {
            $query->where('queue', $queue);
        }

        if ($jobClass) {
            $query->where('payload', 'like', '%' . $jobClass . '%');
        }

        $jobs = $query->orderBy('failed_at', 'desc')
            ->paginate(config('queue-inspector.per_page'));

        $jobs->through(function ($job) {
            $job->decoded_payload = json_decode($job->payload, true);
            $job->job_class       = $this->extractJobClass($job->decoded_payload);
            return $job;
        });

        return $jobs;
    }

    // -------------------------------------------------------
    // STATS FOR DASHBOARD
    // -------------------------------------------------------

    public function getStats(): array
    {
        $jobsTable       = config('queue-inspector.jobs_table');
        $failedTable     = config('queue-inspector.failed_jobs_table');
        $metricsTable    = config('queue-inspector.metrics_table');

        // Total pending jobs
        $totalPending = $this->db->table($jobsTable)->count();

        // Total failed jobs
        $totalFailed = $this->db->table($failedTable)->count();

        $totalSuccess = $this->db->table($metricsTable)
            ->where('status', 'success')
            ->count();
        // Average execution time from our metrics table
        $avgExecutionTime = $this->db->table($metricsTable)
            ->where('status', 'success')
            ->avg('execution_time_ms');

        // Top failing job — which job class fails the most
        $topFailingJob = $this->db->table($metricsTable)
            ->select('job_class')
            ->selectRaw('COUNT(*) as fail_count')
            ->where('status', 'failed')
            ->groupBy('job_class')
            ->orderByDesc('fail_count')
            ->first();

        // Available queues for the filter dropdown
        $availableQueues = $this->db->table($jobsTable)
            ->select('queue')
            ->distinct()
            ->pluck('queue')
            ->merge(
                $this->db->table($failedTable)
                    ->select('queue')
                    ->distinct()
                    ->pluck('queue')
            )
            ->unique()
            ->values();

        return [
            'total_pending'     => $totalPending,
            'total_failed'      => $totalFailed,
            'total_success'      => $totalSuccess, 
            'avg_execution_time' => $avgExecutionTime
                ? round($avgExecutionTime, 2)
                : null,
            'top_failing_job'   => $topFailingJob,
            'available_queues'  => $availableQueues,
        ];
    }

    // -------------------------------------------------------
    // ACTIONS
    // -------------------------------------------------------

    public function retryJob(int $id): void
    {
        $failedJob = $this->db->table(
            config('queue-inspector.failed_jobs_table')
        )->find($id);

        if (! $failedJob) {
            return;
        }

        // Push the job back onto the jobs table
        $this->db->table(
            config('queue-inspector.jobs_table')
        )->insert([
            'queue'        => $failedJob->queue,
            'payload'      => $failedJob->payload,
            'attempts'     => 0,
            'reserved_at'  => null,
            'available_at' => now()->timestamp,
            'created_at'   => now()->timestamp,
        ]);

        // Remove it from failed jobs
        $this->db->table(
            config('queue-inspector.failed_jobs_table')
        )->delete($id);
    }

    public function deleteFailedJob(int $id): void
    {
        $this->db->table(
            config('queue-inspector.failed_jobs_table')
        )->delete($id);
    }

    public function retryAllJobs(): int
    {
        $failedJobs = $this->db->table(
            config('queue-inspector.failed_jobs_table')
        )->get();

        foreach ($failedJobs as $failedJob) {
            $this->retryJob($failedJob->id);
        }

        return $failedJobs->count();
    }

    public function clearAllFailedJobs(): int
    {
        $count = $this->db->table(
            config('queue-inspector.failed_jobs_table')
        )->count();

        $this->db->table(
            config('queue-inspector.failed_jobs_table')
        )->truncate();

        return $count;
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------

    private function extractJobClass(?array $payload): string
    {
        if (! $payload) {
            return 'Unknown';
        }

        // Laravel stores the job class in payload['displayName']
        // e.g. "App\Jobs\SendEmailJob"
        if (isset($payload['displayName'])) {
            // Get just the class name without the full namespace
            // App\Jobs\SendEmailJob → SendEmailJob
            $parts = explode('\\', $payload['displayName']);
            return end($parts);
        }

        return 'Unknown';
    }

    // -------------------------------------------------------
    // SUCCESS JOBS
    // -------------------------------------------------------

    public function getSuccessJobs(
        ?string $queue    = null,
        ?string $jobClass = null
    ): LengthAwarePaginator {

        $query = $this->db->table(
            config('queue-inspector.metrics_table')
        )->where('status', 'success');

        if ($queue) {
            $query->where('queue', $queue);
        }

        if ($jobClass) {
            $query->where('job_class', 'like', '%' . $jobClass . '%');
        }

        return $query->orderBy('finished_at', 'desc')
            ->paginate(config('queue-inspector.per_page'));
    }
}
