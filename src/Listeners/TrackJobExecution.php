<?php

namespace AbinashBhatta\QueueInspector\Listeners;

use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\DB;

class TrackJobExecution
{
    /**
     * Called when a job is ABOUT TO START.
     * We record the start time and memory.
     */
    public function handleJobProcessing(JobProcessing $event): void
    {
        $payload = $event->job->payload();

        DB::table(config('queue-inspector.metrics_table'))->insert([
            'job_uuid'         => $payload['uuid'] ?? null,
            'job_class'        => $this->extractJobClass($payload),
            'queue'            => $event->job->getQueue(),
            'started_at'       => now(),
            'finished_at'      => null,
            'execution_time_ms'=> null,
            'memory_usage_mb'  => null,
            'status'           => 'processing',
            'exception'        => null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }

    /**
     * Called when a job FINISHES SUCCESSFULLY.
     * We update the record with finish time and execution duration.
     */
    public function handleJobProcessed(JobProcessed $event): void
    {
        $payload = $event->job->payload();
        $uuid    = $payload['uuid'] ?? null;

        // Find the processing record we created in handleJobProcessing
        $metric = DB::table(config('queue-inspector.metrics_table'))
            ->where('job_uuid', $uuid)
            ->where('status', 'processing')
            ->orderByDesc('id')
            ->first();

        if (! $metric) {
            return;
        }

        // Calculate how long the job took in milliseconds
        $startedAt     = \Carbon\Carbon::parse($metric->started_at);
        $finishedAt    = now();
        $executionTime = $startedAt->diffInMilliseconds($finishedAt);

        // Get peak memory usage in megabytes
        $memoryUsage = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

        DB::table(config('queue-inspector.metrics_table'))
            ->where('id', $metric->id)
            ->update([
                'finished_at'       => $finishedAt,
                'execution_time_ms' => $executionTime,
                'memory_usage_mb'   => $memoryUsage,
                'status'            => 'success',
                'updated_at'        => now(),
            ]);
    }

    /**
     * Called when a job FAILS.
     * We update the record with the exception details.
     */
    public function handleJobFailed(JobFailed $event): void
    {
        $payload = $event->job->payload();
        $uuid    = $payload['uuid'] ?? null;

        $metric = DB::table(config('queue-inspector.metrics_table'))
            ->where('job_uuid', $uuid)
            ->where('status', 'processing')
            ->orderByDesc('id')
            ->first();

        $finishedAt = now();

        if ($metric) {
            // Update existing processing record
            $startedAt     = \Carbon\Carbon::parse($metric->started_at);
            $executionTime = $startedAt->diffInMilliseconds($finishedAt);
            $memoryUsage   = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

            DB::table(config('queue-inspector.metrics_table'))
                ->where('id', $metric->id)
                ->update([
                    'finished_at'       => $finishedAt,
                    'execution_time_ms' => $executionTime,
                    'memory_usage_mb'   => $memoryUsage,
                    'status'            => 'failed',
                    'exception'         => $event->exception->getMessage(),
                    'updated_at'        => now(),
                ]);
        } else {
            DB::table(config('queue-inspector.metrics_table'))->insert([
                'job_uuid'          => $uuid,
                'job_class'         => $this->extractJobClass($payload),
                'queue'             => $event->job->getQueue(),
                'started_at'        => null,
                'finished_at'       => $finishedAt,
                'execution_time_ms' => null,
                'memory_usage_mb'   => null,
                'status'            => 'failed',
                'exception'         => $event->exception->getMessage(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    // -------------------------------------------------------
    // HELPER
    // -------------------------------------------------------

    private function extractJobClass(array $payload): string
    {
        if (isset($payload['displayName'])) {
            $parts = explode('\\', $payload['displayName']);
            return end($parts);
        }

        return 'Unknown';
    }
}