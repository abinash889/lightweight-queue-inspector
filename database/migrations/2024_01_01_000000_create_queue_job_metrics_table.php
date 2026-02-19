<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_job_metrics', function (Blueprint $table) {
            $table->id();

            $table->string('job_uuid')->nullable()->index();
            $table->string('job_class');
            $table->string('queue')->default('default');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('execution_time_ms')->nullable();
            $table->decimal('memory_usage_mb', 8, 2)->nullable();
            $table->enum('status', ['processing', 'success', 'failed'])->default('processing');
            $table->text('exception')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_job_metrics');
    }
};