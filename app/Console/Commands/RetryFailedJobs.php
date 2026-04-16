<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class RetryFailedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:retry-failed
                          {--hours= : Retry jobs that failed within the last n hours}
                          {--queue= : Retry jobs from a specific queue}
                          {--id= : Retry a specific job by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry failed jobs with various filtering options';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $hours = $this->option('hours');
        $queue = $this->option('queue');
        $id = $this->option('id');

        try {
            $query = DB::table('failed_jobs');

            // Filter by specific ID if provided
            if ($id) {
                $query->where('id', $id);
            }

            // Filter by time range if hours provided
            if ($hours) {
                $query->where('failed_at', '>=', Carbon::now()->subHours($hours));
            }

            // Filter by queue if provided
            if ($queue) {
                $query->where('queue', $queue);
            }

            $failedJobs = $query->get();

            if ($failedJobs->isEmpty()) {
                $this->info('No failed jobs found matching the criteria.');
                return Command::SUCCESS;
            }

            $retried = 0;
            foreach ($failedJobs as $job) {
                try {
                    // Use the built-in queue:retry command
                    Artisan::call('queue:retry', ['id' => $job->id]);
                    $retried++;
                    $this->info("Retried job ID: {$job->id}");
                } catch (\Exception $e) {
                    $this->error("Failed to retry job ID {$job->id}: {$e->getMessage()}");
                }
            }

            $this->info("Successfully retried {$retried} jobs.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("An error occurred: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
