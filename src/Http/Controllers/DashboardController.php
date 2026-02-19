<?php

namespace AbinashBhatta\QueueInspector\Http\Controllers;

use Illuminate\Routing\Controller;
use AbinashBhatta\QueueInspector\QueueInspectorService;

class DashboardController extends Controller
{
    public function __construct(
        private QueueInspectorService $service
    ) {}

    public function index()
    {
        $stats = $this->service->getStats();

        return view('queue-inspector::dashboard', compact('stats'));
    }

    private function showAuthSetupPage()
    {
        return response(
            view('queue-inspector::auth-setup'),
            503
        );
    }
}
