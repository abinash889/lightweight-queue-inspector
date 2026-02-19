<?php

namespace AbinashBhatta\QueueInspector\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use AbinashBhatta\QueueInspector\QueueInspectorService;

class JobController extends Controller
{
    public function __construct(
        private QueueInspectorService $service
    ) {}

    public function pending(Request $request)
    {
        $jobs = $this->service->getPendingJobs(
            queue: $request->get('queue'),
            jobClass: $request->get('job_class')
        );

        return view('queue-inspector::pending', compact('jobs'));
    }

    public function failed(Request $request)
    {
        $jobs = $this->service->getFailedJobs(
            queue: $request->get('queue'),
            jobClass: $request->get('job_class')
        );

        return view('queue-inspector::failed', compact('jobs'));
    }

    public function retry($id)
    {
        $this->service->retryJob($id);

        return redirect()
            ->route('queue-inspector.jobs.failed')
            ->with('success', 'Job has been queued for retry.');
    }

    public function destroy($id)
    {
        $this->service->deleteFailedJob($id);

        return redirect()
            ->route('queue-inspector.jobs.failed')
            ->with('success', 'Job deleted successfully.');
    }

    public function retryAll()
    {
        $count = $this->service->retryAllJobs();

        return redirect()
            ->route('queue-inspector.jobs.failed')
            ->with('success', "{$count} jobs have been queued for retry.");
    }

    public function clearAll()
    {
        $count = $this->service->clearAllFailedJobs();

        return redirect()
            ->route('queue-inspector.jobs.failed')
            ->with('success', "{$count} jobs have been deleted.");
    }

    public function success(Request $request)
    {
        $jobs = $this->service->getSuccessJobs(
            queue: $request->get('queue'),
            jobClass: $request->get('job_class')
        );

        return view('queue-inspector::success', compact('jobs'));
    }
}
