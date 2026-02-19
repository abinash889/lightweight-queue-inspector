<?php

namespace AbinashBhatta\QueueInspector\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Pagination\LengthAwarePaginator getPendingJobs(?string $queue = null, ?string $jobClass = null)
 * @method static \Illuminate\Pagination\LengthAwarePaginator getFailedJobs(?string $queue = null, ?string $jobClass = null)
 * @method static \Illuminate\Pagination\LengthAwarePaginator getSuccessJobs(?string $queue = null, ?string $jobClass = null)
 * @method static array getStats()
 * @method static void retryJob(int $id)
 * @method static void deleteFailedJob(int $id)
 * @method static int retryAllJobs()
 * @method static int clearAllFailedJobs()
 *
 * @see \AbinashBhatta\QueueInspector\QueueInspectorService
 */
class QueueInspector extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'queue-inspector';
    }
}