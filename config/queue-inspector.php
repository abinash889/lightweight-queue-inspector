<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dashboard Path
    |--------------------------------------------------------------------------
    | The URL where the dashboard will be accessible.
    | Default: http://yourapp.com/queue-inspector
    |
    */
    'path' => env('QUEUE_INSPECTOR_PATH', 'queue-inspector'),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    | Protect the dashboard. 'auth' means the user must be logged in.
    | You can add more middleware here like 'auth:sanctum' or custom ones.
    |
    */
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Jobs Table
    |--------------------------------------------------------------------------
    | The table that stores pending queue jobs.
    | Only change this if you renamed Laravel's default 'jobs' table.
    |
    */
    'jobs_table' => env('QUEUE_JOBS_TABLE', 'jobs'),

    /*
    |--------------------------------------------------------------------------
    | Failed Jobs Table
    |--------------------------------------------------------------------------
    | The table that stores failed queue jobs.
    | Only change this if you renamed Laravel's default 'failed_jobs' table.
    |
    */
    'failed_jobs_table' => env('QUEUE_FAILED_JOBS_TABLE', 'failed_jobs'),

    /*
    |--------------------------------------------------------------------------
    | Metrics Table
    |--------------------------------------------------------------------------
    | The table our package creates to store execution tracking data.
    |
    */
    'metrics_table' => 'queue_job_metrics',

    /*
    |--------------------------------------------------------------------------
    | Per Page
    |--------------------------------------------------------------------------
    | How many jobs to show per page on the dashboard.
    |
    */
    'per_page' => 20,

];